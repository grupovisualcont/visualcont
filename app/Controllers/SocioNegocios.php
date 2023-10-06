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
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Socio de Negocio';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $socio_negocio = (new SocioNegocio())->getSocioNegocio(
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
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $predeterminados = (new Predeterminado())->getPredeterminado('CodTipPer_sn, CodTipoDoc_sn, IdCondicion_sn, CodUbigeo_sn, IdEstadoSN');

                $tipo_persona = (new TipoPersona())->getTipoPersona($predeterminados->CodTipPer_sn, '', [], '', '')[0];

                $option_tipo_persona = '<option value="' . $tipo_persona['CodTipPer'] . '">' . $tipo_persona['DescPer'] . '</option>';

                $tipo_documento_identidad = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad($predeterminados->CodTipoDoc_sn, '', [], '', '')[0];

                $option_tipo_documento_identidad = '<option data-tipo-dato="' . $tipo_documento_identidad['TipoDato'] . '" value="' . $tipo_documento_identidad['CodTipoDoc'] . '">' . $tipo_documento_identidad['DesDocumento'] . '</option>';

                $condicion = (new Anexo())->getAnexo($this->CodEmpresa, 0, 2, '', '', [], '', '')[0];

                $option_condicion = '<option value="' . $condicion['IdAnexo'] . '">' . $condicion['DescAnexo'] . '</option>';

                $pais = (new Ubigeo())->getUbigeo('', '', [], 'LENGTH(codubigeo) = 2', '')[0];

                $option_pais = '<option value="' . $pais['codubigeo'] . '">' . $pais['descubigeo'] . '</option>';

                $ubigeo = (new Ubigeo())->getUbigeoQuery($this->db, $predeterminados->CodUbigeo_sn, '')[0];

                $option_ubigeo = '<option value="' . $ubigeo->id . '">' . htmlspecialchars($ubigeo->text, ENT_QUOTES) . '</option>';

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 11, 1, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $tipos_socio_negocio = (new TipoSocioNegocio())->getTipoSocioNegocio();

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

                $datos_ruc = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad(6, 'CodTipoDoc, N_tip', [], '', '');

                if (count($datos_ruc) == 0) {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => '6', 'N_tip' => 'F'];
                } else {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => $datos_ruc[0]['CodTipoDoc'], 'N_tip' => $datos_ruc[0]['N_tip']];
                }

                $datos_extranjero = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('-', 'CodTipoDoc, N_tip', [], '', '');

                if (count($datos_extranjero) == 0) {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => '-'];
                } else {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => $datos_extranjero[0]['CodTipoDoc']];
                }

                $script = "
                    var datos_ruc_CodTipPer = '" . $datos_ruc['CodTipPer'] . "';
                    var datos_ruc_CodTipoDoc = '" . $datos_ruc['CodTipoDoc'] . "';
                    var datos_ruc_N_tip = '" . $datos_ruc['N_tip'] . "';
                    var datos_extranjero_CodTipPer = '" . $datos_extranjero['CodTipPer'] . "';
                    var datos_extranjero_CodTipoDoc = '" . $datos_extranjero['CodTipoDoc'] . "';
                ";

                $script = (new Empresa())->generar_script($script, ['app/mantenience/business_partner/create.js']);

                return viewApp($this->page, 'app/mantenience/business_partner/create', [
                    'option_tipo_persona' => $option_tipo_persona,
                    'option_tipo_documento_identidad' => $option_tipo_documento_identidad,
                    'option_condicion' => $option_condicion,
                    'option_pais' => $option_pais,
                    'option_ubigeo' => $option_ubigeo,
                    'option_estado' => $option_estado,
                    'checkbox_tipos_socio_negocio' => $checkbox_tipos_socio_negocio,
                    'typeOrder' => 'num',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($IdSocioN)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $socio_negocio = (new SocioNegocio())->getSocioNegocio(
                    $this->CodEmpresa,
                    $IdSocioN,
                    'socionegocio.*, a1.DescAnexo AS estado, a2.DescAnexo AS condicion',
                    [
                        array('tabla' => 'anexos a1', 'on' => 'a1.IdAnexo = socionegocio.Idestado AND a1.CodEmpresa = socionegocio.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'anexos a2', 'on' => 'a2.IdAnexo = socionegocio.Idestado AND a2.CodEmpresa = socionegocio.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    'socionegocio.IdSocioN ASC'
                )[0];

                $socio_negocio_tipos = (new SocioNegocioXTipo())->getSocioNegocioXTipo($IdSocioN);

                $socio_negocio_tipo_array = array();

                foreach ($socio_negocio_tipos as $indice => $value) {
                    $socio_negocio_tipo_array[] = $value['CodTipoSN'];
                }

                $socio_negocio_banco = (new SocioNegocioBanco())->getSocioNegocioBanco($IdSocioN);

                $tipo_persona = (new TipoPersona())->getTipoPersona($socio_negocio['CodTipPer'], '', [], '', '')[0];

                $option_tipo_persona = '<option value="' . $tipo_persona['CodTipPer'] . '">' . $tipo_persona['DescPer'] . '</option>';

                $tipo_documento_identidad = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad($socio_negocio['CodTipoDoc'], '', [], '', '')[0];

                $option_tipo_documento_identidad = '<option data-tipo-dato="' . $tipo_documento_identidad['TipoDato'] . '" value="' . $tipo_documento_identidad['CodTipoDoc'] . '">' . $tipo_documento_identidad['DesDocumento'] . '</option>';

                $condicion = (new Anexo())->getAnexo($this->CodEmpresa, $socio_negocio['IdCondicion'], 2, '', '', [], '', '')[0];

                $option_condicion = '<option value="' . $condicion['IdAnexo'] . '">' . $condicion['DescAnexo'] . '</option>';

                $pais = (new Ubigeo())->getUbigeo('', '', [], 'codubigeo = ' . $socio_negocio['codubigeo'] . ' OR codubigeo = ' . substr($socio_negocio['codubigeo'], 0, 2), '')[0];

                $option_pais = '<option value="' . $pais['codubigeo'] . '">' . $pais['descubigeo'] . '</option>';

                $ubigeo = (new Ubigeo())->getUbigeoQuery($this->db, $socio_negocio['codubigeo'], '')[0];

                $option_ubigeo = '<option value="' . $ubigeo->id . '">' . htmlspecialchars($ubigeo->text, ENT_QUOTES) . '</option>';

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, $socio_negocio['Idestado'], 1, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $tipos_socio_negocio = (new TipoSocioNegocio())->getTipoSocioNegocio();

                $checkbox_tipos_socio_negocio = '';

                foreach ($tipos_socio_negocio as $indice => $valor) {
                    $checked = '';

                    if (in_array($valor->CodTipoSN, $socio_negocio_tipo_array)) $checked = 'checked';

                    $checkbox_tipos_socio_negocio .= '
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="tipo_socio_negocio[]" value="' . $valor->CodTipoSN . '" ' . $checked . '>' . $valor->DescTipoSN . '
                            </label>
                        </div>
                    ';
                }

                $vinculo = (new Ts27Vinculo())->getTs27Vinculo($socio_negocio['CodVinculo'] ?? '', '', [], '', '')[0];

                $option_vinculo = $socio_negocio['CodVinculo'] ? '<option value="' . $vinculo['CodVinculo'] . '">' . $vinculo['DescVinculo'] . '</option>' : '';

                $sexo = (new Anexo())->getAnexo($this->CodEmpresa, $socio_negocio['IdSexo'] ?? 0, 3, '', '', [], '', '')[0];

                $option_sexo = $socio_negocio['IdSexo'] ? '<option value="' . $sexo['IdAnexo'] . '">' . $sexo['DescAnexo'] . '</option>' : '';

                $tipo_documento_identidad_banco = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad($socionegocio['CodTipoDoc_Tele'] ?? '', '', [], '', '')[0];

                $option_tipo_documento_identidad_banco = $socio_negocio['CodTipoDoc_Tele'] ? '<option value="' . $tipo_documento_identidad_banco['CodTipoDoc'] . '">' . $tipo_documento_identidad_banco['DesDocumento'] . '</option>' : '';

                $bancos = (new Banco())->getBanco($this->CodEmpresa, '', '', '', [], '', '');

                $options_banco = '';

                foreach ($bancos as $indice => $valor) {
                    $options_banco .= '<option value="' . $valor['Codbanco'] . '">' . $valor['abreviatura'] . '</option>';
                }

                $tipo_cuentas = (new Anexo())->getAnexo($this->CodEmpresa, 0, 54, '02', '', [], '', '');

                $options_tipo_cuenta = '';

                foreach ($tipo_cuentas as $indice => $valor) {
                    $options_tipo_cuenta .= '<option value="' . $valor['IdAnexo'] . '">' . $valor['DescAnexo'] . '</option>';
                }

                $datos_ruc = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad(6, 'CodTipoDoc, N_tip', [], '', '');

                if (count($datos_ruc) == 0) {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => '6', 'N_tip' => 'F'];
                } else {
                    $datos_ruc = ['CodTipPer' => '02', 'CodTipoDoc' => $datos_ruc[0]['CodTipoDoc'], 'N_tip' => $datos_ruc[0]['N_tip']];
                }

                $datos_extranjero = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('-', 'CodTipoDoc, N_tip', [], '', '');

                if (count($datos_extranjero) == 0) {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => '-'];
                } else {
                    $datos_extranjero = ['CodTipPer' => '03', 'CodTipoDoc' => $datos_extranjero[0]['CodTipoDoc']];
                }

                $script = "
                    var id_banco = " . (count($socio_negocio_banco) + 1) . ";
                    var datos_ruc_CodTipPer = '" . $datos_ruc['CodTipPer'] . "';
                    var datos_ruc_CodTipoDoc = '" . $datos_ruc['CodTipoDoc'] . "';
                    var datos_ruc_N_tip = '" . $datos_ruc['N_tip'] . "';
                    var datos_extranjero_CodTipPer = '" . $datos_extranjero['CodTipPer'] . "';
                    var datos_extranjero_CodTipoDoc = '" . $datos_extranjero['CodTipoDoc'] . "';
                    var socio_negocio_ruc = '" . $socio_negocio['ruc'] . "';
                    var socio_negocio_docidentidad = '" . $socio_negocio['docidentidad'] . "';
                    var socio_negocio_razonsocial = '" . str_replace("'", "\'", $socio_negocio['razonsocial']) . "';
                ";

                $script = (new Empresa())->generar_script($script, ['app/mantenience/business_partner/edit.js']);

                return viewApp($this->page, 'app/mantenience/business_partner/edit', [
                    'codigo_socio_negocio' => $IdSocioN,
                    'socio_negocio' => $socio_negocio,
                    'socio_negocio_banco' => $socio_negocio_banco,
                    'option_tipo_persona' => $option_tipo_persona,
                    'option_tipo_documento_identidad' => $option_tipo_documento_identidad,
                    'option_condicion' => $option_condicion,
                    'option_pais' => $option_pais,
                    'option_ubigeo' => $option_ubigeo,
                    'option_estado' => $option_estado,
                    'checkbox_tipos_socio_negocio' => $checkbox_tipos_socio_negocio,
                    'option_vinculo' => $option_vinculo,
                    'option_sexo' => $option_sexo,
                    'option_tipo_documento_identidad_banco' => $option_tipo_documento_identidad_banco,
                    'options_banco' => $options_banco,
                    'options_tipo_cuenta' => $options_tipo_cuenta,
                    'typeOrder' => 'num',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
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
                $existe_socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'ruc = "' . $post['ruc'] . '" OR razonsocial = "' . $post['razonsocial'] . '"', '');
            } else if (!empty($post['docidentidad'])) {
                $existe_socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'docidentidad = "' . $post['docidentidad'] . '" OR razonsocial = "' . $post['razonsocial'] . '"', '');
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

                $IdSocioN = (new SocioNegocio())->agregar($post);

                if (count($tipos_socio_negocio) > 0) {
                    (new SocioNegocioXTipo())->eliminar($IdSocioN);

                    foreach ($tipos_socio_negocio as $indice => $valor) {
                        $data = [
                            'CodTipoSN' => $valor,
                            'IdSocioN' => $IdSocioN
                        ];

                        (new SocioNegocioXTipo())->agregar($data);
                    }
                } else {
                    (new SocioNegocioXTipo())->eliminar($IdSocioN);
                }

                if (count($codigos_banco) > 0) {
                    (new SocioNegocioBanco())->eliminar($IdSocioN);

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

                        (new SocioNegocioBanco())->agregar($data);
                    }
                } else {
                    (new SocioNegocioBanco())->eliminar($IdSocioN);
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
                $existe_socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'IdSocioN != ' . $IdSocioN . ' AND ruc != "' . $post['ruc'] . '" OR razonsocial = "' . $post['razonsocial'] . '"', '');
            } else {
                $existe_socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'IdSocioN != ' . $IdSocioN . ' AND docidentidad != "' . $post['docidentidad'] . '" OR razonsocial = "' . $post['razonsocial'] . '"', '');
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

                (new SocioNegocio())->actualizar($this->CodEmpresa, $IdSocioN, $post);

                if (count($tipos_socio_negocio) > 0) {
                    (new SocioNegocioXTipo())->eliminar($IdSocioN);

                    foreach ($tipos_socio_negocio as $indice => $valor) {
                        $data = [
                            'CodTipoSN' => $valor,
                            'IdSocioN' => $IdSocioN
                        ];

                        (new SocioNegocioXTipo())->agregar($data);
                    }
                } else {
                    (new SocioNegocioXTipo())->eliminar($IdSocioN);
                }

                if (count($codigos_banco) > 0) {
                    (new SocioNegocioBanco())->eliminar($IdSocioN);

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

                        (new SocioNegocioBanco())->agregar($data);
                    }
                } else {
                    (new SocioNegocioBanco())->eliminar($IdSocioN);
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

            (new SocioNegocio())->eliminar($this->CodEmpresa, $IdSocioN);

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

            $result = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, 'IdSocioN, ' . (new SocioNegocio())->getRazonSocial(false) . ' AS Cliente, ruc, docidentidad, telefono, direccion1', [], '', 'IdSocioN ASC');

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
            $result = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, 'IdSocioN, ' . (new SocioNegocio())->getRazonSocial(false) . ' AS Cliente, ruc, docidentidad, telefono, direccion1', [], '', 'IdSocioN ASC');

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
                $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'ruc = "' . $ruc . '"', '');

                $existe_duplicados = array('existe' => false, 'codigo' => '');

                if (!empty($ruc) && count($socio_negocio) > 0) {
                    $existe_duplicados = array('existe' => true, 'codigo' => $ruc);
                } else {
                    $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'docidentidad = "' . $docidentidad . '"', '');

                    if (!empty($docidentidad) && count($socio_negocio) > 0) {
                        $existe_duplicados = array('existe' => true, 'codigo' => $docidentidad);
                    } else {
                        $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'razonsocial = "' . $razonsocial . '"', '');

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

                $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'ruc = "' . $ruc . '" AND ruc != "' . $Notruc . '"', '');

                $existe_duplicados = array('existe' => false, 'codigo' => '');

                if (!empty($ruc) && count($socio_negocio) > 0) {
                    $existe_duplicados = array('existe' => true, 'codigo' => $ruc);
                } else {
                    $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'docidentidad = "' . $docidentidad . '" AND docidentidad != "' . $Notdocidentidad . '"', '');

                    if (!empty($docidentidad) && count($socio_negocio) > 0) {
                        $existe_duplicados = array('existe' => true, 'codigo' => $docidentidad);
                    } else {
                        $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'razonsocial = "' . $razonsocial . '" AND razonsocial != "' . $Notrazonsocial . '"', '');

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
        $acciones = $this->request->getGet('acciones');
        $items = (new SocioNegocio())->autoCompletado($busqueda, 2, $this->request->getCookie('empresa'));
        $botonCrear = $acciones['botonCrear'] ?? "0";
        if ($botonCrear == "1") {
            $text = (empty($busqueda)) ? 'Crear nuevo proveedor' : "Crear \"{$busqueda}\"";
            $items = array_merge($items, [
                [
                    'id' => 'C',
                    'text' => $text
                ]
            ]);
        }
        return $this->response->setJson($items);
    }

    public function autocompletado_()
    {
        try {
            $post = $this->request->getPost();

            $text = (new SocioNegocio())->getRazonSocial(true);

            if (isset($post['search'])) {
                $search = $post['search'];

                $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, 'IdSocioN AS id, ' . $text . ' AS text', [], $text . ' LIKE "%' . $search . '%"', '');
            } else {
                $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, 'IdSocioN AS id, ' . $text . ' AS text', [], '', '');
            }

            echo json_encode($socio_negocio);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
