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
