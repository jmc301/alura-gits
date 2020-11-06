<?php

namespace App;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auditoria extends Model
{
    use SoftDeletes;
    
    protected $hidden = ['tipo_alerta_id', 'user_id', 'transportadora_id', 'gerenciadora_risco_id', 'cargo_id', 'operador_id', 'tecnologia_id'];
    protected $appends = ['hash_id', 'hash_tipo_alerta_id', 'hash_user_id', 'hash_transportadora_id', 'hash_gerenciadora_risco_id', 'hash_cargo_id', 'hash_operador_id', 'hash_tecnologia_id', 'hash_solicitacao_id'];
    protected $dates = ['dt_identificacao', 'dt_ciencia'];
    protected $fillable = [
        'protocolo',
        'sm',
        'solicitacao_id',
        'tipo_alerta_id',
        'user_id',
        'transportadora_id',
        'gerenciadora_risco_id',
        'cargo_id',
        'operador_id',
        'tecnologia_id',
        'dt_identificacao',
        'gr_ciente',
        'quem',
        'dt_ciencia',
        'intervalo_identificacao',
        'resolvido',
        'acao',
        'placa',
        'motorista',
        'cpf',
        'url_evidencia1',
        'url_evidencia2'
    ];
    
    public function getHashIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public function getHashSolicitacaoIdAttribute()
    {
        return Hashids::encode($this->solicitacao_id);
    }

    public function getHashTipoAlertaIdAttribute()
    {
        return Hashids::encode($this->tipo_alerta_id);
    }

    public function getHashUserIdAttribute()
    {
        return Hashids::encode($this->user_id);
    }

    public function getHashTransportadoraIdAttribute()
    {
        return Hashids::encode($this->transportadora_id);
    }

    public function getHashGerenciadoraRiscoIdAttribute()
    {
        return Hashids::encode($this->gerenciadora_risco_id);
    }

    public function getHashCargoIdAttribute()
    {
        return Hashids::encode($this->cargo_id);
    }

    public function getHashOperadorIdAttribute()
    {
        return Hashids::encode($this->operador_id);
    }

    public function getHashTecnologiaIdAttribute()
    {
        return Hashids::encode($this->tecnologia_id);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipo_alerta()
    {
        return $this->belongsTo(TipoAlerta::class);
    }

    public function transportadora()
    {
        return $this->belongsTo(Transportadora::class);
    }

    public function gerenciadora_risco()
    {
        return $this->belongsTo(GerenciadoraRisco::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function operador()
    {
        return $this->belongsTo(Operador::class);
    }

    public function solicitacao()
    {
        return $this->belongsTo(Solicitacao::class,'solicitacao_id');
    }

    public function tecnologia()
    {
        return $this->belongsTo(Tecnologia::class);
    }

    public function auditoria_local()
    {
        return $this->hasOne(AuditoriaLocal::class);
    }
}
