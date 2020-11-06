<?php

namespace App\Http\Controllers;

use App\Auditoria;
use Carbon\Carbon;
use App\Solicitacao;
use App\AuditoriaLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AuditCenterRequest;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic;
use PhpParser\Node\Stmt\TryCatch;

class AuditCenterController extends Controller
{
    public function index()
    {
        $registros = Auditoria::select('id', 'sm', 'protocolo', 'dt_identificacao', 'user_id', 'tipo_alerta_id', 'transportadora_id', 'placa', 'gerenciadora_risco_id')
            ->with('user:id,nome', 'tipo_alerta:id,descricao,cor', 'transportadora:id,empresa_id,nome', 'transportadora.empresa:id,nome', 'gerenciadora_risco:id,nome')
            ->whereDate('dt_identificacao', now())
            ->get();
        return view('auditcenter.index', compact('registros'));
    }

    public function filter(Request $request)
    {
        $filtro = $request->all();
        $registros = Auditoria::select('id', 'sm', 'protocolo', 'dt_identificacao', 'user_id', 'tipo_alerta_id', 'transportadora_id', 'placa', 'gerenciadora_risco_id')
            ->with('user:id,nome', 'tipo_alerta:id,descricao,cor', 'transportadora:id,empresa_id,nome', 'transportadora.empresa:id,nome', 'gerenciadora_risco:id,nome')
            ->when(request('de'), function ($query) use ($filtro) {
                $query->whereDate('dt_identificacao', '>=', $filtro['de']);
            })->when(request('ate'), function ($query) use ($filtro) {
                $query->whereDate('dt_identificacao', '<=', $filtro['ate']);
            })->when(request('protocolo'), function ($query) use ($filtro) {
                $query->whereProtocolo($filtro['protocolo']);
            })->when(request('placa'), function ($query) use ($filtro) {
                $query->wherePlaca($filtro['placa']);
            })->get();
        return view('auditcenter.index', compact('registros', 'filtro'));
    }

    public function create()
    {
        return view('auditcenter.create');
    }

    public function edit($auditcenter)
    {
        $registro = Auditoria::with(
            'transportadora.empresa',
            'auditoria_local:auditoria_id,municipio_id,local,latitude,longitude',
            'auditoria_local.municipio:id,nome,estado_id',
            'auditoria_local.municipio.estado:id,nome,sigla'

        )->find($auditcenter);
        return view('auditcenter.edit', compact('registro'));
    }

    public function store(AuditCenterRequest $request)
    {
        $post = $request->all();
        $post['user_id'] = Auth::user()->id;
        $post['transportadora_id'] = Hashids::decode($post['transportadora']['hash_id'])[0];
        $post['tipo_alerta_id'] = Hashids::decode($post['tipo_alerta']['hash_id'])[0];
        $post['gerenciadora_risco_id'] = Hashids::decode($post['gerenciadora_risco']['hash_id'])[0];
        $post['cargo_id'] = Hashids::decode($post['cargo']['hash_id'])[0];
        $post['operador_id'] = Hashids::decode($post['operador']['hash_id'])[0];
        $post['tecnologia_id'] = Hashids::decode($post['tecnologia']['hash_id'])[0];
        $post['dt_identificacao'] = Carbon::parse($post['data_ident'] . " " . $post['hora_ident']);

        $post['solicitacao_id'] = !empty($post['solicitacao_id']) ? Hashids::decode($post['solicitacao_id'])[0] : null;

        if (!empty($post['data_ciencia']) && !empty($post['hora_ciencia'])) {
            $post['dt_ciencia'] = Carbon::parse($post['data_ciencia'] . " " . $post['hora_ciencia']);
            $post['intervalo_identificacao'] = $post['dt_identificacao']->diffInHours($post['dt_ciencia']) . ':' . $post['dt_identificacao']->diff($post['dt_ciencia'])->format('%I:%S');
        }

        $auditoria = Auditoria::create($post);

        if (isset($post['local'])) {
            AuditoriaLocal::create([
                'auditoria_id' => $auditoria->id,
                'municipio_id' => $post['municipio']['id'],
                'local' => $post['local'],
                'latitude' => $post['latitude'],
                'longitude' => $post['longitude']
            ]);
        }

        $auditoria->protocolo = date('Y') . '-' . substr(strrev(time()), 0, 4) . substr(rand(0, time()), 0, 3);
        
       
        if ($request->hasFile('evidencia1')) {
            try {
                $auditoria->url_evidencia1 = $this->imgUpload(1, $request->file('evidencia1'), $auditoria->id);
            } catch (Exception $e) {
                return response()->json(['url_evidencia1' => 'error', 'erroEvidencia1' => "Evidência 1, Imagem e/ou formato invalido, envie PNG, JPG, GIF"]);
            }
        }

        if ($request->hasFile('evidencia2')) {
            try {
                $auditoria->url_evidencia2 = $this->imgUpload(2, $request->file('evidencia2'), $auditoria->id);
            } catch (Exception $e) {
                return response()->json(['url_evidencia2' => 'error', 'erroEvidencia2' => "Evidência 2, Imagem e/ou formato invalido, envie PNG, JPG, GIF"]);
            }
        }
        
        $auditoria->save();

        $solicitacao = Solicitacao::find($post['solicitacao_id']);
        if ($solicitacao = Solicitacao::whereSm($auditoria->sm)->first()) {
            $solicitacao->notifications++;
            $solicitacao->save();
        }
        Log::info('User: ' . '{ "id": "' . Auth::id() . '", "nome": "' . Auth::user()->nome . '", "email": "' . Auth::user()->email . '" } => Created Auditoria: ' . $auditoria);

        return response()->json($auditoria);
    }

