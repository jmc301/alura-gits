<template>
    <div>
        <div class="card card-outline-secondary">
            <div class="card-header pt-2 pb-0 pl-3 pr-0">
                <h6><strong>Dados do Registro</strong></h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="sm">SM:</label>
                            <input
                                :readonly="loading || solicitation"
                                type="text"
                                name="sm"
                                v-model="post.sm"
                                v-validate="'required'"                                
                                :class="['form-control form-control-sm', {'is-invalid': errors.has('sm')}]"
                            >
                            <span v-show="errors.has('sm')" class="invalid-feedback">
                                {{ errors.first('sm') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tipo_alerta">Tipo de Ocorrência:</label>
                            <v-select
                                @input="resetLocal()"
                                :disabled="loading"
                                name="tipo_alerta"
                                placeholder="Selecione..."
                                :options="tiposAlertas"
                                label="descricao"
                                :selectOnTab="true"
                                v-model="post.tipo_alerta"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('tipo_alerta')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('tipo_alerta')" class="v-select-invalid-feedback">
                                {{ errors.first('tipo_alerta') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="empresa">Empresa:</label>
                            <!--  :disabled="loading || solicitation" -->
                            <v-select
                                @input="getTransportadorasGrs()"
                                :disabled="loading"
                                name="empresa"
                                placeholder="Selecione..."
                                :options="empresas"
                                label="nome"
                                :selectOnTab="true"
                                v-model="post.empresa"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('empresa')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('empresa')" class="v-select-invalid-feedback">
                                {{ errors.first('empresa') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="transportadora">Transportadora:</label>
                            <v-select
                                :disabled="loading"
                                name="transportadora"
                                placeholder="Selecione..."
                                :options="transportadoras"
                                label="nome"
                                :selectOnTab="true"
                                :resetOnOptionsChange="true"
                                v-model="post.transportadora"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('transportadora')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('transportadora')" class="v-select-invalid-feedback">
                                {{ errors.first('transportadora') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="data_ident">Data Identificação:</label>
                            <input
                                :readonly="loading"
                                type="date"
                                name="data_ident"
                                v-model="post.data_ident"
                                v-validate="'required'"
                                data-vv-as="data identificação"
                                :class="['form-control form-control-sm', {'is-invalid': errors.has('data_ident')}]"
                            >
                            <span v-show="errors.has('data_ident')" class="invalid-feedback">
                                {{ errors.first('data_ident') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="hora_ident">Hora Identificação:</label>
                            <input
                                :readonly="loading"
                                type="time"
                                name="hora_ident"
                                v-model="post.hora_ident"
                                v-validate="'required'"
                                data-vv-as="data identificação"
                                :class="['form-control form-control-sm', {'is-invalid': errors.has('hora_ident')}]"
                            >
                            <span v-show="errors.has('hora_ident')" class="invalid-feedback">
                                {{ errors.first('hora_ident') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="motorista">Motorista:</label>
                            <input
                                :readonly="loading || solicitation"
                                type="text"
                                name="motorista"
                                v-model="post.motorista"
                                v-validate="'required'"                                
                                :class="['form-control form-control-sm', {'is-invalid': errors.has('motorista')}]"
                            >
                            <span v-show="errors.has('motorista')" class="invalid-feedback">
                                {{ errors.first('motorista') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="cpf">CPF Motorista:</label>
                            <input
                                :readonly="loading || solicitation"
                                type="text"
                                name="cpf"
                                v-model="post.cpf"   
                                v-mask="'###.###.###-##'"
                                v-validate="'required'"                                
                                :class="['form-control form-control-sm', {'is-invalid': errors.has('cpf')}]"
                            >
                            <span v-show="errors.has('cpf')" class="invalid-feedback">
                                {{ errors.first('cpf') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="gerenciadora_risco">GR:</label>
                            <v-select
                                :disabled="loading"
                                name="gerenciadora_risco"
                                placeholder="Selecione..."
                                :options="gerenciadorasRiscos"
                                label="nome"
                                :selectOnTab="true"
                                :resetOnOptionsChange="true"
                                v-model="post.gerenciadora_risco"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('gerenciadora_risco')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('gerenciadora_risco')" class="v-select-invalid-feedback">
                                {{ errors.first('gerenciadora_risco') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="quem">Quem:</label>
                            <input
                                :readonly="loading"
                                type="text"
                                name="quem"
                                v-model="post.quem"                             
                                class="form-control form-control-sm"
                            >
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="data_ciencia">Data Ciência:</label>
                            <input
                                :readonly="loading"
                                type="date"
                                name="data_ciencia"
                                v-model="post.data_ciencia"
                                class="form-control form-control-sm"
                            >
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="hora_ciencia">Hora Ciência:</label>
                            <input
                                :readonly="loading"
                                type="time"
                                name="hora_ciencia"
                                v-model="post.hora_ciencia"
                                class="form-control form-control-sm"
                            >
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="cargo">Cargo:</label>
                            <v-select
                                :disabled="loading"
                                name="cargo"
                                placeholder="Selecione..."
                                :options="cargos"
                                label="descricao"
                                :selectOnTab="true"
                                v-model="post.cargo"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('cargo')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('cargo')" class="v-select-invalid-feedback">
                                {{ errors.first('cargo') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="acao">Ação:</label>
                            <input
                                :readonly="loading"
                                type="text"
                                name="acao"
                                v-model="post.acao"                             
                                class="form-control form-control-sm"
                            >
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="placa">Placa:</label>
                            <input
                                :readonly="loading || solicitation"
                                type="text"
                                name="placa"
                                v-model="post.placa"
                                v-validate="'required'"                                
                                :class="['form-control form-control-sm', {'is-invalid': errors.has('placa')}]"
                            >
                            <span v-show="errors.has('placa')" class="invalid-feedback">
                                {{ errors.first('placa') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="operador">Operador:</label>
                            <v-select
                                :disabled="loading"
                                name="operador"
                                placeholder="Selecione..."
                                :options="operadores"
                                label="nome"
                                :selectOnTab="true"
                                v-model="post.operador"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('operador')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('operador')" class="v-select-invalid-feedback">
                                {{ errors.first('operador') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tecnologia">Tecnologia:</label>
                            <v-select
                                :disabled="loading || solicitacao"
                                name="tecnologia"
                                placeholder="Selecione..."
                                :options="tecnologias"
                                label="descricao"
                                :selectOnTab="true"
                                v-model="post.tecnologia"
                                v-validate="'required'"
                                :class="{'v-select-invalid': errors.has('tecnologia')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('tecnologia')" class="v-select-invalid-feedback">
                                {{ errors.first('tecnologia') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="post.tipo_alerta && post.tipo_alerta.posicao">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>UF:</label>
                            <v-select
                                :disabled="loading"
                                name="uf"
                                placeholder="Selecione..."
                                :options="ufs"
                                label="sigla"
                                v-model="post.uf"
                                v-validate="{ required: post.tipo_alerta && post.tipo_alerta.posicao }"
                                data-vv-as="UF"
                                @input="setMunicipio()"
                                :class="{'v-select-invalid': errors.has('uf')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('uf')" class="v-select-invalid-feedback">
                                {{ errors.first('uf') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="municipio">Municipio:</label>
                            <v-select
                                :disabled="loading"
                                name="municipio"
                                placeholder="Selecione..."
                                :options="municipios"
                                label="nome"
                                :resetOnOptionsChange="true"
                                v-model="post.municipio"
                                v-validate="{ required: post.tipo_alerta && post.tipo_alerta.posicao }"
                                data-vv-as="municipio"
                                :class="{'v-select-invalid': errors.has('municipio')}"
                            >
                                <div slot="no-options">Valor não encontrado.</div>
                            </v-select>
                            <span v-show="errors.has('municipio')" class="v-select-invalid-feedback">
                                {{ errors.first('municipio') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="local">Local:</label>
                            <input
                                :readonly="loading"
                                type="text"
                                name="local"
                                v-model="post.local"    
                                v-validate="{ required: post.tipo_alerta && post.tipo_alerta.posicao }"                         
                                class="form-control form-control-sm"
                                :class="{'is-invalid': errors.has('local')}"
                            >
                            <span v-show="errors.has('local')" class="invalid-feedback">
                                {{ errors.first('local') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="latitude">Latitude:</label>
                            <input
                                :readonly="loading"
                                type="text"
                                name="latitude"
                                v-model="post.latitude"    
                                v-validate="{ required: post.tipo_alerta && post.tipo_alerta.posicao }"                         
                                class="form-control form-control-sm"
                                :class="{'is-invalid': errors.has('latitude')}"
                            >
                            <span v-show="errors.has('latitude')" class="invalid-feedback">
                                {{ errors.first('latitude') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="longitude">Longitude:</label>
                            <input
                                :readonly="loading"
                                type="text"
                                name="longitude"
                                v-model="post.longitude"    
                                v-validate="{ required: post.tipo_alerta && post.tipo_alerta.posicao }"                         
                                class="form-control form-control-sm"
                                :class="{'is-invalid': errors.has('longitude')}"
                            >
                            <span v-show="errors.has('longitude')" class="invalid-feedback">
                                {{ errors.first('longitude') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-sm-6 col-md-3 col-xl-2">
                        <div class="form-group">
                            <label for="gr_ciente">GR Ciente:</label><br />
                            <div class="btn-group" style="margin-bottom:-10px;">
                                <div class="switch-field">           
                                    <input :disabled="loading" id="gr_left" name="gr_ciente" type="radio" :value="0" v-model="post.gr_ciente">
                                    <label for="gr_left">Não</label>             
                                    <input :disabled="loading" id="gr_right" name="gr_ciente" type="radio" :value="1" v-model="post.gr_ciente">
                                    <label for="gr_right">Sim</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-3 col-xl-2">
                        <div class="form-group">
                            <label for="resolvido">Problema Resolvido:</label><br />
                            <div class="btn-group" style="margin-bottom:-10px;">
                                <div class="switch-field">              
                                    <input :disabled="loading" id="acao_left" name="resolvido" type="radio" :value="0" v-model="post.resolvido">
                                    <label for="acao_left">Não</label>          
                                    <input :disabled="loading" id="acao_right" name="resolvido" type="radio" :value="1" v-model="post.resolvido">
                                    <label for="acao_right">Sim</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-7 col-md-4" style="margin-bottom: 5px;">
                        <label for="evidencia1">Evidência 1:</label>
                        <input
                            :readonly="loading"
                            name="evidencia1"
                            type="file"
                            accept="image/*"
                            ref="fileEvidencia1"
                            v-validate="'image'"
                            data-vv-as="evidência"
                            v-on:change="handleUploadEv1()"
                            :class="['form-control form-control-sm', {'is-invalid': errors.has('evidencia1')}]"
                            
                        >
                        <span v-show="errors.has('evidencia1')" class="invalid-feedback">
                            {{ errors.first('evidencia1') }}
                        </span>
                    </div>
                    <div v-if="urlEvidencia1 && !deletedEv1" class="col-5 col-md-1 d-flex align-items-end">
                        <a
                            :href="`https://${bucket}.s3.amazonaws.com/fotos_evi/${post.imgLink}/${urlEvidencia1}`"
                            class="btn btn-primary mr-2 mb-2"
                            data-lightbox="EVIDENCIA"
                            data-title="EVIDENCIA 1">
                            <i class="fas fa-camera"></i>
                        </a>

                        <a
                            :disabled="loading"
                            @click="!loading ? selectedFoto = 'url_evidencia1' : null"
                            class="btn btn-danger mb-2 text-white confirmation-box"
                            data-toggle="confirmation"
                            data-btn-ok-label="Sim"
                            data-btn-ok-class="btn btn-sm btn-success"
                            data-btn-cancel-label="Não"
                            data-btn-cancel-class="btn btn-sm btn-danger"
                            data-title="Confirmar?"
                            data-popout="true"
                            data-singleton="true"
                            data-placement="left">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>

                    <div class="offset-md-1 col-7 col-md-4" style="margin-bottom: 5px;">
                        <label for="evidencia2">Evidência 2:</label>
                        <input
                            :readonly="loading"
                            name="evidencia2"
                            type="file"
                            ref="fileEvidencia2"
                            v-validate="'image'"
                            accept="image/*"
                            data-vv-as="evidência"
                            v-on:change="handleUploadEv2()"
                            :class="['form-control form-control-sm', {'is-invalid': errors.has('evidencia2')}]"
                        >
                        <span v-show="errors.has('evidencia2')" class="invalid-feedback">
                            {{ errors.first('evidencia2') }}
                        </span>
                    </div> 
                    <div v-if="urlEvidencia2 && !deletedEv2" class="col-5 col-md-1 d-flex align-items-end">
                        <a
                            :href="`https://${bucket}.s3.amazonaws.com/fotos_evi/${post.imgLink}/${urlEvidencia2}`"
                            class="btn btn-primary mr-2 mb-2"
                            data-lightbox="EVIDENCIA"
                            data-title="EVIDENCIA 2">
                            <i class="fas fa-camera"></i>
                        </a>
                        <a
                            :disabled="loading"
                            @click="!loading ? selectedFoto = 'url_evidencia2' : null"
                            class="btn btn-danger mb-2 text-white confirmation-box"
                            data-toggle="confirmation"
                            data-btn-ok-label="Sim"
                            data-btn-ok-class="btn btn-sm btn-success"
                            data-btn-cancel-label="Não"
                            data-btn-cancel-class="btn btn-sm btn-danger"
                            data-title="Confirmar?"
                            data-popout="true"
                            data-singleton="true"
                            data-placement="left">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-footer p-2 pl-3">
                <button
                    type="sumbit"
                    @click="!loading ? onSubmit() : null"
                    :class="['btn btn-success btn-sm mt-1 col-md-2', {'disabled' : loading}]"
                >
                    <i v-if="loading" class="spinner-border spinner-border-sm spinner" role="status" aria-hidden="true"></i>
                    <i v-else class="fas fa-save"></i>
                    Salvar
                </button>

                <button
                    @click="cancel()"
                    :class="['btn btn-danger btn-sm mt-1 col-md-2', {'disabled' : loading}]"
                >
                    <i class="fas fa-undo"></i>
                    Cancelar
                </button>

                <button
                   v-if="this.post.id"
                    :disabled="loading"
                    @click="!loading ? selectedFoto = 'url_evidencia2' : null"
                    class="btn btn-primary btn-sm mt-1 col-md-2 text-white confirmation-mail"
                    data-toggle="confirmation"
                    data-btn-ok-label="Sim"
                    data-btn-ok-class="btn btn-sm btn-success"
                    data-btn-cancel-label="Não"
                    data-btn-cancel-class="btn btn-sm btn-danger"
                    data-title="Enviar E-mail?"
                    data-popout="true"
                    data-singleton="true"
                    data-placement="left">
                        <i v-if="mail" class="spinner-border spinner-border-sm"></i>
                        <i v-else class="fas fa-envelope mail-icon"></i>
                         Enviar E-mail
                </button>
            </div>
        </div>
        <notifications group="submit" position="center bottom" />
    </div>
</template>

<script>
export default {
    components: {            
        vSelect: () => import('vue-select' /* webpackChunkName: "vue-select" */),
    },
    props: {
        'data': {
            default: null
        },
        'solicitacao': {
            default: false
        }
    },
    data() {
        return {
           
            bucket: process.env.MIX_AWS_BUCKET,
            tiposAlertas: [],
            empresas: [],
            transportadoras: [],
            gerenciadorasRiscos: [],
            cargos: [],
            operadores: [],
            tecnologias: [],
            post: {
                id: this.data && !this.solicitacao ? this.data.hash_id : null,
                solicitacao_id: this.solicitacao ? this.data.hash_id : this.data ? this.data.hash_solicitacao_id : null,
                protocolo: this.data ? this.data.protocolo : null,
                sm: this.data ? this.data.sm : null,
                tipo_alerta: null,
                empresa: null,
                transportadora: null,
                data_ident: moment().format('YYYY-MM-DD'),
                hora_ident: moment().format('HH:mm'),
                motorista: this.data ? this.data.motorista : null,
                cpf: this.data ? this.data.cpf : null,
                gerenciadora_risco: null,
                quem: this.data ? this.data.quem : null,
                data_ciencia: this.data ? this.data.data_ciencia : null,
                hora_ciencia: this.data ? this.data.hora_ciencia : null,
                cargo: null,
                acao: this.data ? this.data.acao : null,
                placa: this.solicitacao ? this.data.placa_cavalo : this.data ? this.data.placa : null,
                operador: null,
                tecnologia: null,
                gr_ciente: this.data && this.data.gr_ciente ? this.data.gr_ciente : 0,
                resolvido: this.data && this.data.resolvido ? this.data.resolvido : 0,
                uf: null,
                municipio: null,
                local: this.data && this.data.auditoria_local ? this.data.auditoria_local.local : null,
                latitude: this.data && this.data.auditoria_local ? this.data.auditoria_local.latitude : null,
                longitude: this.data && this.data.auditoria_local ? this.data.auditoria_local.longitude : null,
                imgLink: this.data ? this.data.id : null,
            },
            urlEvidencia1: this.data ? this.data.url_evidencia1 : null,
            urlEvidencia2: this.data ? this.data.url_evidencia2 : null,
            selectedFoto: null,
            evidencia1: null,
            deletedEv1: false,
            evidencia2: null,
            deletedEv2: false,
            loading: false,
            mail: false,
            ufs: [],
            municipios: []
        }
    },
    created() {
        axios.get(process.env.MIX_BASE_URL+'/getestados').then(res => {
            this.ufs = res.data
        }).then(() => {
            if(this.data) {
                if(this.data.auditoria_local) {
                    this.post.uf = this.ufs.find(uf => {
                        return uf.id == this.data.auditoria_local.municipio.estado.id
                    })
                }
            }
        }).then(() => {
            if(this.data) {
                if(this.data.auditoria_local) {
                    this.setMunicipio()
                }
            }
        })
    },
    computed: {
        solicitation() {
            return this.solicitacao ? true : this.post.solicitacao_id ? true : false
        },
        url() {
            return this.post.id ? `/${this.post.id}` : ''
        }
    },
    methods: {
        setMunicipio() {
            if(this.post.uf) {
                axios.get(process.env.MIX_BASE_URL+`/getmunicipios/${this.post.uf.id}`).then(res => {
                    this.municipios = res.data
                }).then(() => {
                    if(this.data) {
                        this.post.municipio = this.municipios.find(municipio => {
                            return municipio.id == this.data.auditoria_local.municipio_id
                        })
                    }
                })
            } else {
                this.municipios = []
            }
            
        },
        resetLocal() {
            this.post.uf = null
            this.post.municipio = null
            this.post.local = null
        },
        confirmation() {
            $('.confirmation-box').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                onConfirm: () => {
                    if(!this.loading) {
                        this.loading = true
                        this.deleteFoto()
                    }
                },
                onCancel: () => {
                    this.selectedFoto = null
                }
            })
        },
        deleteFoto() {
            axios.post(process.env.MIX_BASE_URL+'/auditcenter/deletefoto', {
                    'id': this.data.hash_id,
                    'field': this.selectedFoto
                }).then(() => {
                    if(this.selectedFoto == 'url_evidencia1') {
                        this.deletedEv1 = true 
                    } else {
                        this.deletedEv2 = true
                    }
                    this.loading = false
                    this.selectedFoto = null
                })
        },
        confirmationMail() {
            $('.confirmation-mail').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                onConfirm: () => {
                    if(!this.loading) {
                        this.loading = true
                        this.mail = true
                        this.sendMail()
                    }
                }
            })
        },
        sendMail() {
            axios.post(process.env.MIX_BASE_URL+'/auditcenter/sendmail', {id:this.post.id}).then(res => {
                this.loading = false
                this.mail = false
                if(res.data.status_code == 505) {
                    this.$notify({
                        group: 'submit',
                        title: 'Erro!',
                        text: res.data.msg,
                        type: 'error'
                    })
                } else {
                    this.$notify({
                        group: 'submit',
                        title: 'Sucesso!',
                        text: 'E-mail enviado...',
                        type: 'success'
                    })
                }
            }).catch(error => {
                this.loading = false
                this.mail = false
                this.$notify({
                    group: 'submit',
                    title: 'Erro!',
                    text: error,
                    type: 'error'
                })
            })
        },
        cancel() {
            if(this.solicitacao) {
                window.location.href=""+process.env.MIX_BASE_URL+"/solicitacoes"
            } else {                
                window.location.href=""+process.env.MIX_BASE_URL+"/auditcenter"
            }
        },
        onSubmit () {
            this.$validator.validate().then(valid => {
                if (valid) {
                    let formData = new FormData()
                    formData.append('data', JSON.stringify(this.post))
                    if(this.post.evidencia1) {
                        formData.append('evidencia1', this.post.evidencia1)
                    }
                    if(this.post.evidencia2) {
                        formData.append('evidencia2', this.post.evidencia2)
                    }
                    if(this.post.id) {
                        formData.append('_method', 'put')
                    }
                    this.loading = true
                    axios.post(process.env.MIX_BASE_URL+`/auditcenter${this.url}`, formData)
                            .then(res => {  
                                this.loading = false

                            if(res.data.erroEvidencia1) {
                                this.$notify({
                                    group: 'submit',
                                    title: 'Erro! Evidência 1',
                                    text: res.data.erroEvidencia1,
                                    type: 'error'
                                })
                            }else if(res.data.erroEvidencia2){
                                this.$notify({ 
                                    group: 'submit',
                                    title: 'Erro! Evidência 2',
                                    text: res.data.erroEvidencia2,
                                    type: 'error'
                                })
                            }else {
                                 if(res.data.url_evidencia1) {
                                    this.urlEvidencia1 = res.data.url_evidencia1
                                    this.deletedEv1 = false
                                }

                                if(res.data.url_evidencia2) {
                                    this.urlEvidencia2 = res.data.url_evidencia2
                                    this.deletedEv2 = false
                                    
                                }
                                this.post.protocolo = res.data.protocolo
                                this.post.id = res.data.hash_id
                                this.post.evidencia1 = null
                                this.post.evidencia2 = null
                                this.$notify({
                                    group: 'submit',
                                    title: 'Sucesso!',
                                    text: 'Registro atualizado',
                                    type: 'success'
                                })
                            }



                               
                            }).then(() => {
                                this.confirmationMail()
                                this.confirmation()
                            }).catch(error => {
                                this.loading = false
                                this.$notify({
                                    group: 'submit',
                                    title: 'Erro!',
                                    text: 'Por favor tente novamente',
                                    type: 'error'
                                })
                            })
                }
            })
        },
        handleUploadEv1(){
            this.post.evidencia1 = this.$refs.fileEvidencia1.files[0];
        },   
        handleUploadEv2(){
            this.post.evidencia2 = this.$refs.fileEvidencia2.files[0];
        },        
        getTransportadorasGrs() {
            if(this.post.empresa) {
                axios.get(process.env.MIX_BASE_URL+`/gettransportadoras/${this.post.empresa.hash_id}`).then(res => {
                    this.transportadoras = res.data
                }).then(() => {
                    if(this.data) {
                        this.post.transportadora = this.transportadoras.find(transportadora => {
                            return transportadora.hash_id == this.data.hash_transportadora_id
                        })
                    }
                })
                axios.get(process.env.MIX_BASE_URL+`/getgrs/${this.post.empresa.hash_id}`).then(res => {
                    this.gerenciadorasRiscos = res.data
                }).then(() => {
                    if(this.data) {
                        this.post.gerenciadora_risco = this.gerenciadorasRiscos.find(gerenciadora_risco => {
                            return gerenciadora_risco.hash_id == this.data.hash_gerenciadora_risco_id
                        })
                    }
                })
            } else {
                this.transportadoras = []
                this.gerenciadorasRiscos = []
            }
        }
    },
    mounted() {  
        this.confirmation()  
        this.confirmationMail()
        if(this.data) {
            if(this.data.dt_identificacao) {
                this.post.data_ident = moment(this.data.dt_identificacao).format('YYYY-MM-DD')
                this.post.hora_ident = moment(this.data.dt_identificacao).format('HH:mm')
            }
            if(this.data.dt_ciencia) {
                this.post.data_ciencia = moment(this.data.dt_ciencia).format('YYYY-MM-DD')
                this.post.hora_ciencia = moment(this.data.dt_ciencia).format('HH:mm')
            }
        }    

        axios.get(process.env.MIX_BASE_URL+'/gettipoalertas').then(res => {
            this.tiposAlertas = res.data
        }).then(() => {
            if(this.data) {
                this.post.tipo_alerta = this.tiposAlertas.find(tipoAlerta => {
                    return tipoAlerta.hash_id == this.data.hash_tipo_alerta_id
                })
            }
        })

        axios.get(process.env.MIX_BASE_URL+'/getempresas').then(res => {
            this.empresas = res.data
        }).then(() => {
            if(this.data) {
                this.post.empresa = this.empresas.find(empresa => {
                    return empresa.hash_id == this.data.transportadora.empresa.hash_id
                })
            }
        }).then(() => {
            if(this.data) {
                this.getTransportadorasGrs()
            }
        })
        
        axios.get(process.env.MIX_BASE_URL+'/getcargos').then(res => {
            this.cargos = res.data
        }).then(() => {
            if(this.data) {
                this.post.cargo = this.cargos.find(cargo => {
                    return cargo.hash_id == this.data.hash_cargo_id
                })
            }
        })

        axios.get(process.env.MIX_BASE_URL+'/getoperadores').then(res => {
            this.operadores = res.data
        }).then(() => {
            if(this.data) {
                this.post.operador = this.operadores.find(operador => {
                    return operador.hash_id == this.data.hash_operador_id
                })
            }
        })
        
        axios.get(process.env.MIX_BASE_URL+'/gettecnologias').then(res => {
            this.tecnologias = res.data
        }).then(() => {
            if(this.data) {
                this.post.tecnologia = this.tecnologias.find(tecnologia => {
                    return tecnologia.hash_id == this.data.hash_tecnologia_id
                })
            }
        })
    }
}
</script>