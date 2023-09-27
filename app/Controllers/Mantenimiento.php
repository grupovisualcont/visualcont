<?php

namespace App\Controllers;

use App\Models\SocioNegocio;

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

        $this->socioNegocioModel = model('SocioNegocio');
        $this->tipoPersonaModel = model('TipoPersona');
        $this->tipoDocumentoIdentidadModel = model('TipoDocumentoIdentidad');
        $this->ubigeoModel = model('Ubigeo');
        $this->anexoModel = model('Anexo');
        $this->tipoSocioNegocioModel = model('TipoSocioNegocio');
        $this->socioNegocioXTipoModel = model('SocioNegocioXTipo');
        $this->ts27VinculoModel = model('Ts27Vinculo');
        $this->socioNegocioBancoModel = model('SocioNegocioBanco');

        // Plan Contable

        $this->amarreModel = model('Amarre');
        $this->planContableEquivModel = model('PlanContableEquiv');

        // Tipo de Vouchers

        $this->tipoVoucherCabModel = model('TipoVoucherCab');
        $this->tipoVoucherDetModel = model('TipoVoucherDet');
        $this->centroCostoModel = model('CentroCosto');
        $this->activoFijoModel = model('ActivoFijo');

        // Caja - Banco

        $this->cajaBancoModel = model('Banco');
        $this->entidadFinancieraModel = model('EntidadFinanciera');
        $this->planContableModel = model('PlanContable');
        $this->monedaModel = model('Moneda');
        $this->chequeModel = model('Cheque');

        // Activos Fijos

        $this->tipoActivoModel = model('TipoActivo');
        $this->i_AnexoSunatModel = model('I_AnexoSunat');

        // Comprobantes de Pago

        $this->documentoModel = model('Documento');
        $this->claseDocModel = model('ClaseDoc');
        $this->tipoComprobanteModel = model('TipoComprobante');

        // Presupuesto

        $this->conceptoPresModel = model('ConceptoPres');
        $this->voucherPresModel = model('VoucherPres');

        // Condición de Pago

        $this->condicionPagoModel = model('CondicionPago');

        // Tipo de Cambio

        $this->tipoCambioModel = model('TipoCambio');
    }

    // Socio de Negocio

    public function socio_negocio()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->socioNegocioModel = new SocioNegocio();

                $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa);

                $data = $this->empresa->menu($this->page);

                $data['socio_negocio'] = $socio_negocio;
                $data['typeOrder'] = 'num';

                return view('app/mantenimiento/socio_negocio/index', $data);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
