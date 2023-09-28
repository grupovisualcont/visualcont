<?php

namespace App\Controllers;

use App\Models\ActivoFijo;
use App\Models\Amarre;
use App\Models\Anexo;
use App\Models\Banco;
use App\Models\CentroCosto;
use App\Models\Cheque;
use App\Models\ClaseDoc;
use App\Models\ConceptoPres;
use App\Models\CondicionPago;
use App\Models\Documento;
use App\Models\EntidadFinanciera;
use App\Models\I_AnexoSunat;
use App\Models\Moneda;
use App\Models\PlanContable;
use App\Models\PlanContableEquiv;
use App\Models\Predeterminado;
use App\Models\SocioNegocio;
use App\Models\SocioNegocioBanco;
use App\Models\SocioNegocioXTipo;
use App\Models\TipoActivo;
use App\Models\TipoCambio;
use App\Models\TipoComprobante;
use App\Models\TipoDocumentoIdentidad;
use App\Models\TipoPersona;
use App\Models\TipoSocioNegocio;
use App\Models\TipoVoucherCab;
use App\Models\TipoVoucherDet;
use App\Models\Ts27Vinculo;
use App\Models\Ubigeo;
use App\Models\VoucherPres;

session_start();

class Mantenimiento extends BaseController
{
    // Inicio

    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    // Socio de Negocio

    protected $socioNegocioModel;
    protected $tipoPersonaModel;
    protected $tipoDocumentoIdentidadModel;
    protected $ubigeoModel;
    protected $anexoModel;
    protected $tipoSocioNegocioModel;
    protected $socioNegocioXTipoModel;
    protected $ts27VinculoModel;
    protected $socioNegocioBancoModel;
    protected $predeterminadoModel;

    // Plan Contable

    protected $amarreModel;
    protected $planContableEquivModel;

    // Tipo de Vouchers

    protected $tipoVoucherCabModel;
    protected $tipoVoucherDetModel;
    protected $centroCostoModel;
    protected $activoFijoModel;

    // Caja - Banco

    protected $cajaBancoModel;
    protected $entidadFinancieraModel;
    protected $planContableModel;
    protected $monedaModel;
    protected $chequeModel;

    // Activos Fijos

    protected $tipoActivoModel;
    protected $i_AnexoSunatModel;

    // Comprobantes de Pago

    protected $documentoModel;
    protected $claseDocModel;
    protected $tipoComprobanteModel;

    // Presupuesto

    protected $conceptoPresModel;
    protected $voucherPresModel;

    // Condición de Pago

    protected $condicionPagoModel;

    // Tipo de Cambio

    protected $tipoCambioModel;

    public function __construct()
    {
        // Inicio

        $this->page = 'Mantenimiento';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        // Socio de Negocio

        $this->socioNegocioModel = new SocioNegocio();
        $this->tipoPersonaModel = new TipoPersona();
        $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();
        $this->ubigeoModel = new Ubigeo();
        $this->anexoModel = new Anexo();
        $this->tipoSocioNegocioModel = new TipoSocioNegocio();
        $this->socioNegocioXTipoModel = new SocioNegocioXTipo();
        $this->ts27VinculoModel = new Ts27Vinculo();
        $this->socioNegocioBancoModel = new SocioNegocioBanco();
        $this->predeterminadoModel = new Predeterminado();

        // Plan Contable

        $this->amarreModel = new Amarre();
        $this->planContableEquivModel = new PlanContableEquiv();

        // Tipo de Vouchers

        $this->tipoVoucherCabModel = new TipoVoucherCab();
        $this->tipoVoucherDetModel = new TipoVoucherDet();
        $this->centroCostoModel = new CentroCosto();
        $this->activoFijoModel = new ActivoFijo();

        // Caja - Banco

        $this->cajaBancoModel = new Banco();
        $this->entidadFinancieraModel = new EntidadFinanciera();
        $this->planContableModel = new PlanContable();
        $this->monedaModel = new Moneda();
        $this->chequeModel = new Cheque();

        // Activos Fijos

        $this->tipoActivoModel = new TipoActivo();
        $this->i_AnexoSunatModel = new I_AnexoSunat();

        // Comprobantes de Pago

        $this->documentoModel = new Documento();
        $this->claseDocModel = new ClaseDoc();
        $this->tipoComprobanteModel = new TipoComprobante();

        // Presupuesto

        $this->conceptoPresModel = new ConceptoPres();
        $this->voucherPresModel = new VoucherPres();

        // Condición de Pago

        $this->condicionPagoModel = new CondicionPago();

        // Tipo de Cambio

        $this->tipoCambioModel = new TipoCambio();
    }

    // Socio de Negocio

    public function socio_negocio()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->socioNegocioModel = new SocioNegocio();