    public function update(AuditCenterRequest $request)
    {

        $post = $request->all();
        $post['id'] = Hashids::decode($post['id'])[0];
        $post['transportadora_id'] = Hashids::decode($post['transportadora']['hash_id'])[0];
        $post['tipo_alerta_id'] = Hashids::decode($post['tipo_alerta']['hash_id'])[0];
        $post['gerenciadora_risco_id'] = Hashids::decode($post['gerenciadora_risco']['hash_id'])[0];
        $post['cargo_id'] = Hashids::decode($post['cargo']['hash_id'])[0];
        $post['operador_id'] = Hashids::decode($post['operador']['hash_id'])[0];
        $post['tecnologia_id'] = Hashids::decode($post['tecnologia']['hash_id'])[0];
        $post['dt_identificacao'] = Carbon::parse($post['data_ident'] . " " . $post['hora_ident']);

        $post['solicitacao_id'] = !empty($post['solicitacao_id']) ? Hashids::decode($post['solicitacao_id'])[0] : null;

        if (!empty($post['data_ciencia']) && !empty($post['hora_ciencia'])) {
            $post['dt_ciencia'] = Carbon::parse($post['data_ciencia'] . " " . $post['hora_ciencia']);
            $post['intervalo_identificacao'] = $post['dt_identificacao']->diffInHours($post['dt_ciencia']) . ':' . $post['dt_identificacao']->diff($post['dt_ciencia'])->format('%I:%S');
        }

        $auditoria = Auditoria::find($post['id']);

        if (isset($post['local'])) {
            AuditoriaLocal::updateOrCreate(
                ['auditoria_id' => $auditoria->id],
                [
                    'municipio_id' => $post['municipio']['id'],
                    'local' => $post['local'],
                    'latitude' => $post['latitude'],
                    'longitude' => $post['longitude']
                ]
            );
        } else {
            $old = AuditoriaLocal::whereAuditoriaId($auditoria->id)->first();
            if ($old) {
                $old->delete();
            }
        }


        if ($request->hasFile('evidencia1')) {           
            try {
                $post['url_evidencia1'] = $this->imgUpload(1, $request->file('evidencia1'), $auditoria->id);
                if (!empty($auditoria->url_evidencia1)) {
                    $this->imgDelete($auditoria->id, 'url_evidencia1');
                }
    
            } catch (Exception $e) {
                return response()->json(['url_evidencia1' => 'error', 'erroEvidencia1' => "Evidência 1, Imagem e/ou formato invalido, envie PNG, JPG, GIF"]);
            }
        }

        if ($request->hasFile('evidencia2')) {          
            try {
                $post['url_evidencia2'] = $this->imgUpload(1, $request->file('evidencia2'), $auditoria->id);
                if (!empty($auditoria->url_evidencia2)) {
                    $this->imgDelete($auditoria->id, 'url_evidencia2');
                }
            } catch (Exception $e) {
                return response()->json(['url_evidencia2' => 'error', 'erroEvidencia2' => "Evidência 2, Imagem e/ou formato invalido, envie PNG, JPG, GIF"]);
            }
        }


        $auditoria->update($post);

        Log::info('User: ' . '{ "id": "' . Auth::id() . '", "nome": "' . Auth::user()->nome . '", "email": "' . Auth::user()->email . '" } => Updated Auditoria: ' . $auditoria);
        return response()->json($auditoria);
    }

