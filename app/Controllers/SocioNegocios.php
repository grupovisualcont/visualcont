<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\Predeterminado;
use App\Models\SocioNegocio;
use App\Models\SocioNegocioBanco;
use App\Models\SocioNegocioXTipo;
use App\Models\TipoDocumentoIdentidad;
use App\Models\TipoPersona;
use App\Models\TipoSocioNegocio;
use App\Models\Ts27Vinculo;
use App\Models\Ubigeo;

class SocioNegocios extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $socioNegocioModel;
    protected $tipoPersonaModel;
    protected $tipoDocumentoIdentidadModel;
    protected $cajaBancoModel;
    protected $ubigeoModel;
    protected $anexoModel;
    protected $tipoSocioNegocioModel;
    protected $socioNegocioXTipoModel;
    protected $ts27VinculoModel;
    protected $socioNegocioBancoModel;
    protected $predeterminadoModel;

    public function __construct()
    {
        $this->page = 'Socio de Negocio';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->socioNegocioModel = new SocioNegocio();
        $this->tipoPersonaModel = new TipoPersona();
        $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();
        $this->cajaBancoModel = new Banco();
        $this->ubigeoModel = new Ubigeo();
        $this->anexoModel = new Anexo();
        $this->tipoSocioNegocioModel = new TipoSocioNegocio();
        $this->socioNegocioXTipoModel = new SocioNegocioXTipo();
        $this->ts27VinculoModel = new Ts27Vinculo();
        $this->socioNegocioBancoModel = new SocioNegocioBanco();
        $this->predeterminadoModel = new Predeterminado();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->socioNegocioModel = new SocioNegocio();

                $socio_negocio = $this->socioNegocioModel->getSocioNegocio(
                    $this->CodEmpresa,
                    0,
                    '
                    socionegocio.*, 
                    IF(LENGTH(socionegocio.razonsocial) = 0, CONCAT(socionegocio.Nom1, " ", IF(LENGTH(socionegocio.Nom2) = 0, "", CONCAT(socionegocio.Nom2, " ")), socionegocio.ApePat, " ", socionegocio.ApeMat), socionegocio.razonsocial) AS razonsocial, 
                    a1.DescAnexo AS estado, 
                    a2.DescAnexo AS condicion
                    ',
                    [
                        array('tabla' => 'anexos a1', 'on' => 'a1.IdAnexo = socionegocio.Idestado AND a1.CodEmpresa = socionegocio.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'anexos a2', 'on' => 'a2.IdAnexo = socionegocio.Idestado AND a2.CodEmpresa = socionegocio.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    'socionegocio.IdSocioN ASC'
                );

                return viewApp($this->page, 'app/mantenience/business_partner/index', [
                    'socio_negocio' => $socio_negocio,
                    'typeOrder' => 'num'
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->predeterminadoModel = new Predeterminado();

                $predeterminados = $this->predeterminadoModel->getPredeterminado('CodTipPer_sn, CodTipoDoc_sn, IdCondicion_sn, CodUbigeo_sn, IdEstadoSN');

                $this->tipoPersonaModel = new TipoPersona();

                $tipos_persona = $this->tipoPersonaModel->getTipoPersona($predeterminados->CodTipPer_sn, '', [], '', '')[0];

                $option_tipos_persona = '<option value="' . $tipos_persona['CodTipPer'] . '">' . $tipos_persona['DescPer'] . '</option>';

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $tipos_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad($predeterminados->CodTipoDoc_sn, '', [], '', '')[0];

                $option_tipos_documento_identidad = '<option data-tipo-dato="' . $tipos_documento_identidad['TipoDato'] . '" value="' . $tipos_documento_identidad['CodTipoDoc'] . '">' . $tipos_documento_identidad['DesDocumento'] . '</option>';

                $this->anexoModel = new Anexo();

                $condiciones = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 2, '', '', [], '', '')[0];

                $option_condiciones = '<option value="' . $condiciones['IdAnexo'] . '">' . $condiciones['DescAnexo'] . '</option>';

                $this->ubigeoModel = new Ubigeo();

                $paises = $this->ubigeoModel->getPaises();

                $options_paises = '';

                foreach ($paises as $indice => $valor) {
                    $selected = '';

                    if (strlen($valor['codubigeo']) == 2) $selected = 'selected';

                    $options_paises .= '<option value="' . $valor['codubigeo'] . '" ' . $selected . '>' . $valor['descubigeo'] . '</option>';
                }

                $ubigeos = $this->db
                    ->query('SELECT dist.codubigeo, (
                                SELECT (
                                    SELECT CONCAT(dept.descubigeo, " \\\ ", prov.descubigeo, " \\\ ", dist.descubigeo) 
                                    FROM ubigeo dept WHERE dept.codubigeo = SUBSTRING(prov.codubigeo, 1, 4)
                                )
                                FROM ubigeo prov
                                WHERE prov.codubigeo = SUBSTRING(dist.codubigeo, 1, 6)
                            )
                            AS descubigeo
                            FROM ubigeo dist
                            WHERE LENGTH(dist.codubigeo) = 9 AND LENGTH(dist.codubigeo) != 2 AND dist.codubigeo NOT LIKE "9%"
                        ')->getResult();

                $options_ubigeos = '';

                foreach ($ubigeos as $indice => $valor) {
                    $selected = '';

                    if ($valor->codubigeo == $predeterminados->CodUbigeo_sn) $selected = 'selected';

                    $options_ubigeos .= '<option value="' . $valor->codubigeo . '" ' . $selected . '>' . htmlspecialchars($valor->descubigeo, ENT_QUOTES) . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, 11, 1, '', '', [], '', '')[0];

                $option_estados = '<option value="' . $estados['IdAnexo'] . '">' . $estados['DescAnexo'] . '</option>';

                $this->tipoSocioNegocioModel = new TipoSocioNegocio();

                $tipos_socio_negocio = $this->tipoSocioNegocioModel->getTipoSocioNegocio();

                $checkbox_tipos_socio_negocio = '';

                foreach ($tipos_socio_negocio as $indice => $valor) {
                    $checkbox_tipos_socio_negocio .= '
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="tipo_socio_negocio[]" value="' . $valor->CodTipoSN . '">' . $valor->DescTipoSN . '
                            </label>
                        </div>
                    ';
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $datos_ruc = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidadByCodTipoDoc(6, 'CodTipoDoc, N_tip');

                if (count($datos_ruc) == 0) {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => '6', 'N_tip' => 'F'];
                } else {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => $datos_ruc[0]['CodTipoDoc'], 'N_tip' => $datos_ruc[0]['N_tip']];
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $datos_extranjero = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidadByCodTipoDoc('-', 'CodTipoDoc, N_tip');

                if (count($datos_extranjero) == 0) {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => '-'];
                } else {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => $datos_extranjero[0]['CodTipoDoc']];
                }

                $this->empresa = new Empresa();

                $script = "
                    var datos_ruc_CodTipPer = '" . $datos_ruc['CodTipPer'] . "';
                    var datos_ruc_CodTipoDoc = '" . $datos_ruc['CodTipoDoc'] . "';
                    var datos_ruc_N_tip = '" . $datos_ruc['N_tip'] . "';
                    var datos_extranjero_CodTipPer = '" . $datos_extranjero['CodTipPer'] . "';
                    var datos_extranjero_CodTipoDoc = '" . $datos_extranjero['CodTipoDoc'] . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/business_partner/create.js']);

                return viewApp($this->page, 'app/mantenience/business_partner/create', [
                    'option_tipos_persona' => $option_tipos_persona,
                    'option_tipos_documento_identidad' => $option_tipos_documento_identidad,
                    'option_condiciones' => $option_condiciones,
                    'options_paises' => $options_paises,
                    'options_ubigeos' => $options_ubigeos,
                    'option_estados' => $option_estados,
                    'checkbox_tipos_socio_negocio' => $checkbox_tipos_socio_negocio,
                    'typeOrder' => 'num',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($IdSocioN)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->socioNegocioModel = new SocioNegocio();

                $socionegocio = $this->socioNegocioModel->getSocioNegocio(
                    $this->CodEmpresa,
                    $IdSocioN,
                    [
                        array('tabla' => 'anexos a1', 'on' => 'a1.IdAnexo = socionegocio.Idestado AND a1.CodEmpresa = socionegocio.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'anexos a2', 'on' => 'a2.IdAnexo = socionegocio.Idestado AND a2.CodEmpresa = socionegocio.CodEmpresa', 'tipo' => 'left')
                    ],
                    'socionegocio.*, a1.DescAnexo AS estado, a2.DescAnexo AS condicion',
                    '',
                    'socionegocio.IdSocioN ASC'
                )[0];

                $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                $socionegociotipos = $this->socioNegocioXTipoModel->getSocioNegocioXTipo($IdSocioN);

                $socionegociotipo_array = array();

                foreach ($socionegociotipos as $socionegociotipo) {
                    $socionegociotipo_array[] = $socionegociotipo['CodTipoSN'];
                }

                $this->socioNegocioBancoModel = new SocioNegocioBanco();

                $socionegociobanco = $this->socioNegocioBancoModel->getSocioNegocioBanco($IdSocioN);

                $this->tipoPersonaModel = new TipoPersona();

                $tipos_persona = $this->tipoPersonaModel->getTipoPersona();

                $options_tipos_persona = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipos_persona as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodTipPer'] == $socionegocio['CodTipPer']) $selected = 'selected';

                    $options_tipos_persona .= '<option value="' . $valor['CodTipPer'] . '" ' . $selected . '>' . $valor['DescPer'] . '</option>';
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $tipos_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad();

                $options_tipos_documento_identidad = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipos_documento_identidad as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodTipoDoc'] == $socionegocio['CodTipoDoc']) $selected = 'selected';

                    $options_tipos_documento_identidad .= '<option data-tipo-dato="' . $valor['TipoDato'] . '" value="' . $valor['CodTipoDoc'] . '" ' . $selected . '>' . $valor['DesDocumento'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $condiciones = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 2, '', '', '', '');

                $options_condiciones = '<option value="" disabled selected>Seleccione</option>';

                foreach ($condiciones as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $socionegocio['IdCondicion']) $selected = 'selected';

                    $options_condiciones .= '<option data-descripcion="' . $valor['DescAnexo'] . '" value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->ubigeoModel = new Ubigeo();

                $paises = $this->ubigeoModel->getPaises();

                $options_paises = '';

                foreach ($paises as $indice => $valor) {
                    $selected = '';

                    if (substr($socionegocio['codubigeo'], 0, 2) == $valor['codubigeo']) {
                        $selected = 'selected';
                    } else if ($valor['codubigeo'] == $socionegocio['codubigeo']) {
                        $selected = 'selected';
                    }

                    $options_paises .= '<option value="' . $valor['codubigeo'] . '" ' . $selected . '>' . $valor['descubigeo'] . '</option>';
                }

                $ubigeos = $this->db
                    ->query('SELECT dist.codubigeo, (
                                SELECT (
                                    SELECT CONCAT(dept.descubigeo, " \\\ ", prov.descubigeo, " \\\ ", dist.descubigeo) 
                                    FROM ubigeo dept WHERE dept.codubigeo = SUBSTRING(prov.codubigeo, 1, 4)
                                )
                                FROM ubigeo prov
                                WHERE prov.codubigeo = SUBSTRING(dist.codubigeo, 1, 6)
                            )
                            AS descubigeo
                            FROM ubigeo dist
                            WHERE LENGTH(dist.codubigeo) = 9 AND LENGTH(dist.codubigeo) != 2 AND dist.codubigeo NOT LIKE "9%"
                        ')->getResult();

                $options_ubigeos = '';

                foreach ($ubigeos as $indice => $valor) {
                    $selected = '';

                    if ($valor->codubigeo == $socionegocio['codubigeo']) $selected = 'selected';

                    $options_ubigeos .= '<option value="' . $valor->codubigeo . '" ' . $selected . '>' . htmlspecialchars($valor->descubigeo, ENT_QUOTES) . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 1, '', '', '', '');

                $options_estados = '<option value="" disabled selected>Seleccione</option>';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $socionegocio['Idestado']) $selected = 'selected';

                    $options_estados .= '<option data-descripcion="' . $valor['DescAnexo'] . '" value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->tipoSocioNegocioModel = new TipoSocioNegocio();

                $tipos_socio_negocio = $this->tipoSocioNegocioModel->getTipoSocioNegocio();

                $checkbox_tipos_socio_negocio = '';

                foreach ($tipos_socio_negocio as $indice => $valor) {
                    $checked = '';

                    if (in_array($valor->CodTipoSN, $socionegociotipo_array)) $checked = 'checked';

                    $checkbox_tipos_socio_negocio .= '
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="tipo_socio_negocio[]" value="' . $valor->CodTipoSN . '" ' . $checked . '>' . $valor->DescTipoSN . '
                            </label>
                        </div>
                    ';
                }

                $this->ts27VinculoModel = new Ts27Vinculo();

                $vinculos = $this->ts27VinculoModel->getTs27Vinculo();

                $options_vinculos = '';

                foreach ($vinculos as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodVinculo'] == $socionegocio['CodVinculo']) $selected = 'selected';

                    $options_vinculos .= '<option value="' . $valor['CodVinculo'] . '" ' . $selected . '>' . $valor['DescVinculo'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $sexos = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 3, '', '', '', '');

                $options_sexos = '<option value="" disabled selected>Seleccione</option>';

                foreach ($sexos as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $socionegocio['IdSexo']) $selected = 'selected';

                    $options_sexos .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $tipos_documento_identidad_bancos = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidadBanco();

                $options_tipos_documento_identidad_bancos = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipos_documento_identidad_bancos as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodTipoDoc'] == $socionegocio['CodTipoDoc_Tele']) $selected = 'selected';

                    $options_tipos_documento_identidad_bancos .= '<option value="' . $valor['CodTipoDoc'] . '" ' . $selected . '>' . $valor['DesDocumento'] . '</option>';
                }

                $this->cajaBancoModel = new Banco();

                $bancos = $this->cajaBancoModel->getBanco($this->CodEmpresa, '', '', [], 'Codbanco, abreviatura', '', '');

                $options_banco = '<option value="" disabled selected>Seleccione</option>';

                foreach ($bancos as $indice => $valor) {
                    $options_banco .= '<option value="' . $valor['Codbanco'] . '">' . $valor['Codbanco'] . ' - ' . $valor['abreviatura'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $tipos_cuenta = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 54, '02', '', '', 'CodInterno ASC');

                $options_tipo_cuenta = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipos_cuenta as $indice => $valor) {
                    $options_tipo_cuenta .= '<option value="' . $valor['IdAnexo'] . '">' . $valor['IdAnexo'] . ' - ' . $valor['DescAnexo'] . '</option>';
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $datos_ruc = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidadByCodTipoDoc(6, 'CodTipoDoc, N_tip');

                if (count($datos_ruc) == 0) {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => '6', 'N_tip' => 'F'];
                } else {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => $datos_ruc[0]['CodTipoDoc'], 'N_tip' => $datos_ruc[0]['N_tip']];
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $datos_extranjero = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidadByCodTipoDoc('-', 'CodTipoDoc, N_tip');

                if (count($datos_extranjero) == 0) {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => '-'];
                } else {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => $datos_extranjero[0]['CodTipoDoc']];
                }

                $this->empresa = new Empresa();

                $script = "
                    var id_banco = " . (count($socionegociobanco) + 1) . ";
                    var datos_ruc_CodTipPer = '" . $datos_ruc['CodTipPer'] . "';
                    var datos_ruc_CodTipoDoc = '" . $datos_ruc['CodTipoDoc'] . "';
                    var datos_ruc_N_tip = '" . $datos_ruc['N_tip'] . "';
                    var datos_extranjero_CodTipPer = '" . $datos_extranjero['CodTipPer'] . "';
                    var datos_extranjero_CodTipoDoc = '" . $datos_extranjero['CodTipoDoc'] . "';
                    var options_banco = '" . $options_banco . "';
                    var options_tipo_cuenta = '" . $options_tipo_cuenta . "';
                    var socionegocio_ruc = '" . $socionegocio['ruc'] . "';
                    var socionegocio_docidentidad = '" . $socionegocio['docidentidad'] . "';
                    var socionegocio_razonsocial = '" . $socionegocio['razonsocial'] . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/business_partner/edit.js']);

                return viewApp($this->page, 'app/mantenience/business_partner/edit', [
                    'codigo_socio_negocio' => $IdSocioN,
                    'socionegocio' => $socionegocio,
                    'socionegociobanco' => $socionegociobanco,
                    'options_tipos_persona' => $options_tipos_persona,
                    'options_tipos_documento_identidad' => $options_tipos_documento_identidad,
                    'options_condiciones' => $options_condiciones,
                    'options_paises' => $options_paises,
                    'options_ubigeos' => $options_ubigeos,
                    'options_estados' => $options_estados,
                    'checkbox_tipos_socio_negocio' => $checkbox_tipos_socio_negocio,
                    'options_vinculos' => $options_vinculos,
                    'options_sexos' => $options_sexos,
                    'options_tipos_documento_identidad_bancos' => $options_tipos_documento_identidad_bancos,
                    'options_banco' => $options_banco,
                    'options_tipo_cuenta' => $options_tipo_cuenta,
                    'typeOrder' => 'num',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function save()
    {
        try {
            $post = $this->request->getPost();

            if (!empty($post['ruc'])) {
                $this->socioNegocioModel = new SocioNegocio();

                $existe_socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'ruc = "' . $post['ruc'] . '" OR razonsocial = "' . $post['razonsocial'] . '"', '');
            } else if (!empty($post['docidentidad'])) {
                $this->socioNegocioModel = new SocioNegocio();

                $existe_socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'docidentidad = "' . $post['docidentidad'] . '" OR razonsocial = "' . $post['razonsocial'] . '"', '');
            } else if ($post['CodTipoDoc'] == '-') {
                $existe_socio_negocio = array();
            }

            if (count($existe_socio_negocio) == 0) {
                $tipos_socio_negocio = isset($post['tipo_socio_negocio']) ? $post['tipo_socio_negocio'] : [];
                $codigos_banco = isset($post['CodBanco']) ? $post['CodBanco'] : [];
                $predeterminado = isset($post['Predeterminado']) ? $post['Predeterminado'] : 0;

                $post['fecingreso'] = !empty($post['fecingreso']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fecingreso']))) : NULL;
                $post['ApePat'] = ucwords(strtolower(trim($post['ApePat'])));
                $post['ApeMat'] = ucwords(strtolower(trim($post['ApeMat'])));
                $post['Nom1'] = ucwords(strtolower(trim($post['Nom1'])));
                $post['Nom2'] = ucwords(strtolower(trim($post['Nom2'])));
                $post['razonsocial'] = strtoupper(trim($post['razonsocial']));

                $this->db->disableForeignKeyChecks();

                $this->db->transBegin();

                $this->socioNegocioModel = new SocioNegocio();

                $IdSocioN = $this->socioNegocioModel->agregar($post);

                if (count($tipos_socio_negocio) > 0) {
                    $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                    $this->socioNegocioXTipoModel->eliminar($IdSocioN);

                    foreach ($tipos_socio_negocio as $indice => $valor) {
                        $data = [
                            'CodTipoSN' => $valor,
                            'IdSocioN' => $IdSocioN
                        ];

                        $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                        $this->socioNegocioXTipoModel->agregar($data);
                    }
                } else {
                    $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                    $this->socioNegocioXTipoModel->eliminar($IdSocioN);
                }

                if (count($codigos_banco) > 0) {
                    $this->socioNegocioBancoModel = new SocioNegocioBanco();

                    $this->socioNegocioBancoModel->eliminar($IdSocioN);

                    foreach ($codigos_banco as $indice => $valor) {
                        $predeterminado_auxiliar = '0';

                        if ($predeterminado == $indice + 1) $predeterminado_auxiliar = '1';

                        $data = [
                            'IdSocion' => $IdSocioN,
                            'CodBanco' => $valor,
                            'idTipoCuenta' => $post['idTipoCuenta'][$indice],
                            'NroCuenta' => $post['NroCuenta'][$indice],
                            'NroCuentaCCI' => $post['NroCuentaCCI'][$indice],
                            'Predeterminado' => $predeterminado_auxiliar
                        ];

                        $this->socioNegocioBancoModel = new SocioNegocioBanco();

                        $this->socioNegocioBancoModel->agregar($data);
                    }
                } else {
                    $this->socioNegocioBancoModel = new SocioNegocioBanco();

                    $this->socioNegocioBancoModel->eliminar($IdSocioN);
                }

                if ($this->db->transStatus() === FALSE) {
                    $this->db->transRollback();

                    $result = false;
                } else {
                    $this->db->transCommit();

                    $result = true;
                }
            } else {
                $result = false;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/business_partner/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update()
    {
        try {
            $post = $this->request->getPost();

            if (substr($post['pais'], 0, 2) != '01') $post['codubigeo'] = $post['pais'];

            $IdSocioN = $this->request->getPost('IdSocioN');

            if ($post['CodTipoDoc'] == '6') {
                $this->socioNegocioModel = new SocioNegocio();

                $existe_socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', "IdSocioN != " . $IdSocioN . " AND ruc != '" . $post['ruc'] . "' OR razonsocial = '" . $post['razonsocial'] . "'", '');
            } else {
                $this->socioNegocioModel = new SocioNegocio();

                $existe_socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', "IdSocioN != " . $IdSocioN . " AND docidentidad != '" . $post['docidentidad'] . "' OR razonsocial = '" . $post['razonsocial'] . "'", '');
            }

            if (count($existe_socio_negocio) > 0) {
                $tipos_socio_negocio = isset($post['tipo_socio_negocio']) ? $post['tipo_socio_negocio'] : [];
                $codigos_banco = isset($post['CodBanco']) ? $post['CodBanco'] : [];
                $predeterminado = isset($post['Predeterminado']) ? $post['Predeterminado'] : 0;

                $post['fecingreso'] = !empty($post['fecingreso']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fecingreso']))) : NULL;
                $post['ApePat'] = ucwords(strtolower(trim($post['ApePat'])));
                $post['ApeMat'] = ucwords(strtolower(trim($post['ApeMat'])));
                $post['Nom1'] = ucwords(strtolower(trim($post['Nom1'])));
                $post['Nom2'] = ucwords(strtolower(trim($post['Nom2'])));
                $post['razonsocial'] = strtoupper(trim($post['razonsocial']));

                $this->db->disableForeignKeyChecks();

                $this->db->transBegin();

                $this->socioNegocioModel = new SocioNegocio();

                $this->socioNegocioModel->actualizar($IdSocioN, $post);

                if (count($tipos_socio_negocio) > 0) {
                    $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                    $this->socioNegocioXTipoModel->eliminar($IdSocioN);

                    foreach ($tipos_socio_negocio as $indice => $valor) {
                        $data = [
                            'CodTipoSN' => $valor,
                            'IdSocioN' => $IdSocioN
                        ];

                        $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                        $this->socioNegocioXTipoModel->agregar($data);
                    }
                } else {
                    $this->socioNegocioXTipoModel = new SocioNegocioXTipo();

                    $this->socioNegocioXTipoModel->eliminar($IdSocioN);
                }

                if (count($codigos_banco) > 0) {
                    $this->socioNegocioBancoModel = new SocioNegocioBanco();

                    $this->socioNegocioBancoModel->eliminar($IdSocioN);

                    foreach ($codigos_banco as $indice => $valor) {
                        $predeterminado_auxiliar = '0';

                        if ($predeterminado == $indice + 1) $predeterminado_auxiliar = '1';

                        $data = [
                            'IdSocion' => $IdSocioN,
                            'CodBanco' => $valor,
                            'idTipoCuenta' => $post['idTipoCuenta'][$indice],
                            'NroCuenta' => $post['NroCuenta'][$indice],
                            'NroCuentaCCI' => $post['NroCuentaCCI'][$indice],
                            'Predeterminado' => $predeterminado_auxiliar
                        ];

                        $this->socioNegocioBancoModel = new SocioNegocioBanco();

                        $this->socioNegocioBancoModel->agregar($data);
                    }
                } else {
                    $this->socioNegocioBancoModel = new SocioNegocioBanco();

                    $this->socioNegocioBancoModel->eliminar($IdSocioN);
                }

                if ($this->db->transStatus() === FALSE) {
                    $this->db->transRollback();

                    $result = false;
                } else {
                    $this->db->transCommit();

                    $result = true;
                }
            } else {
                $result = false;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/business_partner/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($IdSocioN)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->socioNegocioModel = new SocioNegocio();

            $this->socioNegocioModel->eliminar($this->CodEmpresa, $IdSocioN);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/business_partner/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Socio de Negocio - Reporte');

            $columnas = array('Código', 'Cliente', 'RUC', 'DocIdentidad', 'Teléfono', 'Dirección');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->socioNegocioModel = new SocioNegocio();

            $result = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], 'IdSocioN, IF(ruc = "", CONCAT(ApePat, " ", ApeMat, " ", Nom1, IF(LENGTH(Nom2) = 0, "", CONCAT(" ", Nom2))), razonsocial) AS Cliente, ruc, docidentidad, telefono, direccion1', '', 'IdSocioN ASC');

            foreach ($result as $indice => $valor) {
                $values = array(
                    $valor['IdSocioN'],
                    $valor['Cliente'],
                    $valor['ruc'],
                    $valor['docidentidad'],
                    $valor['telefono'],
                    $valor['direccion1']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('socio_negocio_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $this->socioNegocioModel = new SocioNegocio();

            $result = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], 'IdSocioN, IF(ruc = "", CONCAT(ApePat, " ", ApeMat, " ", Nom1, IF(LENGTH(Nom2) = 0, "", CONCAT(" ", Nom2))), razonsocial) AS Cliente, ruc, docidentidad, telefono, direccion1', '', 'IdSocioN ASC');

            $columnas = array('Código', 'Cliente', 'RUC', 'DocIdentidad', 'Teléfono', 'Dirección');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['IdSocioN'] . '</td>
                    <td align="left">' . $valor['Cliente'] . '</td>
                    <td align="left">' . $valor['ruc'] . '</td>
                    <td align="left">' . $valor['docidentidad'] . '</td>
                    <td align="left">' . $valor['telefono'] . '</td>
                    <td align="left">' . $valor['direccion1'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('socio_negocio_reporte');
            $pdf->creacion('Socio de Negocio - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_duplicados()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $ruc = trim(strval($this->request->getPost('ruc')));
            $docidentidad = trim(strval($this->request->getPost('docidentidad')));
            $razonsocial = strtoupper(trim(strval($this->request->getPost('razonsocial'))));

            if ($tipo == 'nuevo') {
                $this->socioNegocioModel = new SocioNegocio();

                $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'ruc = "' . $ruc . '"', '');

                $existe_duplicados = array('existe' => false, 'codigo' => '');

                if (!empty($ruc) && count($socio_negocio) > 0) {
                    $existe_duplicados = array('existe' => true, 'codigo' => $ruc);
                } else {
                    $this->socioNegocioModel = new SocioNegocio();

                    $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'docidentidad = "' . $docidentidad . '"', '');

                    if (!empty($docidentidad) && count($socio_negocio) > 0) {
                        $existe_duplicados = array('existe' => true, 'codigo' => $docidentidad);
                    } else {
                        $this->socioNegocioModel = new SocioNegocio();

                        $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'razonsocial = "' . $razonsocial . '"', '');

                        if (!empty($razonsocial) && count($socio_negocio) > 0) {
                            $existe_duplicados = array('existe' => true, 'codigo' => $razonsocial);
                        }
                    }
                }

                echo json_encode($existe_duplicados);
            } else if ($tipo == 'editar') {
                $Notruc = trim(strval($this->request->getPost('Notruc')));
                $Notdocidentidad = trim(strval($this->request->getPost('Notdocidentidad')));
                $Notrazonsocial = strtoupper(trim(strval($this->request->getPost('Notrazonsocial'))));

                $this->socioNegocioModel = new SocioNegocio();

                $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'ruc = "' . $ruc . '" AND ruc != "' . $Notruc . '"', '');

                $existe_duplicados = array('existe' => false, 'codigo' => '');

                if (!empty($ruc) && count($socio_negocio) > 0) {
                    $existe_duplicados = array('existe' => true, 'codigo' => $ruc);
                } else {
                    $this->socioNegocioModel = new SocioNegocio();

                    $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'docidentidad = "' . $docidentidad . '" AND docidentidad != "' . $Notdocidentidad . '"', '');

                    if (!empty($docidentidad) && count($socio_negocio) > 0) {
                        $existe_duplicados = array('existe' => true, 'codigo' => $docidentidad);
                    } else {
                        $this->socioNegocioModel = new SocioNegocio();

                        $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], '', 'razonsocial = "' . $razonsocial . '" AND razonsocial != "' . $Notrazonsocial . '"', '');

                        if (!empty($razonsocial) && count($socio_negocio) > 0) {
                            $existe_duplicados = array('existe' => true, 'codigo' => $razonsocial);
                        }
                    }
                }

                echo json_encode($existe_duplicados);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autocompletado()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new SocioNegocio())->autoCompletado($busqueda, 2, $this->request->getCookie('empresa'));
        return $this->response->setJson($items);
    }
}