                $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa);

                return viewApp($this->page, 'app/mantenimiento/socio_negocio/index', [
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

    public function socio_negocio_nuevo()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->predeterminadoModel = new Predeterminado();

                $predeterminados = $this->predeterminadoModel->getPredeterminado('CodTipPer_sn, CodTipoDoc_sn, IdCondicion_sn, CodUbigeo_sn, IdEstadoSN');

                $this->tipoPersonaModel = new TipoPersona();

                $tipos_persona = $this->tipoPersonaModel->getTipoPersona();

                $options_tipos_persona = '';

                foreach ($tipos_persona as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodTipPer'] == $predeterminados['CodTipPer_sn']) $selected = 'selected';

                    $options_tipos_persona .= '<option value="' . $valor['CodTipPer'] . '" ' . $selected . '>' . $valor['DescPer'] . '</option>';
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $tipos_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad();

                $options_tipos_documento_identidad = '';

                foreach ($tipos_documento_identidad as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodTipoDoc'] == $predeterminados['CodTipoDoc_sn']) $selected = 'selected';

                    $options_tipos_documento_identidad .= '<option data-tipo-dato="' . $valor['TipoDato'] . '" value="' . $valor['CodTipoDoc'] . '" ' . $selected . '>' . $valor['DesDocumento'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $condiciones = $this->anexoModel->getAnexoByTipoAnexo($this->CodEmpresa, 2, '', '');

                $options_condiciones = '';

                foreach ($condiciones as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $predeterminados['IdCondicion_sn']) $selected = 'selected';

                    $options_condiciones .= '<option data-descripcion="' . $valor['DescAnexo'] . '" value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

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

                    if ($valor->codubigeo == $predeterminados['CodUbigeo_sn']) $selected = 'selected';

                    $options_ubigeos .= '<option value="' . $valor->codubigeo . '" ' . $selected . '>' . htmlspecialchars($valor->descubigeo, ENT_QUOTES) . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexoByTipoAnexo($this->CodEmpresa, 1, '', '');

                $options_estados = '';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $predeterminados['IdEstadoSN']) $selected = 'selected';

                    $options_estados .= '<option data-descripcion="' . $valor['DescAnexo'] . '" value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->tipoSocioNegocioModel = new TipoSocioNegocio();

                $tipos_socio_negocio = $this->tipoSocioNegocioModel->getTipoSocioNegocio();

                $checkbox_tipos_socio_negocio = '';

                foreach ($tipos_socio_negocio as $indice => $valor) {
                    $checkbox_tipos_socio_negocio .= '
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="tipo_socio_negocio[]" value="' . $valor['CodTipoSN'] . '">' . $valor['DescTipoSN'] . '
                            </label>
                        </div>
                    ';
                }

                $this->ts27VinculoModel = new Ts27Vinculo();

                $vinculos = $this->ts27VinculoModel->getTs27Vinculo();

                $options_vinculos = '';

                foreach ($vinculos as $indice => $valor) {
                    $options_vinculos .= '<option value="' . $valor['CodVinculo'] . '">' . $valor['DescVinculo'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $sexos = $this->anexoModel->getAnexoByTipoAnexo($this->CodEmpresa, 3, '', '');

                $options_sexos = '<option value="" disabled selected>Seleccione</option>';

                foreach ($sexos as $indice => $valor) {
                    $options_sexos .= '<option value="' . $valor['IdAnexo'] . '">' . $valor['DescAnexo'] . '</option>';
                }

                $this->tipoDocumentoIdentidadModel = new TipoDocumentoIdentidad();

                $tipos_documento_identidad_bancos = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidadBanco();

                $options_tipos_documento_identidad_bancos = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipos_documento_identidad_bancos as $indice => $valor) {
                    $options_tipos_documento_identidad_bancos .= '<option value="' . $valor['CodTipoDoc'] . '">' . $valor['DesDocumento'] . '</option>';
                }

                $this->cajaBancoModel = new Banco();

                $bancos = $this->cajaBancoModel->getBanco($this->CodEmpresa, 'Codbanco, abreviatura');

                $options_banco = '<option value="" disabled selected>Seleccione</option>';

                foreach ($bancos as $indice => $valor) {
                    $options_banco .= '<option value="' . $valor['Codbanco'] . '">' . $valor['Codbanco'] . ' - ' . $valor['abreviatura'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $tipos_cuenta = $this->anexoModel->getAnexoByTipoAnexo($this->CodEmpresa, 54, '02', 'CodInterno ASC');

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
                    var datos_ruc_CodTipPer = '" . $datos_ruc['CodTipPer'] . "';
                    var datos_ruc_CodTipoDoc = '" . $datos_ruc['CodTipoDoc'] . "';
                    var datos_ruc_N_tip = '" . $datos_ruc['N_tip'] . "';
                    var datos_extranjero_CodTipPer = '" . $datos_extranjero['CodTipPer'] . "';
                    var datos_extranjero_CodTipoDoc = '" . $datos_extranjero['CodTipoDoc'] . "';
                    var options_banco = '" . $options_banco . "';
                    var options_tipo_cuenta = '" . $options_tipo_cuenta . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenimiento/socio_negocio/create.js']);

                return viewApp($this->page, 'app/mantenimiento/socio_negocio/create', [
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
                    'datos_extranjero' => $datos_extranjero,
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

    public function socio_negocio_eliminar($IdSocioN)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->socioNegocioModel = new SocioNegocio();

            $this->socioNegocioModel->eliminarSocioNegocio($this->CodEmpresa, $IdSocioN);

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

            return redirect()->to(base_url('app/mantenimiento/socio_negocio/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function socio_negocio_reporte_excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Socio de Negocio - Reporte');

            $columnas = array('Código', 'Cliente', 'RUC', 'DocIdentidad', 'Teléfono', 'Dirección');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->socioNegocioModel = new SocioNegocio();

            $result = $this->socioNegocioModel->getSocioNegocioExcel($this->CodEmpresa);

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

    public function socio_negocio_reporte_pdf()
    {
        try {
            $this->socioNegocioModel = new SocioNegocio();

            $result = $this->socioNegocioModel->getSocioNegocioPDF($this->CodEmpresa);

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
}