    public function imgUpload($x, $file, $id)
    {
        $fileName = $id . '-' . $x . time() . rand(10, 10) . '.' . $file->extension();
        $imageResize = ImageManagerStatic::make($file->path());
        $imageResize->resize(1024, 768, function ($constraint) {
            $constraint->aspectRatio();
        });
        $imageResize->save(public_path('img/' . $fileName));
        $filePath = 'fotos_evi/' . $id . '/' . $fileName;

        Storage::disk('s3')->put($filePath, fopen(public_path('img/' . $fileName), 'r'), 'public');
        unlink(public_path('img/' . $fileName));
        Log::info('User: ' . '{ "id": "' . Auth::id() . '", "nome": "' . Auth::user()->nome . '", "email": "' . Auth::user()->email . '" } => Upload image: ' . $fileName . ' to ' . $id);
        return $fileName;
    }
    public function deleteFoto(Request $request)
    {
        $request->id = Hashids::decode($request->id)[0];
        try {
            $this->imgDelete($request->id, $request->field);
            return response()->json(['status_code' => 200]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function imgDelete($auditcenter, $field)
    {
        $foto = Auditoria::find($auditcenter);
        Storage::disk('s3')->delete('fotos_evi/' . $foto->id . '/' . $foto->{$field});
        $foto->{$field} = null;
        $foto->save();
        Log::info('User: ' . '{ "id": "' . Auth::id() . '", "nome": "' . Auth::user()->nome . '", "email": "' . Auth::user()->email . '" } => Deleted image: ' . $foto->{$field} . ' from ' . $foto);
    }

    public function sendMail(Request $request)
    {
        $registro = Auditoria::select(
            'id',
            'placa',
            'sm',
            'dt_identificacao',
            'quem',
            'acao',
            'dt_ciencia',
            'url_evidencia1',
            'url_evidencia2',
            'protocolo',
            'transportadora_id',
            'tipo_alerta_id',
            'gerenciadora_risco_id',
            'tecnologia_id',
            'operador_id',
            'cargo_id',
            'user_id'
        )->with(
            'transportadora:id,empresa_id,nome,email',
            'transportadora.empresa:id,nome,email',
            'tipo_alerta:id,descricao',
            'gerenciadora_risco:id,nome',
            'tecnologia:id,descricao',
            'operador:id,nome',
            'cargo:id,descricao',
            'user:id,nome',
            'auditoria_local:auditoria_id,municipio_id,local',
            'auditoria_local.municipio:id,nome,estado_id',
            'auditoria_local.municipio.estado:id,sigla'
        )->find(Hashids::decode($request->id)[0])->toArray();

        $emails = array();
        $emails = explode(';', preg_replace('/\s+/', '', $registro['transportadora']['empresa']['email']));
        array_push($emails, 'auditoria.sservice@moraesvelleda.com.br');

        try {
            foreach ($emails as $mail) {
                Mail::send('auditcenter.mail', $registro, function ($message) use ($registro, $mail) {
                    $message->to($mail, 'Moraes Velleda')->subject('Protocolo: ' . $registro['protocolo'] . ' - Placa: ' . $registro['placa'] . ' - Transportadora: ' . $registro['transportadora']['nome'] . ' - Embarcador: ' . $registro['transportadora']['empresa']['nome'] . ' - Tipo Ocorrência: ' . $registro['tipo_alerta']['descricao']);
                });
            }
            Log::info('User: ' . '{ "id": "' . Auth::id() . '", "nome": "' . Auth::user()->nome . '", "email": "' . Auth::user()->email . '" } => Sent mail from Auditoria: "' . $registro['protocolo'] . '"');
            return response()->json(['status' => 'sucesso']);
        } catch (\Exception $e) {
            $mensagem = $e->getMessage();
            $erro = strpos($mensagem, 'Unsupported image type');
            if ($erro === false) {
                Log::error('User: ' . '{ "id": "' . Auth::id() . '", "nome": "' . Auth::user()->nome . '", "email": "' . Auth::user()->email . '" } => Failed to send mail from Auditoria: "' . $registro['protocolo'] . '" error ' . $mensagem);
                return [
                    'status_code' => 505,
                    'msg' => $mensagem
                ];
            } else {
                $mensagem = "O Tipo ou Formato de arquivo escolhido invalido";
                return [
                    'status_code' => 505,
                    'msg' => $mensagem
                ];
            }
        }
    }
}
