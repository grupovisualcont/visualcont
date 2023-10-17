<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Amarre;
use App\Models\Anexo;
use App\Models\CondicionPago;
use App\Models\Documento;
use App\Models\HistoricoImp;
use App\Models\Moneda;
use App\Models\MovimientoCab;
use App\Models\MovimientoDet;
use App\Models\MovimientoObs;
use App\Models\PlanContable;
use App\Models\Predeterminado;
use App\Models\SaldoDet;
use App\Models\SocioNegocio;
use App\Models\SocioNegocioXTipo;
use App\Models\TipoCambio;
use App\Models\TipoDocumentoIdentidad;
use App\Models\TipoPago;
use App\Models\TipoPersona;
use App\Models\TipoSocioNegocio;
use App\Models\TipoVoucherCab;
use App\Models\TipoVoucherDet;

class Ventas extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Ventas';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                    $this->CodEmpresa,
                    0,
                    'movimientocab.*, tvcab.DescVoucher',
                    [
                        array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = movimientocab.CodTV AND tvcab.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                    ],
                    [
                        array('Periodo' => date('Y'), 'Mes' => date('m'), 'Origen' => array('VEN', 'IMPORVEN'))
                    ],
                    '',
                    '',
                    ''
                );

                foreach ($movimiento_cab as $indice => $valor) {
                    $movimiento_cab_referencia = (new MovimientoCab())->getMovimientoCab(
                        $this->CodEmpresa,
                        0,
                        'IdMov, IdMovRef',
                        [],
                        [array('IdMovRef' => $valor['IdMov'])],
                        'IdMov !=' . $valor['IdMov'],
                        '',
                        ''
                    );

                    if (count($movimiento_cab_referencia) > 0) $movimiento_cab[$indice]['IdMovRef'] = $movimiento_cab_referencia[0]['IdMov'];

                    $movimiento_cab_aplicacion = (new MovimientoCab())->getMovimientoCab(
                        $this->CodEmpresa,
                        0,
                        'IdMov, IdMovAplica',
                        [],
                        [array('IdMovAplica' => $valor['IdMov'])],
                        'IdMov !=' . $valor['IdMov'],
                        '',
                        ''
                    );

                    if (count($movimiento_cab_aplicacion) > 0) $movimiento_cab[$indice]['IdMovAplica'] = $movimiento_cab_aplicacion[0]['IdMov'];
                }

                $script = (new Empresa())->generar_script(['app/movements/sales/index.js']);

                return viewApp($this->page, 'app/movements/sales/index', [
                    'page' => $this->page,
                    'movimiento_cab' => $movimiento_cab,
                    'typeOrder' => 'string',
                    'script' => $script
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
                $predeterminado = (new Predeterminado())->getPredeterminado('CodTV_ve, CodDocumento_ve, TipoOperacion_ve, CodMoneda_ve');

                $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, $predeterminado['CodTV_ve'], 0, '', [], '', '')[0];

                $option_tipo_voucher = '<option data-tipo="' . $tipo_voucher_cab['Tipo'] . '" value="' . $tipo_voucher_cab['CodTV'] . '">' . $tipo_voucher_cab['CodTV'] . ' - ' . $tipo_voucher_cab['DescVoucher'] . '</option>';

                $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                    $this->CodEmpresa,
                    0,
                    'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                    [],
                    [
                        array('Periodo' => date('Y'), 'Mes' => date('m'), 'Origen' => array('VEN', 'IMPORVEN'))
                    ],
                    '',
                    '',
                    ''
                );

                $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . '000001';

                if ($movimiento_cab[0]['codigo']) {
                    $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                    if (strlen($movimiento_cab[0]['codigo']) == 1) {
                        $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                    } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                        $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                    } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                        $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . '000' . $movimiento_cab[0]['codigo'];
                    } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                        $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . '00' . $movimiento_cab[0]['codigo'];
                    } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                        $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . '0' . $movimiento_cab[0]['codigo'];
                    } else {
                        $codigo_voucher_maximo = $predeterminado['CodTV_ve'] . date('m') . $movimiento_cab[0]['codigo'];
                    }
                }

                $documento = (new Documento())->getDocumento($this->CodEmpresa, $predeterminado['CodDocumento_ve'], 'VE', '', [array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left')], '', 'documento.DescDocumento ASC')[0];

                $tipo_dato = explode('|', $documento['TipoDatoS']);
                $longitud = isset($tipo_dato[2]) ? $tipo_dato[2] : '';
                $serie = isset($tipo_dato[3]) ? $tipo_dato[3] : '';
                $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                $option_documento = '<option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $documento['CodDocumento'] . '">' . $documento['CodDocumento'] . ' - ' . $documento['DescDocumento'] . '</option>';

                $facturas = (new Documento())->getDocumento($this->CodEmpresa, '', 'VE', 'CodDocumento', [], 'CodSunat = "01"', 'DescDocumento ASC');

                $notas_credito = (new Documento())->getDocumento($this->CodEmpresa, '', 'VE', 'CodDocumento', [], 'CodSunat = "07"', 'DescDocumento ASC');

                $moneda = (new Moneda())->getMoneda($predeterminado['CodMoneda_ve'], '', [], '', '')[0];

                $option_moneda = '<option value="' . $moneda['CodMoneda'] . '">' . $moneda['DescMoneda'] . '</option>';

                $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', '', [], 'Tipo = 168', '')[0];

                $option_condicion_pago_credito = '<option value="' . $condicion_pago['codcondpago'] . '">' . $condicion_pago['desccondpago'] . '</option>';

                $tipo_cambio = (new Empresa())->consulta_tipo_cambio();

                $tipo_operacion = (new Anexo())->getAnexo($this->CodEmpresa, $predeterminado['TipoOperacion_ve'], 0, '', '', [], '', '')[0];

                $option_tipo_operacion = '<option value="' . $tipo_operacion['IdAnexo'] . '">' . $tipo_operacion['DescAnexo'] . '</option>';

                $tipo_persona = (new TipoPersona())->getTipoPersona('01', '', [], '', '')[0];

                $option_tipo_persona = '<option value="' . $tipo_persona['CodTipPer'] . '">' . $tipo_persona['DescPer'] . '</option>';

                $tipo_documento_identidad = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('1', '', [], '', '')[0];

                $option_tipo_documento_identidad = '<option data-tipo-dato="' . $tipo_documento_identidad['TipoDato'] . '" value="' . $tipo_documento_identidad['CodTipoDoc'] . '">' . $tipo_documento_identidad['DesDocumento'] . '</option>';

                $condicion = (new Anexo())->getAnexo($this->CodEmpresa, 0, 2, '', '', [], 'CodInterno = 0', '')[0];

                $option_condicion = '<option data-descripcion="' . $condicion['DescAnexo'] . '" value="' . $condicion['IdAnexo'] . '">' . $condicion['DescAnexo'] . '</option>';

                $datos_ruc = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('6', 'CodTipoDoc, N_tip', [], '', '');

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

                $script = (new Empresa())->generar_script(['app/movements/sales/create.js']);

                return viewApp($this->page, 'app/movements/sales/create', [
                    'codigo_voucher_maximo' => $codigo_voucher_maximo,
                    'datos_ruc' => $datos_ruc,
                    'datos_extranjero' => $datos_extranjero,
                    'facturas' => $facturas,
                    'notas_credito' => $notas_credito,
                    'option_tipo_voucher' => $option_tipo_voucher,
                    'codigo_voucher_maximo' => $codigo_voucher_maximo,
                    'option_documento' => $option_documento,
                    'option_condicion_pago_credito' => $option_condicion_pago_credito,
                    'option_moneda' => $option_moneda,
                    'tipo_cambio_venta' => $tipo_cambio->venta,
                    'option_tipo_operacion' => $option_tipo_operacion,
                    'option_tipo_persona' => $option_tipo_persona,
                    'option_tipo_documento_identidad' => $option_tipo_documento_identidad,
                    'option_condicion' => $option_condicion,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($IdMov)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, 'IdMov', [], [array('IdMovRef' => $IdMov)], '', '', '');

                $IdMovRef = '';

                if (count($movimiento_cab) > 0) {
                    $IdMovRef = $movimiento_cab[0]['IdMov'];
                }

                $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                    $this->CodEmpresa,
                    $IdMov,
                    '',
                    [
                        array('tabla' => 'movimientodet movdet', 'on' => 'movdet.IdMov = movimientocab.IdMov AND movdet.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'moneda mon', 'on' => 'mon.CodMoneda = movdet.CodMoneda', 'tipo' => 'inner'),
                        array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = movimientocab.CodTV AND tvcab.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                    ],
                    [],
                    'movdet.IdSocioN IS NOT NULL',
                    '',
                    'movdet.IdMovDet ASC'
                )[0];

                $editar_detalles = $this->editar_detalles($IdMov); 

                $movimiento_det = $editar_detalles['tr'];

                $Referencia = $editar_detalles['Referencia'];

                $Importado = $editar_detalles['Importado'];

                $movimiento_det_referencias = $this->editar_detalles_referencias($IdMov);

                $movimiento_cab_banco = (new MovimientoCab())->getMovimientoCab(
                    $this->CodEmpresa,
                    0,
                    '',
                    [
                        array('tabla' => 'movimientodet movdet', 'on' => 'movdet.IdMov = movimientocab.IdMov AND movdet.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'moneda mon', 'on' => 'mon.CodMoneda = movdet.CodMoneda', 'tipo' => 'left')
                    ],
                    [
                        array('IdMovRef' => $IdMov)
                    ],
                    'movdet.Parametro = "BANCO" AND movdet.CodTipoPago IS NOT NULL',
                    '',
                    ''
                );

                $movimiento_cab_referencia = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, 'IdMov', [], [array('IdMovAplica' => $IdMov)], '', '', '');

                $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, $movimiento_cab['CodTV'], array(1, 2), '', [], '', 'DescVoucher ASC')[0];

                $option_tipo_voucher = '<option data-tipo="' . $tipo_voucher_cab['Tipo'] . '" value="' . $tipo_voucher_cab['CodTV'] . '">' . $tipo_voucher_cab['CodTV'] . ' - ' . $tipo_voucher_cab['DescVoucher'] . '</option>';

                $socio_negocio = (new SocioNegocio())->getSocioNegocio(
                    $this->CodEmpresa,
                    $movimiento_cab['IdSocioN'],
                    'IdSocioN, ' . (new SocioNegocio())->getNumeroDocumento() . ' AS numero_documento, ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razon_social',
                    [],
                    '',
                    ''
                )[0];

                $option_socio_negocio = '<option data-numero-documento="' . $socio_negocio['numero_documento'] . '" data-razon-social="' . $socio_negocio['razon_social'] . '" value="' . $socio_negocio['IdSocioN'] . '">' . !empty($socio_negocio['numero_documento']) ? $socio_negocio['numero_documento'] . ' - ' . $socio_negocio['razon_social'] : $socio_negocio['razon_social'] . '</option>';

                $documento = (new Documento())->getDocumento($this->CodEmpresa, $movimiento_cab['CodDocumento'], 'VE', '', [array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left')], '', 'documento.DescDocumento ASC')[0];

                $tipo_dato = explode('|', $documento['TipoDatoS']);
                $longitud = isset($tipo_dato[2]) ? $tipo_dato[2] : '';
                $serie = isset($tipo_dato[3]) ? $tipo_dato[3] : '';
                $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                $option_documento = '<option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $documento['CodDocumento'] . '">' . $documento['CodDocumento'] . ' - ' . $documento['DescDocumento'] . '</option>';

                $facturas = (new Documento())->getDocumento($this->CodEmpresa, '', 'VE', 'CodDocumento', [], 'CodSunat = "01"', 'DescDocumento ASC');

                $notas_credito = (new Documento())->getDocumento($this->CodEmpresa, '', 'VE', 'CodDocumento', [], 'CodSunat = "07"', 'DescDocumento ASC');

                $moneda = (new Moneda())->getMoneda($movimiento_cab['CodMoneda'], '', [], '', '')[0];

                $option_moneda = '<option value="' . $moneda['CodMoneda'] . '">' . $moneda['DescMoneda'] . '</option>';

                $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, $movimiento_cab['CodCondPago'], '', [], '', '')[0];

                $option_condicion_pago = '<option value="' . $condicion_pago['codcondpago'] . '">' . $condicion_pago['desccondpago'] . '</option>';

                $tipo_cambio = (new Empresa())->consulta_tipo_cambio();

                $tipo_operacion = (new Anexo())->getAnexo($this->CodEmpresa, $movimiento_cab['TipoOperacion'], 5, '', '', [], '', '')[0];

                $option_tipo_operacion = '<option value="' . $tipo_operacion['IdAnexo'] . '">' . $tipo_operacion['DescAnexo'] . '</option>';

                $tipo_persona = (new TipoPersona())->getTipoPersona('01', '', [], '', '')[0];

                $option_tipo_persona = '<option value="' . $tipo_persona['CodTipPer'] . '">' . $tipo_persona['DescPer'] . '</option>';

                $tipo_documento_identidad = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('1', '', [], '', '')[0];

                $option_tipo_documento_identidad = '<option data-tipo-dato="' . $tipo_documento_identidad['TipoDato'] . '" value="' . $tipo_documento_identidad['CodTipoDoc'] . '">' . $tipo_documento_identidad['DesDocumento'] . '</option>';

                $condicion = (new Anexo())->getAnexo($this->CodEmpresa, 0, 2, '', '', [], 'CodInterno = 0', '')[0];

                $option_condicion = '<option data-descripcion="' . $condicion['DescAnexo'] . '" value="' . $condicion['IdAnexo'] . '">' . $condicion['DescAnexo'] . '</option>';

                $forma_pago_contado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 6, '', '', [], count($movimiento_cab_banco) > 0 ? 'DescAnexo = "' . $movimiento_cab_banco[0]['Parametro'] . '"' : 'CodInterno = 1', '')[0];

                $option_forma_pago_contado = '<option data-codigo-interno="' . $forma_pago_contado['CodInterno'] . '" value="' . $forma_pago_contado['IdAnexo'] . '">' . $forma_pago_contado['DescAnexo'] . '</option>';

                $option_forma_pago_credito = '<option value="NINGUNO" selected>NINGUNO</option>';

                $option_forma_pago = $movimiento_cab['Tipo'] == 1 ? $option_forma_pago_contado : $option_forma_pago_credito;

                $datos_ruc = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('6', 'CodTipoDoc, N_tip', [], '', '');

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

                $plan_contable = count($movimiento_cab_banco) > 0 ? (new PlanContable())->getPlanContable($this->CodEmpresa, '', $movimiento_cab_banco[0]['CodCuenta'], '', [], '', '')[0] : '';

                $option_plan_contable = count($movimiento_cab_banco) > 0 ? '<option value="' . $plan_contable['CodCuenta'] . '">' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>' : '';

                $descripcion_plan_contable = count($movimiento_cab_banco) > 0 ? $plan_contable['DescCuenta'] : '';

                $tipo_pago = count($movimiento_cab_banco) > 0 ? (new TipoPago())->getTipoPago($movimiento_cab_banco[0]['CodTipoPago'], '', [], '', '')[0] : '';

                $option_tipo_pago = count($movimiento_cab_banco) > 0 ? '<option value="' . $tipo_pago['CodTipoPago'] . '">' . $tipo_pago['DescTipoPago'] . '</option>' : '';

                $script = (new Empresa())->generar_script(['app/movements/sales/edit.js']);

                return viewApp($this->page, 'app/movements/sales/edit', [
                    'movimiento_cab' => $movimiento_cab,
                    'IdMovRef' => $IdMovRef,
                    'movimiento_cab_banco' => $movimiento_cab_banco,
                    'movimiento_det' => $movimiento_det,
                    'movimiento_det_referencias' => $movimiento_det_referencias,
                    'movimiento_cab_referencia' => $movimiento_cab_referencia,
                    'option_tipo_voucher' => $option_tipo_voucher,
                    'option_socio_negocio' => $option_socio_negocio,
                    'option_documento' => $option_documento,
                    'option_condicion_pago' => $option_condicion_pago,
                    'option_moneda' => $option_moneda,
                    'tipo_cambio' => $tipo_cambio->venta,
                    'option_tipo_operacion' => $option_tipo_operacion,
                    'option_forma_pago' => $option_forma_pago,
                    'option_tipo_persona' => $option_tipo_persona,
                    'option_tipo_documento_identidad' => $option_tipo_documento_identidad,
                    'option_condicion' => $option_condicion,
                    'option_plan_contable' => $option_plan_contable,
                    'descripcion_plan_contable' => $descripcion_plan_contable,
                    'option_tipo_pago' => $option_tipo_pago,
                    'datos_ruc' => $datos_ruc,
                    'datos_extranjero' => $datos_extranjero,
                    'Referencia' => $Referencia,
                    'Importado' => $Importado,
                    'facturas' => $facturas,
                    'notas_credito' => $notas_credito,
                    'typeOrder' => 'string',
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

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                $post['CodEmpresa'],
                0,
                'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                [],
                [
                    array('Origen' => array('VEN', 'IMPORVEN'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                ],
                '',
                '',
                ''
            );

            $codigo_voucher_maximo = 'VEN' . date('m') . '000001';

            if ($movimiento_cab[0]['codigo']) {
                $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                if (strlen($movimiento_cab[0]['codigo']) == 1) {
                    $codigo_voucher_maximo = 'VEN' . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                    $codigo_voucher_maximo = 'VEN' . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                    $codigo_voucher_maximo = 'VEN' . date('m') . '000' . $movimiento_cab[0]['codigo'];
                } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                    $codigo_voucher_maximo = 'VEN' . date('m') . '00' . $movimiento_cab[0]['codigo'];
                } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                    $codigo_voucher_maximo = 'VEN' . date('m') . '0' . $movimiento_cab[0]['codigo'];
                } else {
                    $codigo_voucher_maximo = 'VEN' . date('m') . $movimiento_cab[0]['codigo'];
                }
            }

            $post['Codmov'] = $codigo_voucher_maximo;

            $post['Periodo'] = date('Y');
            $post['Mes'] = date('m');
            $post['Origen'] = 'VEN';
            $post['Glosa'] = strtoupper(trim($post['Glosa']));
            $post['Detraccion'] = isset($post['Detraccion']) ? $post['Detraccion'] : 0;

            $post_detalles = array();
            $post_banco = array();
            $post_referencias = array();

            $post['FecContable'] = !empty($post['FecContable']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['FecContable']))) : NULL;

            foreach ($post as $indice => $valor) {
                if (is_array($valor) && strpos($indice, '_Referencia') == FALSE) {
                    $post_detalles[$indice] = $post[$indice];

                    unset($post[$indice]);
                } else if (is_array($valor) && strpos($indice, '_Referencia') == TRUE) {
                    $post_referencias[$indice] = $post[$indice];

                    unset($post[$indice]);
                }

                if (!empty($post['Banco']) && $indice == 'Banco') {
                    parse_str($valor, $post_banco);

                    unset($post[$indice]);
                }
            }

            if (empty($post['Banco'])) {
                unset($post['Banco']);
            }

            if (count($post_detalles['NumItem']) > 0) {
                $BaseImpSunat_Debe_S = 0;
                $Inafecto_Debe_S = 0;
                $Exonerado_Debe_S = 0;
                $ISC_Debe_S = 0;
                $IGVSunat_Debe_S = 0;
                $Percepcion_Debe_S = 0;
                $OtroTributo_Debe_S = 0;
                $Retencion4_Debe_S = 0;
                $Total_Debe_S = 0;
                $Descuento_Debe_S = 0;
                $Anticipo_Debe_S = 0;
                $Icbp_Debe_S = 0;

                $BaseImpSunat_Haber_S = 0;
                $Inafecto_Haber_S = 0;
                $Exonerado_Haber_S = 0;
                $ISC_Haber_S = 0;
                $IGVSunat_Haber_S = 0;
                $Percepcion_Haber_S = 0;
                $OtroTributo_Haber_S = 0;
                $Retencion4_Haber_S = 0;
                $Total_Haber_S = 0;
                $Descuento_Haber_S = 0;
                $Anticipo_Haber_S = 0;
                $Icbp_Haber_S = 0;

                $BaseImpSunat_Debe_D = 0;
                $Inafecto_Debe_D = 0;
                $Exonerado_Debe_D = 0;
                $ISC_Debe_D = 0;
                $IGVSunat_Debe_D = 0;
                $Percepcion_Debe_D = 0;
                $OtroTributo_Debe_D = 0;
                $Retencion4_Debe_D = 0;
                $Total_Debe_D = 0;
                $Descuento_Debe_D = 0;
                $Anticipo_Debe_D = 0;
                $Icbp_Debe_D = 0;

                $BaseImpSunat_Haber_D = 0;
                $Inafecto_Haber_D = 0;
                $Exonerado_Haber_D = 0;
                $ISC_Haber_D = 0;
                $IGVSunat_Haber_D = 0;
                $Percepcion_Haber_D = 0;
                $OtroTributo_Haber_D = 0;
                $Retencion4_Haber_D = 0;
                $Total_Haber_D = 0;
                $Descuento_Haber_D = 0;
                $Anticipo_Haber_D = 0;
                $Icbp_Haber_D = 0;

                $ValorTC = 0;

                foreach ($post_detalles['NumItem'] as $indice => $valor) {
                    $ValorTC = $post_detalles['ValorTC'][$indice];

                    switch ($post_detalles['Parametro'][$indice]) {
                        case 'AFECTO':
                            $BaseImpSunat_Debe_S += $post_detalles['DebeSol'][$indice];
                            $BaseImpSunat_Haber_S += $post_detalles['HaberSol'][$indice];
                            $BaseImpSunat_Debe_D += $post_detalles['DebeDol'][$indice];
                            $BaseImpSunat_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'ANTICIPO':
                            $Anticipo_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Anticipo_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Anticipo_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Anticipo_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'DESCUENTO':
                            $Descuento_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Descuento_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Descuento_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Descuento_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'IGV':
                            $IGVSunat_Debe_S += $post_detalles['DebeSol'][$indice];
                            $IGVSunat_Haber_S += $post_detalles['HaberSol'][$indice];
                            $IGVSunat_Debe_D += $post_detalles['DebeDol'][$indice];
                            $IGVSunat_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'PERCEPCION':
                            $Percepcion_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Percepcion_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Percepcion_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Percepcion_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'ISC':
                            $ISC_Debe_S += $post_detalles['DebeSol'][$indice];
                            $ISC_Haber_S += $post_detalles['HaberSol'][$indice];
                            $ISC_Debe_D += $post_detalles['DebeDol'][$indice];
                            $ISC_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'INAFECTO':
                            $Inafecto_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Inafecto_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Inafecto_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Inafecto_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'EXONERADO':
                            $Exonerado_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Exonerado_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Exonerado_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Exonerado_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'TOTAL':
                            $Total_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Total_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Total_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Total_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'OTRO TRIBUTO':
                            $OtroTributo_Debe_S += $post_detalles['DebeSol'][$indice];
                            $OtroTributo_Haber_S += $post_detalles['HaberSol'][$indice];
                            $OtroTributo_Debe_D += $post_detalles['DebeDol'][$indice];
                            $OtroTributo_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'ICBP':
                            $Icbp_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Icbp_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Icbp_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Icbp_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                    }
                }

                $post['ValorTC'] = $ValorTC;

                if ($post_detalles['CodMoneda'][0] == 'MO001') {
                    $post['TotalDol'] = 0;
                    $post['ValorTC'] = 0;
                    $post['FlagInterno'] = 0;
                } else if ($post_detalles['CodMoneda'][0] == 'MO002') {
                    $post['TotalSol'] = 0;
                    $post['ValorTC'] = 0;
                    $post['FlagInterno'] = 0;
                }

                $IdMov = (new MovimientoCab())->agregar($post);

                $CodCuentaLibre = NULL;

                foreach ($post_detalles['NumItem'] as $indice => $valor) {
                    if ($post_detalles['Parametro'][$indice] == 'AFECTO' || $post_detalles['Parametro'][$indice] == 'INAFECTO' || $post_detalles['Parametro'][$indice] == 'EXONERADO') {
                        $CodCuentaLibre = $post_detalles['CodCuenta'][$indice];

                        break;
                    }
                }

                $CampoLibre1 = NULL;
                $SaldoTotalSD_Referencia = 0;

                if (count($post_referencias) > 0) {
                    $CampoLibre1_array = array();
                    $CodMoneda = $post_detalles['CodMoneda'][0];

                    foreach ($post_referencias['IdMovDet_Referencia'] as $indice => $valor) {
                        $CampoLibre1 = '.' . $post_referencias['CodDocumento_Referencia'][$indice] .
                            '-' . $post_referencias['SerieDoc_Referencia'][$indice] .
                            '-' . $post_referencias['NumeroDoc_Referencia'][$indice] .
                            '-' . date('d/m/Y', strtotime(str_replace('/', '-', $post_referencias['FecEmision_Referencia'][$indice]))) .
                            '-' . $post_referencias['TotalS_Referencia'][$indice] .
                            '-' . $post_referencias['TotalD_Referencia'][$indice];

                        if ($CodMoneda == 'MO001') {
                            $SaldoTotalSD_Referencia += $post_referencias['TotalS_Referencia'][$indice];
                        } else if ($CodMoneda == 'MO002') {
                            $SaldoTotalSD_Referencia += $post_referencias['TotalD_Referencia'][$indice];
                        }

                        $CampoLibre1_array[] = $CampoLibre1;
                    }

                    if (count($CampoLibre1_array) > 0) $CampoLibre1 = implode(', ', $CampoLibre1_array);

                    foreach ($post_referencias['IdMovDetPadre_Referencia'] as $indice => $valor) {
                        $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], $valor, 0, 'Monto, Saldo', [], [], '', '');

                        if (count($movimiento_det) > 0) {
                            $Monto = $movimiento_det[0]['Monto'];
                            $Saldo = $Monto == $movimiento_det[0]['Saldo'] ? 0 : $movimiento_det[0]['Saldo'];

                            if ($CodMoneda == 'MO001') {
                                $Saldo = $Monto - $post_referencias['TotalS_Referencia'][$indice] - $Saldo;
                            } else if ($CodMoneda == 'MO002') {
                                $Saldo = $Monto - $post_referencias['TotalD_Referencia'][$indice] - $Saldo;
                            }

                            (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $valor, '', '', '', ['Saldo' => $Saldo]);
                        }
                    }
                }

                $contador_parametro_TOTAL = 0;

                $IdMovDetCampoLibre1 = array();

                $contador_NumItem = 1;

                $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($post['CodEmpresa'], $post['CodTV'], 0, 'Tipo', [], '', '');

                $Tipo = $tipo_voucher_cab[0]['Tipo'];

                foreach ($post_detalles['NumItem'] as $indice => $valor) {
                    if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') $contador_parametro_TOTAL++;

                    $Monto = 0;
                    $Saldo = 0;

                    if ($post_detalles['CtaCte'][$indice] == 1) {
                        if ($post_detalles['CodMoneda'][0] == 'MO001') {
                            if ($post_detalles['DebeSol'][$indice] != 0) {
                                $Monto = $post_detalles['DebeSol'][$indice];
                                $Saldo = $post_detalles['DebeSol'][$indice];
                            } else if ($post_detalles['HaberSol'][$indice] != 0) {
                                $Monto = $post_detalles['HaberSol'][$indice];
                                $Saldo = $post_detalles['HaberSol'][$indice];
                            }
                        } else if ($post_detalles['CodMoneda'][0] == 'MO002') {
                            if ($post_detalles['DebeDol'][$indice] != 0) {
                                $Monto = $post_detalles['DebeDol'][$indice];
                                $Saldo = $post_detalles['DebeDol'][$indice];
                            } else if ($post_detalles['HaberDol'][$indice] != 0) {
                                $Monto = $post_detalles['HaberDol'][$indice];
                                $Saldo = $post_detalles['HaberDol'][$indice];
                            }
                        }
                    }

                    if ($Tipo == 1) {
                        $Saldo = 0;
                    }

                    $data = $this->datos_movimiento_det();

                    if (
                        isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]) &&
                        ($post_detalles['DebeSol'][$indice] != 0 || $post_detalles['HaberSol'][$indice] != 0) &&
                        ($post_detalles['DebeDol'][$indice] != 0 || $post_detalles['HaberDol'][$indice] != 0)
                    ) {
                        $data['NumItem'] = $contador_NumItem++;
                        $data['CodEmpresa'] = $post['CodEmpresa'];
                        $data['IdMov'] = $IdMov;
                        $data['Periodo'] = $post['Periodo'];
                        $data['Mes'] = $post['Mes'];
                        $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                        $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                        $data['DebeSol'] = $post_detalles['DebeSol'][$indice];
                        $data['HaberSol'] = $post_detalles['HaberSol'][$indice];
                        $data['DebeDol'] = $post_detalles['DebeDol'][$indice];
                        $data['HaberDol'] = $post_detalles['HaberDol'][$indice];
                        $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                        $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                        $data['FecVcto'][$indice] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                        $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                        $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                        $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                        $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                        $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                        $data['Destino'] = 'NO';
                        $data['TipoOperacion'] = $post_detalles['TipoOperacion'][$indice];
                        $data['BaseImpSunatS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $BaseImpSunat_Haber_S - $BaseImpSunat_Debe_S : 0;
                        $data['BaseImpSunatS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $BaseImpSunat_Haber_S - $BaseImpSunat_Debe_S : 0;
                        $data['InafectoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Inafecto_Haber_S - $Inafecto_Debe_S : 0;
                        $data['ExoneradoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Exonerado_Haber_S - $Exonerado_Debe_S : 0;
                        $data['ISCS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $ISC_Haber_S - $ISC_Debe_S : 0;
                        $data['IGVSunatS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $IGVSunat_Haber_S - $IGVSunat_Debe_S : 0;
                        $data['PercepcionS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Percepcion_Haber_S - $Percepcion_Debe_S : 0;
                        $data['OtroTributoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $OtroTributo_Haber_S - $OtroTributo_Debe_S : 0;
                        $data['Retencion4S'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Retencion4_Haber_S - $Retencion4_Debe_S : 0;
                        $data['TotalS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Total_Debe_S - $Total_Haber_S : 0;
                        $data['DescuentoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Descuento_Haber_S - $Descuento_Debe_S : 0;
                        $data['AnticipoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Anticipo_Haber_S - $Anticipo_Debe_S : 0;
                        $data['IcbpS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Icbp_Haber_S - $Icbp_Debe_S : 0;
                        $data['BaseImpSunatD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $BaseImpSunat_Haber_D - $BaseImpSunat_Debe_D : 0;
                        $data['InafectoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Inafecto_Haber_D - $Inafecto_Debe_D : 0;
                        $data['ExoneradoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Exonerado_Haber_D - $Exonerado_Debe_D : 0;
                        $data['ISCD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $ISC_Haber_D - $ISC_Debe_D : 0;
                        $data['IGVSunatD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $IGVSunat_Haber_D - $IGVSunat_Debe_D : 0;
                        $data['PercepcionD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Percepcion_Haber_D - $Percepcion_Debe_D : 0;
                        $data['OtroTributoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $OtroTributo_Haber_D - $OtroTributo_Debe_D : 0;
                        $data['Retencion4D'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Retencion4_Haber_D - $Retencion4_Debe_D : 0;
                        $data['TotalD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Total_Debe_D - $Total_Haber_D : 0;
                        $data['DescuentoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Descuento_Haber_D - $Descuento_Debe_D : 0;
                        $data['AnticipoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Anticipo_Haber_D - $Anticipo_Debe_D : 0;
                        $data['IcbpD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Icbp_Haber_D - $Icbp_Debe_D : 0;
                        $data['CodCcosto'] = isset($post_detalles['CodCcosto'][$indice]) && !empty($post_detalles['CodCcosto'][$indice]) ? $post_detalles['CodCcosto'][$indice] : NULL;
                        $data['Destino'] = 'NO';
                        $data['RegistroSunat'] = 'VENTAS';
                        $data['TipoOperacion'] = isset($post_detalles['TipoOperacion'][$indice]) && !empty($post_detalles['TipoOperacion'][$indice]) ? $post_detalles['TipoOperacion'][$indice] : NULL;
                        $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                        $data['DocRetencion'] = isset($post_detalles['DocRetencion'][$indice]) && !empty($post_detalles['DocRetencion'][$indice]) ? $post_detalles['DocRetencion'][$indice] : NULL;
                        $data['DocDetraccion'] = isset($post_detalles['DocDetraccion'][$indice]) && !empty($post_detalles['DocDetraccion'][$indice]) ? $post_detalles['DocDetraccion'][$indice] : NULL;
                        $data['Parametro'] = $post_detalles['Parametro'][$indice];
                        $data['PorcRetencion'] = isset($post_detalles['PorcRetencion'][$indice]) && !empty($post_detalles['PorcRetencion'][$indice]) ? $post_detalles['PorcRetencion'][$indice] : 0;
                        $data['IdDetraccion'] = isset($post_detalles['IdDetraccion'][$indice]) && !empty($post_detalles['IdDetraccion'][$indice]) ? $post_detalles['IdDetraccion'][$indice] : NULL;
                        $data['FechaDetraccion'] = isset($post_detalles['FechaDetraccion'][$indice]) && !empty($post_detalles['FechaDetraccion'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FechaDetraccion'][$indice]))) : NULL;
                        $data['Monto'] = $Monto;
                        $data['Saldo'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 && count($post_referencias) > 0 ? $Monto - $SaldoTotalSD_Referencia : $Saldo;
                        $data['CtaCte'] = $post_detalles['CtaCte'][$indice];
                        $data['CodCuentaLibre'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' ? $CodCuentaLibre : NULL;
                        $data['CampoLibre1'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $CampoLibre1 : NULL;
                        $data['IdTipOpeDetra'] = isset($post_detalles['IdTipOpeDetra'][$indice]) && !empty($post_detalles['IdTipOpeDetra'][$indice]) ? $post_detalles['IdTipOpeDetra'][$indice] : NULL;
                        $data['CodTipoSN'] = 1;
                        $data['TCcierre'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' ? $ValorTC : NULL;
                        $data['IdenContProy'] = isset($post_detalles['IdenContProy'][$indice]) && !empty($post_detalles['IdenContProy'][$indice]) ? $post_detalles['IdenContProy'][$indice] : NULL;
                        $data['Declarar_Per'] = isset($post_detalles['Declarar_Per'][$indice]) && !empty($post_detalles['Declarar_Per'][$indice]) ? $post_detalles['Declarar_Per'][$indice] : NULL;
                        $data['Declarar_Est'] = isset($post_detalles['Declarar_Est'][$indice]) && !empty($post_detalles['Declarar_Est'][$indice]) ? $post_detalles['Declarar_Est'][$indice] : NULL;
                        $data['IdActivo'] = isset($post_detalles['IdActivo'][$indice]) && !empty($post_detalles['IdActivo'][$indice]) ? $post_detalles['IdActivo'][$indice] : NULL;
                    }

                    if (isset($IdMovRef) && !empty($IdMovRef)) {
                        $data['Saldo'] = 0;
                    }

                    if ((isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice])) &&
                        ($post_detalles['DebeSol'][$indice] != 0 ||
                            $post_detalles['HaberSol'][$indice] != 0) &&
                        ($post_detalles['DebeDol'][$indice] != 0 ||
                            $post_detalles['HaberDol'][$indice] != 0)
                    ) {

                        $IdMovDet = (new MovimientoDet())->agregar($data);

                        if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') $IdMovDetCampoLibre1[] = $IdMovDet;
                    }
                }

                if (count($post_referencias) > 0) {
                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                        $post['CodEmpresa'],
                        0,
                        'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                        [],
                        [
                            array('Origen' => array('VEN_AP'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                        ],
                        '',
                        '',
                        ''
                    );

                    $codigo_referencia_maximo = 'DIA' . date('m') . '000001';

                    if ($movimiento_cab[0]['codigo']) {
                        $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                        if (strlen($movimiento_cab[0]['codigo']) == 1) {
                            $codigo_referencia_maximo = 'DIA' . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                            $codigo_referencia_maximo = 'DIA' . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                            $codigo_referencia_maximo = 'DIA' . date('m') . '000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                            $codigo_referencia_maximo = 'DIA' . date('m') . '00' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                            $codigo_referencia_maximo = 'DIA' . date('m') . '0' . $movimiento_cab[0]['codigo'];
                        } else {
                            $codigo_referencia_maximo = 'DIA' . date('m') . $movimiento_cab[0]['codigo'];
                        }
                    }

                    $post['CodTV'] = 'DIA';
                    $post['Codmov'] = $codigo_referencia_maximo;
                    $post['Periodo'] = date('Y');
                    $post['Mes'] = date('m');
                    $post['IdMovAplica'] = $IdMov;
                    $post['Origen'] = 'VEN_AP';
                    $post['Glosa'] = strtoupper(trim($post['Glosa']));
                    $post['Detraccion'] = isset($post['Detraccion']) ? $post['Detraccion'] : 0;
                    $post['ValorTC'] = $ValorTC;

                    $IdMovAplica = (new MovimientoCab())->agregar($post);

                    $indice_referencia = 0;

                    $total_TotalS_Referencia = 0;
                    $total_TotalD_Referencia = 0;

                    $total_HaberSol_Referencia = 0;
                    $total_HaberDol_Referencia = 0;

                    foreach ($post_referencias['IdMovDet_Referencia'] as $indice => $valor) {
                        $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], $post_referencias['IdMovDetPadre_Referencia'][$indice], 0, '', [], [], '', '');

                        if (count($movimiento_det) > 0) {
                            $indice_referencia++;

                            $total_HaberSol_Referencia += $post_referencias['TotalS_Referencia'][$indice];
                            $total_HaberDol_Referencia += $post_referencias['TotalD_Referencia'][$indice];

                            $total_TotalS_Referencia += $post_referencias['TotalS_Referencia'][$indice];
                            $total_TotalD_Referencia += $post_referencias['TotalD_Referencia'][$indice];

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $indice_referencia;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMovAplica;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $movimiento_det[0]['CodCuenta'];
                            $data['ValorTC'] = $post_referencias['ValorTC_Referencia'][$indice];
                            $data['HaberSol'] = $post_referencias['TotalS_Referencia'][$indice];
                            $data['HaberDol'] = $post_referencias['TotalD_Referencia'][$indice];
                            $data['CodMoneda'] = $movimiento_det[0]['CodMoneda'];
                            $data['FecEmision'] = isset($movimiento_det[0]['FecEmision']) && !empty($movimiento_det[0]['FecEmision']) ? date('Y-m-d', strtotime(str_replace('/', '-', $movimiento_det[0]['FecEmision']))) : NULL;
                            $data['FecVcto'] = isset($movimiento_det[0]['FecVcto']) && !empty($movimiento_det[0]['FecVcto']) ? date('Y-m-d', strtotime(str_replace('/', '-', $movimiento_det[0]['FecVcto']))) : NULL;
                            $data['IdSocioN'] = $movimiento_det[0]['IdSocioN'];
                            $data['CodDocumento'] = $movimiento_det[0]['CodDocumento'];
                            $data['SerieDoc'] = $movimiento_det[0]['SerieDoc'];
                            $data['NumeroDoc'] = $movimiento_det[0]['NumeroDoc'];
                            $data['NumeroDocF'] = $movimiento_det[0]['NumeroDocF'];
                            $data['Destino'] = 'NO';
                            $data['RegistroSunat'] = 'VENTAS';

                            $IdMovDet = (new MovimientoDet())->agregar($data);

                            $data = [
                                'CodEmpresa' => $post['CodEmpresa'],
                                'IdMov' => $IdMovAplica,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $movimiento_det[0]['IdMovDet'],
                                'Periodo' => $post['Periodo'],
                                'Mes' => $post['Mes'],
                                'TotalDetSol' => $post_referencias['TotalS_Referencia'][$indice],
                                'TotalDetDol' => $post_referencias['TotalD_Referencia'][$indice],
                                'Importado' => NULL,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            (new SaldoDet())->agregar($data);
                        }
                    }

                    $total_DebeSol_Detalle = 0;
                    $total_DebeDol_Detalle = 0;

                    $indice_saldoDet = 0;

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        if (
                            $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice])) &&
                            (($post_detalles['DebeSol'][$indice] != 0 ||
                                $post_detalles['HaberSol'][$indice] != 0) &&
                                ($post_detalles['DebeDol'][$indice] != 0 ||
                                    $post_detalles['HaberDol'][$indice] != 0))
                        ) {
                            $indice_referencia++;

                            $total_DebeSol_Detalle += $post_detalles['DebeSol'][$indice] == 0 ? $post_detalles['HaberSol'][$indice] : $post_detalles['DebeSol'][$indice];
                            $total_DebeDol_Detalle += $post_detalles['DebeDol'][$indice] == 0 ? $post_detalles['HaberDol'][$indice] : $post_detalles['DebeDol'][$indice];

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $indice_referencia;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMovAplica;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                            $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                            $data['DebeSol'] = $post_detalles['DebeSol'][$indice] == 0 ? $post_detalles['HaberSol'][$indice] : $post_detalles['DebeSol'][$indice];
                            $data['DebeDol'] = $post_detalles['DebeDol'][$indice] == 0 ? $post_detalles['HaberDol'][$indice] : $post_detalles['DebeDol'][$indice];
                            $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                            $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                            $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                            $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                            $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                            $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                            $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                            $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                            $data['Destino'] = 'NO';

                            $IdMovDet = (new MovimientoDet())->agregar($data);

                            if (count($IdMovDetCampoLibre1) > 0 && isset($IdMovDetCampoLibre1[$indice_saldoDet])) {
                                $data = [
                                    'CodEmpresa' => $post['CodEmpresa'],
                                    'IdMov' => $IdMovAplica,
                                    'IdMovDet' => $IdMovDet,
                                    'IdMovDetRef' => $IdMovDetCampoLibre1[$indice_saldoDet],
                                    'Periodo' => $post['Periodo'],
                                    'Mes' => $post['Mes'],
                                    'TotalDetSol' => $total_TotalS_Referencia,
                                    'TotalDetDol' => $total_TotalD_Referencia,
                                    'Importado' => NULL,
                                    'CodDocRef' => NULL,
                                    'SerieRef' => NULL,
                                    'NumeroRef' => NULL,
                                    'FechaRef' => NULL,
                                    'FlagInterno' => 0
                                ];

                                (new SaldoDet())->agregar($data);

                                $indice_saldoDet++;
                            }
                        }
                    }

                    if (($total_DebeSol_Detalle != $total_HaberSol_Referencia) || ($total_DebeDol_Detalle != $total_HaberDol_Referencia)) {
                        $plan_contable = (new PlanContable())->getPlanContable($post['CodEmpresa'], '', '', 'CodCuenta', [], 'UPPER(DescCuenta) = "DIFERENCIA DE CAMBIO"', '');

                        $CodCuentaDiferenciaCambio = 0;

                        if (count($plan_contable) > 0) {
                            $CodCuentaDiferenciaCambio = $plan_contable[0]['CodCuenta'];
                        }

                        $indice_referencia++;

                        $diferencia_DebeSol = $total_HaberSol_Referencia - $total_DebeSol_Detalle;
                        $diferencia_DebeDol = $total_HaberDol_Referencia - $total_DebeDol_Detalle;

                        $data = $this->datos_movimiento_det();

                        $data['NumItem'] = $indice_referencia;
                        $data['CodEmpresa'] = $post['CodEmpresa'];
                        $data['IdMov'] = $IdMovAplica;
                        $data['Periodo'] = $post['Periodo'];
                        $data['Mes'] = $post['Mes'];
                        $data['CodCuenta'] = $CodCuentaDiferenciaCambio;
                        $data['ValorTC'] = $post_detalles['ValorTC'][0];
                        $data['DebeSol'] = $diferencia_DebeSol;
                        $data['DebeDol'] = $diferencia_DebeDol;
                        $data['CodMoneda'] = $post_detalles['CodMoneda'][0];
                        $data['FecEmision'] = isset($post_detalles['FecEmision'][0]) && !empty($post_detalles['FecEmision'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][0]))) : NULL;
                        $data['FecVcto'] = isset($post_detalles['FecVcto'][0]) && !empty($post_detalles['FecVcto'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][0]))) : NULL;
                        $data['Destino'] = 'NO';

                        (new MovimientoDet())->agregar($data);

                        $amarre = (new Amarre())->getAmarre($post['CodEmpresa'], '', $CodCuentaDiferenciaCambio, 'CuentaDebe, CuentaHaber', [], '', '');

                        if (count($amarre) > 0) {
                            foreach ($amarre as $indice => $valor) {
                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $indice_referencia++;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMovAplica;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = $valor['CuentaDebe'];
                                $data['ValorTC'] = $post_detalles['ValorTC'][0];
                                $data['DebeSol'] = $diferencia_DebeSol;
                                $data['DebeDol'] = $diferencia_DebeDol;
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][0];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][0]) && !empty($post_detalles['FecEmision'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][0]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][0]) && !empty($post_detalles['FecVcto'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][0]))) : NULL;
                                $data['Destino'] = 'SI';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['codCuentaDestino'] = $CodCuentaDiferenciaCambio;

                                (new MovimientoDet())->agregar($data);

                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $indice_referencia++;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMovAplica;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = $valor['CuentaHaber'];
                                $data['ValorTC'] = $post_detalles['ValorTC'][0];
                                $data['DebeSol'] = $diferencia_DebeSol;
                                $data['DebeDol'] = $diferencia_DebeDol;
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][0];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][0]) && !empty($post_detalles['FecEmision'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][0]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][0]) && !empty($post_detalles['FecVcto'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][0]))) : NULL;
                                $data['Destino'] = 'SI';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['codCuentaDestino'] = $CodCuentaDiferenciaCambio;

                                (new MovimientoDet())->agregar($data);
                            }
                        }
                    }
                }

                if (count($post_banco) > 0 && count($post_referencias) == 0) {
                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                        $post['CodEmpresa'],
                        0,
                        'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                        [],
                        [
                            array('Origen' => array('VEN_CO'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                        ],
                        '',
                        '',
                        ''
                    );

                    $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($post['CodEmpresa'], '', 5, 'CodTV', [], '', '');

                    if (count($tipo_voucher_cab) > 0) {
                        $CodTV = $tipo_voucher_cab[0]['CodTV'];
                    } else {
                        $CodTV = 'CCL';
                    }

                    $codigo_voucher_maximo = $CodTV . date('m') . '000001';

                    if ($movimiento_cab[0]['codigo']) {
                        $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                        if (strlen($movimiento_cab[0]['codigo']) == 1) {
                            $codigo_voucher_maximo = $CodTV . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                            $codigo_voucher_maximo = $CodTV . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                            $codigo_voucher_maximo = $CodTV . date('m') . '000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                            $codigo_voucher_maximo = $CodTV . date('m') . '00' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                            $codigo_voucher_maximo = $CodTV . date('m') . '0' . $movimiento_cab[0]['codigo'];
                        } else {
                            $codigo_voucher_maximo = $CodTV . date('m') . $movimiento_cab[0]['codigo'];
                        }
                    }

                    $post['CodTV'] = $CodTV;
                    $post['Codmov'] = $codigo_voucher_maximo;
                    $post['Periodo'] = date('Y');
                    $post['Mes'] = date('m');
                    $post['IdMovRef'] = $IdMov;
                    $post['Origen'] = 'VEN_CO';
                    $post['Glosa'] = strtoupper(trim($post['Glosa']));
                    $post['ValorTC'] = $ValorTC;

                    $IdMov_CCL = (new MovimientoCab())->agregar($post);

                    $total_TotalS_1011 = 0;
                    $total_TotalD_1011 = 0;

                    $total_TotalS_Referencia = 0;
                    $total_TotalD_Referencia = 0;

                    $monto_ultimo_CtaCte_Total = 0;

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') {
                            $total_TotalS_1011 += $post_detalles['DebeSol'][$indice];
                            $total_TotalD_1011 += $post_detalles['DebeDol'][$indice];

                            if ($post_detalles['CodMoneda'][$indice] == 'MO001') {
                                $monto_ultimo_CtaCte_Total = $post_detalles['DebeSol'][$indice];
                            } else if ($post_detalles['CodMoneda'][$indice] == 'MO002') {
                                $monto_ultimo_CtaCte_Total = $post_detalles['DebeDol'][$indice];
                            }
                        }
                    }

                    $contador_parametro_TOTAL = 0;

                    $indice_saldoDet = 0;

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]))) {
                            $contador_parametro_TOTAL += 1;

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $contador_parametro_TOTAL;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMov_CCL;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $post_banco['CodCuenta'];
                            $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                            $data['DebeSol'] = $contador_parametro_TOTAL == 1 ? $total_TotalS_1011 : 0;
                            $data['DebeDol'] = $contador_parametro_TOTAL == 1 ? $total_TotalD_1011 : 0;
                            $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                            $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                            $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                            $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                            $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                            $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                            $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                            $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                            $data['Destino'] = 'NO';
                            $data['RegistroSunat'] = 'NINGUNO';
                            $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                            $data['CodTipoPago'] = isset($post_banco['CodTipoPago']) && !empty($post_banco['CodTipoPago']) ? $post_banco['CodTipoPago'] : NULL;
                            $data['PorcRetencion'] = isset($post_detalles['PorcRetencion'][$indice]) && !empty($post_detalles['PorcRetencion'][$indice]) ? $post_detalles['PorcRetencion'][$indice] : 0;
                            $data['IdDetraccion'] = isset($post_detalles['IdDetraccion'][$indice]) && !empty($post_detalles['IdDetraccion'][$indice]) ? $post_detalles['IdDetraccion'][$indice] : NULL;
                            $data['FechaDetraccion'] = isset($post_detalles['FechaDetraccion'][$indice]) && !empty($post_detalles['FechaDetraccion'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FechaDetraccion'][$indice]))) : NULL;
                            $data['Parametro'] = 'BANCO';
                            $data['GlosaDet'] = isset($post_banco['GlosaDet']) && !empty($post_banco['GlosaDet']) ? $post_banco['GlosaDet'] : '';
                            $data['IdTipOpeDetra'] = isset($post_detalles['IdTipOpeDetra'][$indice]) && !empty($post_detalles['IdTipOpeDetra'][$indice]) ? $post_detalles['IdTipOpeDetra'][$indice] : NULL;
                            $data['IdenContProy'] = isset($post_detalles['IdenContProy'][$indice]) && !empty($post_detalles['IdenContProy'][$indice]) ? $post_detalles['IdenContProy'][$indice] : NULL;
                            $data['Declarar_Per'] = isset($post_detalles['Declarar_Per'][$indice]) && !empty($post_detalles['Declarar_Per'][$indice]) ? $post_detalles['Declarar_Per'][$indice] : NULL;
                            $data['Declarar_Est'] = isset($post_detalles['Declarar_Est'][$indice]) && !empty($post_detalles['Declarar_Est'][$indice]) ? $post_detalles['Declarar_Est'][$indice] : NULL;
                            $data['IdActivo'] = isset($post_detalles['IdActivo'][$indice]) && !empty($post_detalles['IdActivo'][$indice]) ? $post_detalles['IdActivo'][$indice] : NULL;
                            $data['Monto'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                            $data['Saldo'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                            $data['NumCheque'] = $post_banco['NumCheque'];

                            (new MovimientoDet())->agregar($data);

                            $contador_parametro_TOTAL += 1;

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $contador_parametro_TOTAL;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMov_CCL;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                            $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                            $data['HaberSol'] = $post_detalles['DebeSol'][$indice];
                            $data['HaberDol'] = $post_detalles['DebeDol'][$indice];
                            $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                            $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                            $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                            $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                            $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                            $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                            $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                            $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                            $data['Destino'] = 'NO';
                            $data['RegistroSunat'] = 'NINGUNO';
                            $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                            $data['CodTipoPago'] = isset($post_detalles['CodTipoPago'][$indice]) && !empty($post_detalles['CodTipoPago'][$indice]) ? $post_detalles['CodTipoPago'][$indice] : NULL;
                            $data['PorcRetencion'] = isset($post_detalles['PorcRetencion'][$indice]) && !empty($post_detalles['PorcRetencion'][$indice]) ? $post_detalles['PorcRetencion'][$indice] : 0;
                            $data['IdDetraccion'] = isset($post_detalles['IdDetraccion'][$indice]) && !empty($post_detalles['IdDetraccion'][$indice]) ? $post_detalles['IdDetraccion'][$indice] : NULL;
                            $data['FechaDetraccion'] = isset($post_detalles['FechaDetraccion'][$indice]) && !empty($post_detalles['FechaDetraccion'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FechaDetraccion'][$indice]))) : NULL;
                            $data['IdTipOpeDetra'] = isset($post_detalles['IdTipOpeDetra'][$indice]) && !empty($post_detalles['IdTipOpeDetra'][$indice]) ? $post_detalles['IdTipOpeDetra'][$indice] : NULL;
                            $data['IdenContProy'] = isset($post_detalles['IdenContProy'][$indice]) && !empty($post_detalles['IdenContProy'][$indice]) ? $post_detalles['IdenContProy'][$indice] : NULL;
                            $data['Declarar_Per'] = isset($post_detalles['Declarar_Per'][$indice]) && !empty($post_detalles['Declarar_Per'][$indice]) ? $post_detalles['Declarar_Per'][$indice] : NULL;
                            $data['Declarar_Est'] = isset($post_detalles['Declarar_Est'][$indice]) && !empty($post_detalles['Declarar_Est'][$indice]) ? $post_detalles['Declarar_Est'][$indice] : NULL;
                            $data['TipoPC'] = 29;
                            $data['IdActivo'] = isset($post_detalles['IdActivo'][$indice]) && !empty($post_detalles['IdActivo'][$indice]) ? $post_detalles['IdActivo'][$indice] : NULL;
                            $data['Monto'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                            $data['Saldo'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;

                            $IdMovDet = (new MovimientoDet())->agregar($data);

                            $data = [
                                'CodEmpresa' => $post['CodEmpresa'],
                                'IdMov' => $IdMov_CCL,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $IdMovDetCampoLibre1[$indice_saldoDet],
                                'Periodo' => $post['Periodo'],
                                'Mes' => $post['Mes'],
                                'TotalDetSol' => $post_detalles['DebeSol'][$indice],
                                'TotalDetDol' => $post_detalles['DebeDol'][$indice],
                                'Importado' => NULL,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            (new SaldoDet())->agregar($data);

                            $indice_saldoDet++;
                        }
                    }
                } else {
                    $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($post['CodEmpresa'], '', 1, 'CodTV', [], '', '');

                    $CodTV_Contado = array();

                    foreach ($tipo_voucher_cab as $indice => $valor) {
                        $CodTV_Contado[] = $valor['CodTV'];
                    }

                    if (in_array($post['CodTV'], $CodTV_Contado)) {
                        $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($post['CodEmpresa'], '', 5, 'CodTV', [], '', '');

                        if (count($tipo_voucher_cab) > 0) {
                            $CodTV = $tipo_voucher_cab[0]['CodTV'];
                        } else {
                            $CodTV = 'CCL';
                        }

                        $codigo_voucher_maximo = $CodTV . date('m') . '000001';

                        if ($movimiento_cab[0]['codigo']) {
                            $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                            if (strlen($movimiento_cab[0]['codigo']) == 1) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '00' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '0' . $movimiento_cab[0]['codigo'];
                            } else {
                                $codigo_voucher_maximo = $CodTV . date('m') . $movimiento_cab[0]['codigo'];
                            }
                        }

                        $post['CodTV'] = $CodTV;
                        $post['Codmov'] = $codigo_voucher_maximo;
                        $post['Periodo'] = date('Y');
                        $post['Mes'] = date('m');
                        $post['IdMovRef'] = $IdMov;
                        $post['Origen'] = 'VEN_CO';
                        $post['Glosa'] = strtoupper(trim($post['Glosa']));
                        $post['ValorTC'] = $ValorTC;

                        $IdMov_CCL = (new MovimientoCab())->agregar($post);

                        $total_TotalS_1011 = 0;
                        $total_TotalD_1011 = 0;

                        $total_TotalS_Referencia = 0;
                        $total_TotalD_Referencia = 0;

                        $contador_parametro_TOTAL = 0;

                        foreach ($post_detalles['NumItem'] as $indice => $valor) {
                            if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') {
                                $total_TotalS_1011 += $post_detalles['DebeSol'][$indice];
                                $total_TotalD_1011 += $post_detalles['DebeDol'][$indice];
                            }
                        }

                        $contador_parametro_TOTAL = 0;

                        $indice_saldoDet = 0;

                        foreach ($post_detalles['NumItem'] as $indice => $valor) {
                            if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice])) {
                                $contador_parametro_TOTAL += 1;

                                $total_TotalS_Referencia = $post_detalles['DebeSol'][$indice];
                                $total_TotalD_Referencia = $post_detalles['DebeDol'][$indice];

                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $contador_parametro_TOTAL;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMov_CCL;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = 1011;
                                $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                                $data['DebeSol'] = $contador_parametro_TOTAL == 1 ? $total_TotalS_1011 : 0;
                                $data['DebeDol'] = $contador_parametro_TOTAL == 1 ? $total_TotalD_1011 : 0;
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                                $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                                $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                                $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                                $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                                $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                                $data['Destino'] = 'NO';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['Parametro'] = 'BANCO';
                                $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;

                                (new MovimientoDet())->agregar($data);

                                $contador_parametro_TOTAL += 1;

                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $contador_parametro_TOTAL;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMov_CCL;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                                $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                                $data['HaberSol'] = $post_detalles['DebeSol'][$indice];
                                $data['HaberDol'] = $post_detalles['DebeDol'][$indice];
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                                $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                                $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                                $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                                $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                                $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                                $data['Destino'] = 'NO';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                                $data['TipoPC'] = 29;

                                $IdMovDet = (new MovimientoDet())->agregar($data);

                                if (isset($IdMovDetCampoLibre1[$indice_saldoDet])) {
                                    $data = [
                                        'CodEmpresa' => $post['CodEmpresa'],
                                        'IdMov' => $IdMov_CCL,
                                        'IdMovDet' => $IdMovDet,
                                        'IdMovDetRef' => $IdMovDetCampoLibre1[$indice_saldoDet],
                                        'Periodo' => $post['Periodo'],
                                        'Mes' => $post['Mes'],
                                        'TotalDetSol' => $post_detalles['DebeSol'][$indice],
                                        'TotalDetDol' => $post_detalles['DebeDol'][$indice],
                                        'Importado' => NULL,
                                        'CodDocRef' => NULL,
                                        'SerieRef' => NULL,
                                        'NumeroRef' => NULL,
                                        'FechaRef' => NULL,
                                        'FlagInterno' => 0
                                    ];

                                    (new SaldoDet())->agregar($data);
                                }

                                $indice_saldoDet++;
                            }
                        }
                    }
                }
            }

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

            return redirect()->to(base_url('app/movements/sales/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update()
    {
        try {
            $post = $this->request->getPost();

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $IdMov = $post['IdMov'];
            $IdMovRef = $post['IdMovRef'];
            $IdMovAplica = $post['IdMovAplica'];
            $post['Estado'] = !isset($post['Estado']) ? 0 : $post['Estado'];
            $post['Detraccion'] = isset($post['Detraccion']) ? $post['Detraccion'] : 0;
            $post['Periodo'] = date('Y');
            $post['Mes'] = date('m');
            $post['Origen'] = 'VEN';
            $post['Glosa'] = strtoupper(trim($post['Glosa']));

            $post_detalles = array();
            $post_banco = array();
            $post_referencias = array();

            foreach ($post as $indice => $valor) {
                if (is_array($valor) && strpos($indice, '_Referencia') == FALSE) {
                    $post_detalles[$indice] = $post[$indice];

                    unset($post[$indice]);
                } else if (is_array($valor) && strpos($indice, '_Referencia') == TRUE) {
                    $post_referencias[$indice] = $post[$indice];

                    unset($post[$indice]);
                }

                if (!empty($post['Banco']) && $indice == 'Banco') {
                    parse_str($valor, $post_banco);

                    unset($post[$indice]);
                }
            }

            if (empty($post['Banco'])) {
                unset($post['Banco']);
            }

            unset($post['IdMovRef']);
            unset($post['IdMovAplica']);

            if (isset($post_detalles['IdMovDet'])) {
                $ids = implode(',', $post_detalles['IdMovDet']);

                (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMov, null, '', '', '', '', 'IdMovDet NOT IN (' . $ids . ')');
            } else {
                (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMov, null, '', '', '', '', '');
            }

            $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], 0, $IdMov, 'CampoLibre1', [], [], 'CampoLibre1 IS NOT NULL', '');

            if (count($movimiento_det) > 0) {
                $CampoLibre1 = explode(', ', $movimiento_det[0]['CampoLibre1']);

                foreach ($CampoLibre1 as $indice => $valor) {
                    $CodDocumento = explode('.', explode('-', $valor)[0])[1];
                    $SerieDoc = explode('-', $valor)[1];
                    $NumeroDoc = explode('-', $valor)[2];
                    $FecEmision = str_replace('/', '-', explode('-', $valor)[3]);
                    $FecEmision = date('Y-m-d', strtotime($FecEmision));

                    $where = 'det.IdMov != "' . $IdMovAplica . '" AND DATE(det.FecEmision) = "' . $FecEmision . '" AND det.CodDocumento = "' . $CodDocumento . '" AND det.SerieDoc = "' . $SerieDoc . '" AND det.NumeroDoc = "' . $NumeroDoc . '"';

                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                        $post['CodEmpresa'],
                        0,
                        'det.IdMovDet, det.Monto',
                        [
                            array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                        ],
                        [],
                        $where,
                        '',
                        ''
                    );

                    if (count($movimiento_cab) > 0) {
                        $IdMovDet_Auxiliar = $movimiento_cab[0]['IdMovDet'];
                        $Monto_Auxiliar = $movimiento_cab[0]['Monto'];

                        (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $IdMovDet_Auxiliar, '', '', '', ['Saldo' => $Monto_Auxiliar]);

                        if (count($post_referencias) > 0) {
                            foreach ($post_referencias['IdMovDet_Referencia'] as $indice => $valor) {
                                $CodDocumento_Referencia = $post_referencias['CodDocumento_Referencia'][$indice];
                                $SerieDoc_Referencia = $post_referencias['SerieDoc_Referencia'][$indice];
                                $NumeroDoc_Referencia = $post_referencias['NumeroDoc_Referencia'][$indice];
                                $FecEmision_Referencia = $post_referencias['FecEmision_Referencia'][$indice];
                                $TotalS_Referencia = $post_referencias['TotalS_Referencia'][$indice];

                                if ($CodDocumento == $CodDocumento_Referencia && $SerieDoc == $SerieDoc_Referencia && $NumeroDoc == $NumeroDoc_Referencia && $FecEmision == $FecEmision_Referencia) {
                                    (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $IdMovDet_Auxiliar, '', '', '', ['Saldo' => $Monto_Auxiliar - $TotalS_Referencia]);
                                }
                            }
                        }
                    }

                    if (count($post_referencias) > 0) {
                        if (
                            !in_array($CodDocumento, $post_referencias['CodDocumento_Referencia']) ||
                            !in_array($SerieDoc, $post_referencias['SerieDoc_Referencia']) ||
                            !in_array($NumeroDoc, $post_referencias['NumeroDoc_Referencia']) ||
                            !in_array($FecEmision, $post_referencias['FecEmision_Referencia'])
                        ) {
                            (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMovAplica, null, $CodDocumento, $SerieDoc, $NumeroDoc, $FecEmision, '');
                        }
                    }
                }
            }

            if (count($post_detalles['NumItem']) > 0) {
                $BaseImpSunat_Debe_S = 0;
                $Inafecto_Debe_S = 0;
                $Exonerado_Debe_S = 0;
                $ISC_Debe_S = 0;
                $IGVSunat_Debe_S = 0;
                $Percepcion_Debe_S = 0;
                $OtroTributo_Debe_S = 0;
                $Retencion4_Debe_S = 0;
                $Total_Debe_S = 0;
                $Descuento_Debe_S = 0;
                $Anticipo_Debe_S = 0;
                $Icbp_Debe_S = 0;

                $BaseImpSunat_Haber_S = 0;
                $Inafecto_Haber_S = 0;
                $Exonerado_Haber_S = 0;
                $ISC_Haber_S = 0;
                $IGVSunat_Haber_S = 0;
                $Percepcion_Haber_S = 0;
                $OtroTributo_Haber_S = 0;
                $Retencion4_Haber_S = 0;
                $Total_Haber_S = 0;
                $Descuento_Haber_S = 0;
                $Anticipo_Haber_S = 0;
                $Icbp_Haber_S = 0;

                $BaseImpSunat_Debe_D = 0;
                $Inafecto_Debe_D = 0;
                $Exonerado_Debe_D = 0;
                $ISC_Debe_D = 0;
                $IGVSunat_Debe_D = 0;
                $Percepcion_Debe_D = 0;
                $OtroTributo_Debe_D = 0;
                $Retencion4_Debe_D = 0;
                $Total_Debe_D = 0;
                $Descuento_Debe_D = 0;
                $Anticipo_Debe_D = 0;
                $Icbp_Debe_D = 0;

                $BaseImpSunat_Haber_D = 0;
                $Inafecto_Haber_D = 0;
                $Exonerado_Haber_D = 0;
                $ISC_Haber_D = 0;
                $IGVSunat_Haber_D = 0;
                $Percepcion_Haber_D = 0;
                $OtroTributo_Haber_D = 0;
                $Retencion4_Haber_D = 0;
                $Total_Haber_D = 0;
                $Descuento_Haber_D = 0;
                $Anticipo_Haber_D = 0;
                $Icbp_Haber_D = 0;

                $ValorTC = 0;

                foreach ($post_detalles['NumItem'] as $indice => $valor) {
                    $ValorTC = $post_detalles['ValorTC'][$indice];

                    switch ($post_detalles['Parametro'][$indice]) {
                        case 'AFECTO':
                            $BaseImpSunat_Debe_S += $post_detalles['DebeSol'][$indice];
                            $BaseImpSunat_Haber_S += $post_detalles['HaberSol'][$indice];
                            $BaseImpSunat_Debe_D += $post_detalles['DebeDol'][$indice];
                            $BaseImpSunat_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'ANTICIPO':
                            $Anticipo_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Anticipo_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Anticipo_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Anticipo_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'DESCUENTO':
                            $Descuento_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Descuento_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Descuento_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Descuento_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'IGV':
                            $IGVSunat_Debe_S += $post_detalles['DebeSol'][$indice];
                            $IGVSunat_Haber_S += $post_detalles['HaberSol'][$indice];
                            $IGVSunat_Debe_D += $post_detalles['DebeDol'][$indice];
                            $IGVSunat_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'PERCEPCION':
                            $Percepcion_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Percepcion_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Percepcion_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Percepcion_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'ISC':
                            $ISC_Debe_S += $post_detalles['DebeSol'][$indice];
                            $ISC_Haber_S += $post_detalles['HaberSol'][$indice];
                            $ISC_Debe_D += $post_detalles['DebeDol'][$indice];
                            $ISC_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'INAFECTO':
                            $Inafecto_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Inafecto_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Inafecto_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Inafecto_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'EXONERADO':
                            $Exonerado_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Exonerado_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Exonerado_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Exonerado_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'TOTAL':
                            $Total_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Total_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Total_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Total_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'OTRO TRIBUTO':
                            $OtroTributo_Debe_S += $post_detalles['DebeSol'][$indice];
                            $OtroTributo_Haber_S += $post_detalles['HaberSol'][$indice];
                            $OtroTributo_Debe_D += $post_detalles['DebeDol'][$indice];
                            $OtroTributo_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                        case 'ICBP':
                            $Icbp_Debe_S += $post_detalles['DebeSol'][$indice];
                            $Icbp_Haber_S += $post_detalles['HaberSol'][$indice];
                            $Icbp_Debe_D += $post_detalles['DebeDol'][$indice];
                            $Icbp_Haber_D += $post_detalles['HaberDol'][$indice];

                            break;
                    }
                }

                $post['ValorTC'] = $ValorTC;

                if ($post_detalles['CodMoneda'][0] == 'MO001') {
                    $post['TotalDol'] = 0;
                    $post['ValorTC'] = 0;
                    $post['FlagInterno'] = 0;
                } else if ($post_detalles['CodMoneda'][0] == 'MO002') {
                    $post['TotalSol'] = 0;
                    $post['ValorTC'] = 0;
                    $post['FlagInterno'] = 0;
                }

                (new MovimientoCab())->actualizar($post['CodEmpresa'], $IdMov, $post);

                $CodCuentaLibre = NULL;

                foreach ($post_detalles['NumItem'] as $indice => $valor) {
                    if ($post_detalles['Parametro'][$indice] == 'AFECTO' || $post_detalles['Parametro'][$indice] == 'INAFECTO' || $post_detalles['Parametro'][$indice] == 'EXONERADO') {
                        $CodCuentaLibre = $post_detalles['CodCuenta'][$indice];

                        break;
                    }
                }

                $CampoLibre1 = NULL;
                $SaldoTotalSD_Referencia = 0;

                if (count($post_referencias) > 0) {
                    $CampoLibre1_array = array();
                    $CodMoneda = $post_detalles['CodMoneda'][0];

                    foreach ($post_referencias['IdMovDet_Referencia'] as $indice => $valor) {
                        $CampoLibre1 = '.' . $post_referencias['CodDocumento_Referencia'][$indice] .
                            '-' . $post_referencias['SerieDoc_Referencia'][$indice] .
                            '-' . $post_referencias['NumeroDoc_Referencia'][$indice] .
                            '-' . date('d/m/Y', strtotime(str_replace('/', '-', $post_referencias['FecEmision_Referencia'][$indice]))) .
                            '-' . $post_referencias['TotalS_Referencia'][$indice] .
                            '-' . $post_referencias['TotalD_Referencia'][$indice];

                        if ($CodMoneda == 'MO001') {
                            $SaldoTotalSD_Referencia += $post_referencias['TotalS_Referencia'][$indice];
                        } else if ($CodMoneda == 'MO002') {
                            $SaldoTotalSD_Referencia += $post_referencias['TotalD_Referencia'][$indice];
                        }

                        $CampoLibre1_array[] = $CampoLibre1;
                    }

                    if (count($CampoLibre1_array) > 0) $CampoLibre1 = implode(', ', $CampoLibre1_array);

                    foreach ($post_referencias['IdMovDetPadre_Referencia'] as $indice => $valor) {
                        $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], $valor, 0, 'Monto, Saldo', [], [], '', '');

                        if (count($movimiento_det) > 0) {
                            $Monto = $movimiento_det[0]['Monto'];
                            $Saldo = $Monto == $movimiento_det[0]['Saldo'] ? 0 : $movimiento_det[0]['Saldo'];

                            if ($CodMoneda == 'MO001') {
                                $Saldo = $Monto - $post_referencias['TotalS_Referencia'][$indice] - $Saldo;
                            } else if ($CodMoneda == 'MO002') {
                                $Saldo = $Monto - $post_referencias['TotalD_Referencia'][$indice] - $Saldo;
                            }

                            (new MovimientoDet())->where('CodEmpresa', $post['CodEmpresa'])->update($valor, ['Saldo' => $Saldo]);
                        }
                    }
                }

                $contador_parametro_TOTAL = 0;

                $IdMovDetCampoLibre1 = array();

                $contador_NumItem = 1;

                foreach ($post_detalles['NumItem'] as $indice => $valor) {
                    if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') $contador_parametro_TOTAL++;

                    $Monto = 0;
                    $Saldo = 0;

                    if ($post_detalles['CtaCte'][$indice] == 1) {
                        if ($post_detalles['CodMoneda'][0] == 'MO001') {
                            if ($post_detalles['DebeSol'][$indice] != 0) {
                                $Monto = $post_detalles['DebeSol'][$indice];
                                $Saldo = $post_detalles['DebeSol'][$indice];
                            } else if ($post_detalles['HaberSol'][$indice] != 0) {
                                $Monto = $post_detalles['HaberSol'][$indice];
                                $Saldo = $post_detalles['HaberSol'][$indice];
                            }
                        } else if ($post_detalles['CodMoneda'][0] == 'MO002') {
                            if ($post_detalles['DebeDol'][$indice] != 0) {
                                $Monto = $post_detalles['DebeDol'][$indice];
                                $Saldo = $post_detalles['DebeDol'][$indice];
                            } else if ($post_detalles['HaberDol'][$indice] != 0) {
                                $Monto = $post_detalles['HaberDol'][$indice];
                                $Saldo = $post_detalles['HaberDol'][$indice];
                            }
                        }
                    }

                    $data = $this->datos_movimiento_det();

                    if (
                        isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]) &&
                        ($post_detalles['DebeSol'][$indice] != 0 || $post_detalles['HaberSol'][$indice] != 0) &&
                        ($post_detalles['DebeDol'][$indice] != 0 || $post_detalles['HaberDol'][$indice] != 0)
                    ) {
                        $data['NumItem'] = $contador_NumItem++;
                        $data['CodEmpresa'] = $post['CodEmpresa'];
                        $data['IdMov'] = $IdMov;
                        $data['Periodo'] = $post['Periodo'];
                        $data['Mes'] = $post['Mes'];
                        $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                        $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                        $data['DebeSol'] = $post_detalles['DebeSol'][$indice];
                        $data['HaberSol'] = $post_detalles['HaberSol'][$indice];
                        $data['DebeDol'] = $post_detalles['DebeDol'][$indice];
                        $data['HaberDol'] = $post_detalles['HaberDol'][$indice];
                        $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                        $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                        $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                        $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                        $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                        $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                        $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                        $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                        $data['Destino'] = 'NO';
                        $data['RegistroSunat'] = 'VENTAS';
                        $data['TipoOperacion'] = isset($post_detalles['TipoOperacion'][$indice]) && !empty($post_detalles['TipoOperacion'][$indice]) ? $post_detalles['TipoOperacion'][$indice] : NULL;
                        $data['BaseImpSunatS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $BaseImpSunat_Haber_S - $BaseImpSunat_Debe_S : 0;
                        $data['InafectoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Inafecto_Haber_S - $Inafecto_Debe_S : 0;
                        $data['ExoneradoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Exonerado_Haber_S - $Exonerado_Debe_S : 0;
                        $data['ISCS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $ISC_Haber_S - $ISC_Debe_S : 0;
                        $data['IGVSunatS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $IGVSunat_Haber_S - $IGVSunat_Debe_S : 0;
                        $data['PercepcionS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Percepcion_Haber_S - $Percepcion_Debe_S : 0;
                        $data['OtroTributoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $OtroTributo_Haber_S - $OtroTributo_Debe_S : 0;
                        $data['Retencion4S'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Retencion4_Haber_S - $Retencion4_Debe_S : 0;
                        $data['TotalS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Total_Debe_S - $Total_Haber_S : 0;
                        $data['DescuentoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Descuento_Haber_S - $Descuento_Debe_S : 0;
                        $data['AnticipoS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Anticipo_Haber_S - $Anticipo_Debe_S : 0;
                        $data['IcbpS'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Icbp_Haber_S - $Icbp_Debe_S : 0;
                        $data['BaseImpSunatD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $BaseImpSunat_Haber_D - $BaseImpSunat_Debe_D : 0;
                        $data['InafectoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Inafecto_Haber_D - $Inafecto_Debe_D : 0;
                        $data['ExoneradoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Exonerado_Haber_D - $Exonerado_Debe_D : 0;
                        $data['ISCD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $ISC_Haber_D - $ISC_Debe_D : 0;
                        $data['IGVSunatD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $IGVSunat_Haber_D - $IGVSunat_Debe_D : 0;
                        $data['PercepcionD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Percepcion_Haber_D - $Percepcion_Debe_D : 0;
                        $data['OtroTributoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $OtroTributo_Haber_D - $OtroTributo_Debe_D : 0;
                        $data['Retencion4D'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Retencion4_Haber_D - $Retencion4_Debe_D : 0;
                        $data['TotalD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Total_Debe_D - $Total_Haber_D : 0;
                        $data['DescuentoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Descuento_Haber_D - $Descuento_Debe_D : 0;
                        $data['AnticipoD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Anticipo_Haber_D - $Anticipo_Debe_D : 0;
                        $data['IcbpD'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $Icbp_Haber_D - $Icbp_Debe_D : 0;
                        $data['CodCcosto'] = isset($post_detalles['CodCcosto'][$indice]) && !empty($post_detalles['CodCcosto'][$indice]) ? $post_detalles['CodCcosto'][$indice] : NULL;
                        $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                        $data['DocRetencion'] = isset($post_detalles['DocRetencion'][$indice]) && !empty($post_detalles['DocRetencion'][$indice]) ? $post_detalles['DocRetencion'][$indice] : NULL;
                        $data['DocDetraccion'] = isset($post_detalles['DocDetraccion'][$indice]) && !empty($post_detalles['DocDetraccion'][$indice]) ? $post_detalles['DocDetraccion'][$indice] : NULL;
                        $data['Parametro'] = $post_detalles['Parametro'][$indice];
                        $data['PorcRetencion'] = isset($post_detalles['PorcRetencion'][$indice]) && !empty($post_detalles['PorcRetencion'][$indice]) ? $post_detalles['PorcRetencion'][$indice] : 0;
                        $data['IdDetraccion'] = isset($post_detalles['IdDetraccion'][$indice]) && !empty($post_detalles['IdDetraccion'][$indice]) ? $post_detalles['IdDetraccion'][$indice] : NULL;
                        $data['FechaDetraccion'] = isset($post_detalles['FechaDetraccion'][$indice]) && !empty($post_detalles['FechaDetraccion'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FechaDetraccion'][$indice]))) : NULL;
                        $data['Monto'] = $Monto;
                        $data['Saldo'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 && count($post_referencias) > 0 ? $Monto - $SaldoTotalSD_Referencia : $Saldo;
                        $data['CtaCte'] = $post_detalles['CtaCte'][$indice];
                        $data['CodCuentaLibre'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' ? $CodCuentaLibre : NULL;
                        $data['CampoLibre1'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && $contador_parametro_TOTAL == 1 ? $CampoLibre1 : NULL;
                        $data['IdTipOpeDetra'] = isset($post_detalles['IdTipOpeDetra'][$indice]) && !empty($post_detalles['IdTipOpeDetra'][$indice]) ? $post_detalles['IdTipOpeDetra'][$indice] : NULL;
                        $data['CodTipoSN'] = 1;
                        $data['TCcierre'] = $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' ? $ValorTC : NULL;
                        $data['IdenContProy'] = isset($post_detalles['IdenContProy'][$indice]) && !empty($post_detalles['IdenContProy'][$indice]) ? $post_detalles['IdenContProy'][$indice] : NULL;
                        $data['Declarar_Per'] = isset($post_detalles['Declarar_Per'][$indice]) && !empty($post_detalles['Declarar_Per'][$indice]) ? $post_detalles['Declarar_Per'][$indice] : NULL;
                        $data['Declarar_Est'] = isset($post_detalles['Declarar_Est'][$indice]) && !empty($post_detalles['Declarar_Est'][$indice]) ? $post_detalles['Declarar_Est'][$indice] : NULL;
                        $data['IdActivo'] = isset($post_detalles['IdActivo'][$indice]) && !empty($post_detalles['IdActivo'][$indice]) ? $post_detalles['IdActivo'][$indice] : NULL;
                    }

                    if (isset($IdMovRef) && !empty($IdMovRef)) {
                        $data['Saldo'] = 0;
                    }

                    if ((isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice])) &&
                        (isset($post_detalles['IdMovDet'][$indice]) && !empty($post_detalles['IdMovDet'][$indice]))
                    ) {
                        (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $post_detalles['IdMovDet'][$indice], '', '', '', $data);

                        if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') $IdMovDetCampoLibre1[] = $post_detalles['IdMovDet'][$indice];
                    } else {
                        if ((isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice])) &&
                            ($post_detalles['DebeSol'][$indice] != 0 || $post_detalles['HaberSol'][$indice] != 0) &&
                            ($post_detalles['DebeDol'][$indice] != 0 || $post_detalles['HaberDol'][$indice] != 0)
                        ) {
                            $IdMovDet = (new MovimientoDet())->agregar($data);

                            if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') $IdMovDetCampoLibre1[] = $IdMovDet;
                        }
                    }
                }

                if (count($post_referencias) > 0) {
                    $post['IdMovAplica'] = $IdMov;
                    $post['Origen'] = 'VEN_AP';
                    $post['Glosa'] = strtoupper(trim($post['Glosa']));
                    $post['ValorTC'] = $ValorTC;

                    unset($post['IdMov']);

                    if (!empty($IdMovAplica)) {
                        (new MovimientoCab())->actualizar($post['CodEmpresa'], $IdMovAplica, $post);
                    } else {
                        $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                            $post['CodEmpresa'],
                            0,
                            'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                            [],
                            [
                                array('Origen' => array('VEN_AP'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                            ],
                            '',
                            '',
                            ''
                        );

                        $codigo_referencia_maximo = 'DIA' . date('m') . '000001';

                        if ($movimiento_cab[0]['codigo']) {
                            $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                            if (strlen($movimiento_cab[0]['codigo']) == 1) {
                                $codigo_referencia_maximo = 'DIA' . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                                $codigo_referencia_maximo = 'DIA' . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                                $codigo_referencia_maximo = 'DIA' . date('m') . '000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                                $codigo_referencia_maximo = 'DIA' . date('m') . '00' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                                $codigo_referencia_maximo = 'DIA' . date('m') . '0' . $movimiento_cab[0]['codigo'];
                            } else {
                                $codigo_referencia_maximo = 'DIA' . date('m') . $movimiento_cab[0]['codigo'];
                            }
                        }

                        $post['CodTV'] = 'DIA';
                        $post['Codmov'] = $codigo_referencia_maximo;
                        $post['Periodo'] = date('Y');
                        $post['Mes'] = date('m');
                        $post['IdMovAplica'] = $IdMov;
                        $post['Origen'] = 'VEN_AP';
                        $post['Glosa'] = strtoupper(trim($post['Glosa']));
                        $post['Detraccion'] = isset($post['Detraccion']) ? $post['Detraccion'] : 0;
                        $post['ValorTC'] = $ValorTC;

                        $IdMovAplica = (new MovimientoCab())->agregar($post);
                    }

                    $indice_referencia = 0;

                    $total_TotalS_Referencia = 0;
                    $total_TotalD_Referencia = 0;

                    $total_HaberSol_Referencia = 0;
                    $total_HaberDol_Referencia = 0;

                    $saldoDetIdMovDet = array();

                    foreach ($post_referencias['IdMovDet_Referencia'] as $indice => $valor) {
                        $saldoDetIdMovDet[] = $valor;
                    }

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        $CodDocumento = $post_detalles['CodDocumento'][$indice];
                        $SerieDoc = $post_detalles['SerieDoc'][$indice];
                        $NumeroDoc = $post_detalles['NumeroDoc'][$indice];

                        $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                            $post['CodEmpresa'],
                            0,
                            $IdMovAplica,
                            'IdMovDet',
                            [],
                            [
                                array('CodDocumento' => $CodDocumento, 'SerieDoc' => $SerieDoc, 'NumeroDoc' => $NumeroDoc)
                            ],
                            '',
                            ''
                        );

                        if (count($movimiento_det) > 0) {
                            $saldoDetIdMovDet[] = $movimiento_det[0]['IdMovDet'];

                            break;
                        }
                    }

                    $saldoDet = (new SaldoDet())->getSaldoDet($post['CodEmpresa'], 0, $IdMovAplica, 0, 'IdMovDet', [], '', '');

                    foreach ($saldoDet as $indice => $valor) {
                        if (!in_array($valor, $saldoDetIdMovDet)) {
                            (new SaldoDet())->eliminar($post['CodEmpresa'], '', 0, $valor, null);
                        }
                    }

                    foreach ($post_referencias['IdMovDet_Referencia'] as $indice => $valor) {
                        $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], $post_referencias['IdMovDetPadre_Referencia'][$indice], 0, '', [], [], '', '');

                        if (count($movimiento_det) > 0) {
                            $indice_referencia++;

                            $total_HaberSol_Referencia += $post_referencias['TotalS_Referencia'][$indice];
                            $total_HaberDol_Referencia += $post_referencias['TotalD_Referencia'][$indice];

                            $total_TotalS_Referencia += $post_referencias['TotalS_Referencia'][$indice];
                            $total_TotalD_Referencia += $post_referencias['TotalD_Referencia'][$indice];

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $indice_referencia;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMovAplica;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $movimiento_det[0]['CodCuenta'];
                            $data['ValorTC'] = $post_referencias['ValorTC_Referencia'][$indice];
                            $data['HaberSol'] = $post_referencias['TotalS_Referencia'][$indice];
                            $data['HaberDol'] = $post_referencias['TotalD_Referencia'][$indice];
                            $data['CodMoneda'] = $movimiento_det[0]['CodMoneda'];
                            $data['FecEmision'] = isset($movimiento_det[0]['FecEmision']) && !empty($movimiento_det[0]['FecEmision']) ? date('Y-m-d', strtotime(str_replace('/', '-', $movimiento_det[0]['FecEmision']))) : NULL;
                            $data['FecVcto'] = isset($movimiento_det[0]['FecVcto']) && !empty($movimiento_det[0]['FecVcto']) ? date('Y-m-d', strtotime(str_replace('/', '-', $movimiento_det[0]['FecVcto']))) : NULL;
                            $data['IdSocioN'] = $movimiento_det[0]['IdSocioN'];
                            $data['CodDocumento'] = $movimiento_det[0]['CodDocumento'];
                            $data['SerieDoc'] = $movimiento_det[0]['SerieDoc'];
                            $data['NumeroDoc'] = $movimiento_det[0]['NumeroDoc'];
                            $data['NumeroDocF'] = $movimiento_det[0]['NumeroDocF'];
                            $data['Destino'] = 'NO';
                            $data['RegistroSunat'] = 'VENTAS';

                            $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], $valor, 0, '', [], [], '', '');

                            if (count($movimiento_det) > 0) {
                                $IdMovDet = $valor;

                                (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $valor, '', '', '', $data);
                            } else {
                                $IdMovDet = (new MovimientoDet())->agregar($data);
                            }

                            $data = [
                                'CodEmpresa' => $post['CodEmpresa'],
                                'IdMov' => $IdMovAplica,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $post_referencias['IdMovDetPadre_Referencia'][$indice],
                                'Periodo' => $post['Periodo'],
                                'Mes' => $post['Mes'],
                                'TotalDetSol' => $post_referencias['TotalS_Referencia'][$indice],
                                'TotalDetDol' => $post_referencias['TotalD_Referencia'][$indice],
                                'Importado' => NULL,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            $saldoDet = (new SaldoDet())->getSaldoDet($post['CodEmpresa'], 0, 0, $valor, 'IdCobroPago', [], '', '');

                            if (count($saldoDet) > 0) {
                                (new SaldoDet())->actualizar($post['CodEmpresa'], $saldoDet[0]['IdCobroPago'], $data);
                            } else {
                                (new SaldoDet())->agregar($data);
                            }
                        }
                    }

                    $total_DebeSol_Detalle = 0;
                    $total_DebeDol_Detalle = 0;

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        if (
                            $post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice])) &&
                            (($post_detalles['DebeSol'][$indice] != 0 ||
                                $post_detalles['HaberSol'][$indice] != 0) &&
                                ($post_detalles['DebeDol'][$indice] != 0 ||
                                    $post_detalles['HaberDol'][$indice] != 0))
                        ) {
                            $indice_referencia++;

                            $total_DebeSol_Detalle += $post_detalles['DebeSol'][$indice] == 0 ? $post_detalles['HaberSol'][$indice] : $post_detalles['DebeSol'][$indice];
                            $total_DebeDol_Detalle += $post_detalles['DebeDol'][$indice] == 0 ? $post_detalles['HaberDol'][$indice] : $post_detalles['DebeDol'][$indice];

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $indice_referencia;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMovAplica;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                            $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                            $data['DebeSol'] = $post_detalles['DebeSol'][$indice] == 0 ? $post_detalles['HaberSol'][$indice] : $post_detalles['DebeSol'][$indice];
                            $data['DebeDol'] = $post_detalles['DebeDol'][$indice] == 0 ? $post_detalles['HaberDol'][$indice] : $post_detalles['DebeDol'][$indice];
                            $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                            $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                            $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                            $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                            $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                            $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                            $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                            $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                            $data['Destino'] = 'NO';

                            $CodDocumento = $post_detalles['CodDocumento'][$indice];
                            $SerieDoc = $post_detalles['SerieDoc'][$indice];
                            $NumeroDoc = $post_detalles['NumeroDoc'][$indice];

                            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                $post['CodEmpresa'],
                                0,
                                $IdMovAplica,
                                'IdMovDet, CampoLibre1',
                                [],
                                [
                                    array('CodDocumento' => $CodDocumento, 'SerieDoc' => $SerieDoc, 'NumeroDoc' => $NumeroDoc)
                                ],
                                '',
                                ''
                            );

                            if (count($movimiento_det) > 0) {
                                $IdMovDet = $movimiento_det[0]['IdMovDet'];

                                (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $movimiento_det[0]['IdMovDet'], '', '', '', $data);
                            } else {
                                $IdMovDet = (new MovimientoDet())->agregar($data);
                            }

                            $data = [
                                'CodEmpresa' => $post['CodEmpresa'],
                                'IdMov' => $IdMovAplica,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $post_detalles['IdMovDet'][$indice],
                                'Periodo' => $post['Periodo'],
                                'Mes' => $post['Mes'],
                                'TotalDetSol' => $total_TotalS_Referencia,
                                'TotalDetDol' => $total_TotalD_Referencia,
                                'Importado' => NULL,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            $saldoDet = (new SaldoDet())->getSaldoDet($post['CodEmpresa'], 0, 0, $movimiento_det[0]['IdMovDet'], 'IdCobroPago', [], '', '');

                            if (count($saldoDet) > 0) {
                                (new SaldoDet())->actualizar($post['CodEmpresa'], $saldoDet[0]['IdCobroPago'], $data);
                            } else {
                                (new SaldoDet())->agregar($data);
                            }
                        }
                    }

                    $plan_contable = (new PlanContable())->getPlanContable($post['CodEmpresa'], '', '', 'CodCuenta', [], 'UPPER(DescCuenta) = "DIFERENCIA DE CAMBIO"', '');

                    $CodCuentaDiferenciaCambio = 0;

                    if (count($plan_contable) > 0) {
                        $CodCuentaDiferenciaCambio = $plan_contable[0]['CodCuenta'];
                    }

                    if (($total_DebeSol_Detalle != $total_HaberSol_Referencia) || ($total_DebeDol_Detalle != $total_HaberDol_Referencia)) {
                        $indice_referencia++;

                        $diferencia_DebeSol = $total_HaberSol_Referencia - $total_DebeSol_Detalle;
                        $diferencia_DebeDol = $total_HaberDol_Referencia - $total_DebeDol_Detalle;

                        $data = $this->datos_movimiento_det();

                        $data['NumItem'] = $indice_referencia;
                        $data['CodEmpresa'] = $post['CodEmpresa'];
                        $data['IdMov'] = $IdMovAplica;
                        $data['Periodo'] = $post['Periodo'];
                        $data['Mes'] = $post['Mes'];
                        $data['CodCuenta'] = $CodCuentaDiferenciaCambio;
                        $data['ValorTC'] = $post_detalles['ValorTC'][0];
                        $data['DebeSol'] = $diferencia_DebeSol;
                        $data['DebeDol'] = $diferencia_DebeDol;
                        $data['CodMoneda'] = $post_detalles['CodMoneda'][0];
                        $data['FecEmision'] = isset($post_detalles['FecEmision'][0]) && !empty($post_detalles['FecEmision'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][0]))) : NULL;
                        $data['FecVcto'] = isset($post_detalles['FecVcto'][0]) && !empty($post_detalles['FecVcto'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][0]))) : NULL;
                        $data['Destino'] = 'NO';

                        $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                            $post['CodEmpresa'],
                            0,
                            $IdMovAplica,
                            'IdMovDet',
                            [],
                            [
                                array('CodCuenta' => $CodCuentaDiferenciaCambio)
                            ],
                            '',
                            ''
                        );

                        if (count($movimiento_det) > 0) {
                            (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $movimiento_det[0]['IdMovDet'], '', '', '', $data);
                        } else {
                            (new MovimientoDet())->agregar($data);
                        }

                        $amarre = (new Amarre())->getAmarre($post['CodEmpresa'], '', $CodCuentaDiferenciaCambio, 'CuentaDebe, CuentaHaber', [], '', '');

                        if (count($amarre) > 0) {
                            $movimiento_det = (new MovimientoDet())->getMovimientoDet($post['CodEmpresa'], 0, 0, 'IdMovDet, CodCuenta', [], [], 'Destino = "SI"', '');

                            $movimiento_det_amarres = array();

                            foreach ($amarre as $indice => $valor) {
                                $movimiento_det_amarres[] = $valor['CuentaDebe'];
                                $movimiento_det_amarres[] = $valor['CuentaHaber'];
                            }

                            foreach ($movimiento_det as $indice => $valor) {
                                if (!in_array($valor['CodCuenta'], $movimiento_det_amarres)) {
                                    (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMovAplica, $valor['IdMovDet'], '', '', '', '', '');
                                }
                            }

                            foreach ($amarre as $indice => $valor) {
                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $indice_referencia++;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMovAplica;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = $valor['CuentaDebe'];
                                $data['ValorTC'] = $post_detalles['ValorTC'][0];
                                $data['DebeSol'] = $diferencia_DebeSol;
                                $data['DebeDol'] = $diferencia_DebeDol;
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][0];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][0]) && !empty($post_detalles['FecEmision'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][0]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][0]) && !empty($post_detalles['FecVcto'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][0]))) : NULL;
                                $data['Destino'] = 'SI';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['codCuentaDestino'] = $CodCuentaDiferenciaCambio;

                                if (in_array($valor['CuentaDebe'], $movimiento_det_amarres)) {
                                    (new MovimientoDet())->actualizar($post['CodEmpresa'], $IdMovAplica, array_keys($movimiento_det_amarres, $valor['CuentaDebe']), $valor['CuentaDebe'], '', '', $data);
                                } else {
                                    (new MovimientoDet())->agregar($data);
                                }

                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $indice_referencia++;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMovAplica;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = $valor['CuentaHaber'];
                                $data['ValorTC'] = $post_detalles['ValorTC'][0];
                                $data['DebeSol'] = $diferencia_DebeSol;
                                $data['DebeDol'] = $diferencia_DebeDol;
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][0];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][0]) && !empty($post_detalles['FecEmision'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][0]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][0]) && !empty($post_detalles['FecVcto'][0]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][0]))) : NULL;
                                $data['Destino'] = 'SI';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['codCuentaDestino'] = $CodCuentaDiferenciaCambio;

                                if (in_array($valor['CuentaHaber'], $movimiento_det_amarres)) {
                                    (new MovimientoDet())->actualizar($post['CodEmpresa'], $IdMovAplica, array_keys($movimiento_det_amarres, $valor['CuentaHaber']), $valor['CuentaHaber'], '', '', $data);
                                } else {
                                    (new MovimientoDet())->agregar($data);
                                }
                            }
                        } else {
                            (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMovAplica, null, '', '', '', '', 'Destino = "SI"');
                        }
                    } else {
                        if (!empty($IdMovAplica)) {
                            (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMovAplica, $CodCuentaDiferenciaCambio, '', '', '', '', '');
                        }
                    }
                } else {
                    if (!empty($IdMovAplica)) {
                        (new MovimientoCab())->eliminar($post['CodEmpresa'], '', $IdMovAplica);

                        (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMovAplica, null, '', '', '', '', '');

                        (new SaldoDet())->eliminar($post['CodEmpresa'], '', $IdMovAplica, 0, null);
                    }
                }

                if (count($post_banco) > 0 && count($post_referencias) == 0) {
                    $post['IdMovRef'] = $IdMov;
                    $post['Origen'] = 'VEN_CO';
                    $post['Glosa'] = strtoupper(trim($post['Glosa']));
                    $post['ValorTC'] = $ValorTC;

                    unset($post['IdMov']);

                    if (!empty($IdMovRef)) {
                        (new MovimientoCab())->actualizar($post['CodEmpresa'], $IdMovRef, $post);
                    } else {
                        $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                            $post['CodEmpresa'],
                            0,
                            'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                            [],
                            [
                                array('Origen' => array('VEN_CO'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                            ],
                            '',
                            '',
                            ''
                        );

                        $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($post['CodEmpresa'], '', 5, 'CodTV', [], '', '');

                        if (count($tipo_voucher_cab) > 0) {
                            $CodTV = $tipo_voucher_cab[0]['CodTV'];
                        } else {
                            $CodTV = 'CCL';
                        }

                        $codigo_voucher_maximo = $CodTV . date('m') . '000001';

                        if ($movimiento_cab[0]['codigo']) {
                            $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                            if (strlen($movimiento_cab[0]['codigo']) == 1) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '00' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                                $codigo_voucher_maximo = $CodTV . date('m') . '0' . $movimiento_cab[0]['codigo'];
                            } else {
                                $codigo_voucher_maximo = $CodTV . date('m') . $movimiento_cab[0]['codigo'];
                            }
                        }

                        $post['CodTV'] = $CodTV;
                        $post['Codmov'] = $codigo_voucher_maximo;
                        $post['Periodo'] = date('Y');
                        $post['Mes'] = date('m');
                        $post['IdMovRef'] = $IdMov;
                        $post['Origen'] = 'VEN_CO';
                        $post['Glosa'] = strtoupper(trim($post['Glosa']));
                        $post['Detraccion'] = isset($post['Detraccion']) ? $post['Detraccion'] : 0;
                        $post['ValorTC'] = $ValorTC;

                        $IdMovRef = (new MovimientoCab())->agregar($post);
                    }

                    $total_TotalS_1011 = 0;
                    $total_TotalD_1011 = 0;

                    $total_TotalS_Referencia = 0;
                    $total_TotalD_Referencia = 0;

                    $monto_ultimo_CtaCte_Total = 0;

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') {
                            $total_TotalS_1011 += $post_detalles['DebeSol'][$indice];
                            $total_TotalD_1011 += $post_detalles['DebeDol'][$indice];

                            if ($post_detalles['CodMoneda'][$indice] == 'MO001') {
                                $monto_ultimo_CtaCte_Total = $post_detalles['DebeSol'][$indice];
                            } else if ($post_detalles['CodMoneda'][$indice] == 'MO002') {
                                $monto_ultimo_CtaCte_Total = $post_detalles['DebeDol'][$indice];
                            }
                        }
                    }

                    $contador_parametro_TOTAL = 0;

                    $indice_saldoDet = 0;

                    $indice_banco = 0;

                    $indice_parametro = 0;

                    $indice_saldodet = 0;

                    foreach ($post_detalles['NumItem'] as $indice => $valor) {
                        if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]))) {
                            $contador_parametro_TOTAL += 1;

                            $total_TotalS_Referencia = $post_detalles['DebeSol'][$indice];
                            $total_TotalD_Referencia = $post_detalles['DebeDol'][$indice];

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $contador_parametro_TOTAL;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMovRef;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $post_banco['CodCuenta'];
                            $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                            $data['DebeSol'] = $contador_parametro_TOTAL == 1 ? $total_TotalS_1011 : 0;
                            $data['DebeDol'] = $contador_parametro_TOTAL == 1 ? $total_TotalD_1011 : 0;
                            $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                            $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                            $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                            $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                            $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                            $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                            $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                            $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                            $data['Destino'] = 'NO';
                            $data['RegistroSunat'] = 'NINGUNO';
                            $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                            $data['CodTipoPago'] = isset($post_banco['CodTipoPago']) && !empty($post_banco['CodTipoPago']) ? $post_banco['CodTipoPago'] : NULL;
                            $data['PorcRetencion'] = isset($post_detalles['PorcRetencion'][$indice]) && !empty($post_detalles['PorcRetencion'][$indice]) ? $post_detalles['PorcRetencion'][$indice] : 0;
                            $data['IdDetraccion'] = isset($post_detalles['IdDetraccion'][$indice]) && !empty($post_detalles['IdDetraccion'][$indice]) ? $post_detalles['IdDetraccion'][$indice] : NULL;
                            $data['FechaDetraccion'] = isset($post_detalles['FechaDetraccion'][$indice]) && !empty($post_detalles['FechaDetraccion'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FechaDetraccion'][$indice]))) : NULL;
                            $data['Parametro'] = 'BANCO';
                            $data['GlosaDet'] = isset($post_banco['GlosaDet']) && !empty($post_banco['GlosaDet']) ? $post_banco['GlosaDet'] : '';
                            $data['IdTipOpeDetra'] = isset($post_detalles['IdTipOpeDetra'][$indice]) && !empty($post_detalles['IdTipOpeDetra'][$indice]) ? $post_detalles['IdTipOpeDetra'][$indice] : NULL;
                            $data['IdenContProy'] = isset($post_detalles['IdenContProy'][$indice]) && !empty($post_detalles['IdenContProy'][$indice]) ? $post_detalles['IdenContProy'][$indice] : NULL;
                            $data['Declarar_Per'] = isset($post_detalles['Declarar_Per'][$indice]) && !empty($post_detalles['Declarar_Per'][$indice]) ? $post_detalles['Declarar_Per'][$indice] : NULL;
                            $data['Declarar_Est'] = isset($post_detalles['Declarar_Est'][$indice]) && !empty($post_detalles['Declarar_Est'][$indice]) ? $post_detalles['Declarar_Est'][$indice] : NULL;
                            $data['IdActivo'] = isset($post_detalles['IdActivo'][$indice]) && !empty($post_detalles['IdActivo'][$indice]) ? $post_detalles['IdActivo'][$indice] : NULL;
                            $data['Monto'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                            $data['Saldo'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                            $data['NumCheque'] = $post_banco['NumCheque'];

                            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                $post['CodEmpresa'],
                                0,
                                $IdMovRef,
                                'IdMovDet',
                                [],
                                [
                                    array('Parametro' => 'BANCO')
                                ],
                                '',
                                ''
                            );

                            if (count($movimiento_det) > 0) {
                                if (isset($movimiento_det[$indice_banco])) {
                                    (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $movimiento_det[$indice_banco]['IdMovDet'], '', 'BANCO', '', $data);

                                    $indice_banco += 1;
                                } else {
                                    $IdMovDet = (new MovimientoDet())->agregar($data);
                                }
                            } else {
                                (new MovimientoDet())->agregar($data);
                            }

                            $contador_parametro_TOTAL += 1;

                            $data = $this->datos_movimiento_det();

                            $data['NumItem'] = $contador_parametro_TOTAL;
                            $data['CodEmpresa'] = $post['CodEmpresa'];
                            $data['IdMov'] = $IdMovRef;
                            $data['Periodo'] = $post['Periodo'];
                            $data['Mes'] = $post['Mes'];
                            $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                            $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                            $data['HaberSol'] = $post_detalles['DebeSol'][$indice];
                            $data['HaberDol'] = $post_detalles['DebeDol'][$indice];
                            $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                            $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                            $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                            $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                            $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                            $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                            $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                            $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                            $data['Destino'] = 'NO';
                            $data['RegistroSunat'] = 'NINGUNO';
                            $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                            $data['PorcRetencion'] = isset($post_detalles['PorcRetencion'][$indice]) && !empty($post_detalles['PorcRetencion'][$indice]) ? $post_detalles['PorcRetencion'][$indice] : 0;
                            $data['IdDetraccion'] = isset($post_detalles['IdDetraccion'][$indice]) && !empty($post_detalles['IdDetraccion'][$indice]) ? $post_detalles['IdDetraccion'][$indice] : NULL;
                            $data['FechaDetraccion'] = isset($post_detalles['FechaDetraccion'][$indice]) && !empty($post_detalles['FechaDetraccion'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FechaDetraccion'][$indice]))) : NULL;
                            $data['IdTipOpeDetra'] = isset($post_detalles['IdTipOpeDetra'][$indice]) && !empty($post_detalles['IdTipOpeDetra'][$indice]) ? $post_detalles['IdTipOpeDetra'][$indice] : NULL;
                            $data['IdenContProy'] = isset($post_detalles['IdenContProy'][$indice]) && !empty($post_detalles['IdenContProy'][$indice]) ? $post_detalles['IdenContProy'][$indice] : NULL;
                            $data['Declarar_Per'] = isset($post_detalles['Declarar_Per'][$indice]) && !empty($post_detalles['Declarar_Per'][$indice]) ? $post_detalles['Declarar_Per'][$indice] : NULL;
                            $data['Declarar_Est'] = isset($post_detalles['Declarar_Est'][$indice]) && !empty($post_detalles['Declarar_Est'][$indice]) ? $post_detalles['Declarar_Est'][$indice] : NULL;
                            $data['TipoPC'] = 29;
                            $data['IdActivo'] = isset($post_detalles['IdActivo'][$indice]) && !empty($post_detalles['IdActivo'][$indice]) ? $post_detalles['IdActivo'][$indice] : NULL;
                            $data['Monto'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                            $data['Saldo'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;

                            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                $post['CodEmpresa'],
                                0,
                                $IdMovRef,
                                'IdMovDet',
                                [],
                                [],
                                'Parametro IS NULL',
                                ''
                            );

                            if (count($movimiento_det) > 0) {
                                if (isset($movimiento_det[$indice_parametro])) {
                                    $IdMovDet = $movimiento_det[$indice_parametro]['IdMovDet'];

                                    (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $movimiento_det[$indice_parametro]['IdMovDet'], '', '', 'Parametro IS NULL', $data);

                                    $indice_parametro += 1;
                                } else {
                                    $IdMovDet = (new MovimientoDet())->agregar($data);
                                }
                            } else {
                                $IdMovDet = (new MovimientoDet())->agregar($data);
                            }

                            $data = [
                                'CodEmpresa' => $post['CodEmpresa'],
                                'IdMov' => $IdMovRef,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $IdMovDetCampoLibre1[$indice_saldoDet],
                                'Periodo' => $post['Periodo'],
                                'Mes' => $post['Mes'],
                                'TotalDetSol' => $post_detalles['DebeSol'][$indice],
                                'TotalDetDol' => $post_detalles['DebeDol'][$indice],
                                'Importado' => NULL,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            $saldoDet = (new SaldoDet())->getSaldoDet(
                                $post['CodEmpresa'],
                                0,
                                $IdMovRef,
                                0,
                                'IdCobroPago',
                                [],
                                '',
                                ''
                            );

                            if (count($saldoDet) > 0) {
                                if (isset($saldoDet[$indice_saldodet])) {
                                    (new SaldoDet())->actualizar($post['CodEmpresa'], $saldoDet[$indice_saldodet]['IdCobroPago'], $data);

                                    $indice_saldodet += 1;
                                } else {
                                    (new SaldoDet())->agregar($data);
                                }
                            } else {
                                (new SaldoDet())->agregar($data);
                            }

                            $indice_saldoDet++;
                        } else if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] != 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]))) {
                            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                $post['CodEmpresa'],
                                0,
                                $IdMovRef,
                                'IdMovDet, NumItem',
                                [],
                                [
                                    array('CodCuenta' => $post_detalles['CodCuenta'][$indice])
                                ],
                                'Parametro IS NULL',
                                ''
                            );

                            if (count($movimiento_det) > 0) {
                                $IdMovDet = $movimiento_det[0]['IdMovDet'];
                                $NumItem = $movimiento_det[0]['NumItem'];

                                (new MovimientoDet())->eliminar($post['CodEmpresa'], '', 0, $IdMovDet, '', '', '', '', '');

                                $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                    $post['CodEmpresa'],
                                    0,
                                    $IdMovRef,
                                    'IdMovDet',
                                    [],
                                    [
                                        array('Parametro' => 'BANCO')
                                    ],
                                    'NumItem = ' . ($NumItem - 1),
                                    ''
                                );

                                if (count($movimiento_det) > 0) {
                                    $IdMovDet = $movimiento_det[0]['IdMovDet'];

                                    (new MovimientoDet())->eliminar($post['CodEmpresa'], '', 0, $IdMovDet, '', '', '', '', '');
                                }
                            }
                        }
                    }
                } else {
                    $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab(
                        $post['CodEmpresa'],
                        '',
                        1,
                        'CodTV',
                        [],
                        '',
                        ''
                    );

                    $CodTV_Contado = array();

                    foreach ($tipo_voucher_cab as $indice => $valor) {
                        $CodTV_Contado[] = $valor['CodTV'];
                    }

                    if (in_array($post['CodTV'], $CodTV_Contado) && count($post_banco) == 0 && count($post_referencias) == 0) {
                        $post['IdMovRef'] = $IdMov;
                        $post['Origen'] = 'VEN_CO';
                        $post['Glosa'] = strtoupper(trim($post['Glosa']));
                        $post['ValorTC'] = $ValorTC;

                        unset($post['IdMov']);

                        if (!empty($IdMovRef)) {
                            (new MovimientoCab())->actualizar($post['CodEmpresa'], $IdMovRef, $post);
                        } else {
                            $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                                $post['CodEmpresa'],
                                0,
                                'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                                [],
                                [
                                    array('Origen' => array('VEN_CO'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                                ],
                                '',
                                '',
                                ''
                            );

                            $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab(
                                $post['CodEmpresa'],
                                '',
                                5,
                                'CodTV',
                                [],
                                '',
                                ''
                            );

                            if (count($tipo_voucher_cab) > 0) {
                                $CodTV = $tipo_voucher_cab[0]['CodTV'];
                            } else {
                                $CodTV = 'CCL';
                            }

                            $codigo_voucher_maximo = $CodTV . date('m') . '000001';

                            if ($movimiento_cab[0]['codigo']) {
                                $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                                if (strlen($movimiento_cab[0]['codigo']) == 1) {
                                    $codigo_voucher_maximo = $CodTV . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                                } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                                    $codigo_voucher_maximo = $CodTV . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                                } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                                    $codigo_voucher_maximo = $CodTV . date('m') . '000' . $movimiento_cab[0]['codigo'];
                                } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                                    $codigo_voucher_maximo = $CodTV . date('m') . '00' . $movimiento_cab[0]['codigo'];
                                } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                                    $codigo_voucher_maximo = $CodTV . date('m') . '0' . $movimiento_cab[0]['codigo'];
                                } else {
                                    $codigo_voucher_maximo = $CodTV . date('m') . $movimiento_cab[0]['codigo'];
                                }
                            }

                            $post['CodTV'] = $CodTV;
                            $post['Codmov'] = $codigo_voucher_maximo;
                            $post['Periodo'] = date('Y');
                            $post['Mes'] = date('m');
                            $post['IdMovRef'] = $IdMov;
                            $post['Origen'] = 'VEN_CO';
                            $post['Glosa'] = strtoupper(trim($post['Glosa']));
                            $post['Detraccion'] = isset($post['Detraccion']) ? $post['Detraccion'] : 0;
                            $post['ValorTC'] = $ValorTC;

                            $IdMovRef = (new MovimientoCab())->agregar($post);
                        }

                        $total_TotalS_1011 = 0;
                        $total_TotalD_1011 = 0;

                        $total_TotalS_Referencia = 0;
                        $total_TotalD_Referencia = 0;

                        $monto_ultimo_CtaCte_Total = 0;

                        foreach ($post_detalles['NumItem'] as $indice => $valor) {
                            if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL') {
                                $total_TotalS_1011 += $post_detalles['DebeSol'][$indice];
                                $total_TotalD_1011 += $post_detalles['DebeDol'][$indice];

                                if ($post_detalles['CodMoneda'][$indice] == 'MO001') {
                                    $monto_ultimo_CtaCte_Total = $post_detalles['DebeSol'][$indice];
                                } else if ($post_detalles['CodMoneda'][$indice] == 'MO002') {
                                    $monto_ultimo_CtaCte_Total = $post_detalles['DebeDol'][$indice];
                                }
                            }
                        }

                        $contador_parametro_TOTAL = 0;

                        $indice_saldoDet = 0;

                        $indice_banco = 0;

                        $indice_parametro = 0;

                        $indice_saldodet = 0;

                        foreach ($post_detalles['NumItem'] as $indice => $valor) {
                            if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] == 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]))) {
                                $contador_parametro_TOTAL += 1;

                                $total_TotalS_Referencia = $post_detalles['DebeSol'][$indice];
                                $total_TotalD_Referencia = $post_detalles['DebeDol'][$indice];

                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $contador_parametro_TOTAL;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMovRef;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = 1011;
                                $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                                $data['DebeSol'] = $contador_parametro_TOTAL == 1 ? $total_TotalS_1011 : 0;
                                $data['DebeDol'] = $contador_parametro_TOTAL == 1 ? $total_TotalD_1011 : 0;
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                                $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                                $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                                $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                                $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                                $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                                $data['Destino'] = 'NO';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                                $data['Parametro'] = 'BANCO';
                                $data['Monto'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                                $data['Saldo'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;

                                $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                    $post['CodEmpresa'],
                                    0,
                                    $IdMovRef,
                                    'IdMovDet',
                                    [],
                                    [
                                        array('Parametro' => 'BANCO')
                                    ],
                                    '',
                                    ''
                                );

                                if (count($movimiento_det) > 0) {
                                    if (isset($movimiento_det[$indice_banco])) {
                                        (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $movimiento_det[$indice_banco]['IdMovDet'], '', 'BANCO', '', $data);

                                        $indice_banco += 1;
                                    } else {
                                        $IdMovDet = (new MovimientoDet())->agregar($data);
                                    }
                                } else {
                                    (new MovimientoDet())->agregar($data);
                                }

                                $contador_parametro_TOTAL += 1;

                                $data = $this->datos_movimiento_det();

                                $data['NumItem'] = $contador_parametro_TOTAL;
                                $data['CodEmpresa'] = $post['CodEmpresa'];
                                $data['IdMov'] = $IdMovRef;
                                $data['Periodo'] = $post['Periodo'];
                                $data['Mes'] = $post['Mes'];
                                $data['CodCuenta'] = $post_detalles['CodCuenta'][$indice];
                                $data['ValorTC'] = $post_detalles['ValorTC'][$indice];
                                $data['HaberSol'] = $post_detalles['DebeSol'][$indice];
                                $data['HaberDol'] = $post_detalles['DebeDol'][$indice];
                                $data['CodMoneda'] = $post_detalles['CodMoneda'][$indice];
                                $data['FecEmision'] = isset($post_detalles['FecEmision'][$indice]) && !empty($post_detalles['FecEmision'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecEmision'][$indice]))) : NULL;
                                $data['FecVcto'] = isset($post_detalles['FecVcto'][$indice]) && !empty($post_detalles['FecVcto'][$indice]) ? date('Y-m-d', strtotime(str_replace('/', '-', $post_detalles['FecVcto'][$indice]))) : NULL;
                                $data['IdSocioN'] = isset($post_detalles['IdSocioN'][$indice]) && !empty($post_detalles['IdSocioN'][$indice]) ? $post_detalles['IdSocioN'][$indice] : NULL;
                                $data['CodDocumento'] = $post_detalles['CodDocumento'][$indice];
                                $data['SerieDoc'] = $post_detalles['SerieDoc'][$indice];
                                $data['NumeroDoc'] = $post_detalles['NumeroDoc'][$indice];
                                $data['NumeroDocF'] = isset($post_detalles['NumeroDocF'][$indice]) && !empty($post_detalles['NumeroDocF'][$indice]) ? $post_detalles['NumeroDocF'][$indice] : '';
                                $data['Destino'] = 'NO';
                                $data['RegistroSunat'] = 'NINGUNO';
                                $data['CodCondPago'] = isset($post_detalles['CodCondPago'][$indice]) && !empty($post_detalles['CodCondPago'][$indice]) ? $post_detalles['CodCondPago'][$indice] : NULL;
                                $data['TipoPC'] = 29;
                                $data['Monto'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;
                                $data['Saldo'] = count($post_detalles['CtaCte']) > 1 ? $monto_ultimo_CtaCte_Total : 0;

                                $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                    $post['CodEmpresa'],
                                    0,
                                    $IdMovRef,
                                    'IdMovDet',
                                    [],
                                    [],
                                    'Parametro IS NULL',
                                    ''
                                );

                                if (count($movimiento_det) > 0) {
                                    if (isset($movimiento_det[$indice_parametro])) {
                                        $IdMovDet = $movimiento_det[$indice_parametro]['IdMovDet'];

                                        (new MovimientoDet())->actualizar($post['CodEmpresa'], 0, $movimiento_det[$indice_parametro]['IdMovDet'], '', '', 'Parametro IS NULL', $data);

                                        $indice_parametro += 1;
                                    } else {
                                        $IdMovDet = (new MovimientoDet())->agregar($data);
                                    }
                                } else {
                                    $IdMovDet = (new MovimientoDet())->agregar($data);
                                }

                                $data = [
                                    'CodEmpresa' => $post['CodEmpresa'],
                                    'IdMov' => $IdMovRef,
                                    'IdMovDet' => $IdMovDet,
                                    'IdMovDetRef' => $IdMovDetCampoLibre1[$indice_saldoDet],
                                    'Periodo' => $post['Periodo'],
                                    'Mes' => $post['Mes'],
                                    'TotalDetSol' => $post_detalles['DebeSol'][$indice],
                                    'TotalDetDol' => $post_detalles['DebeDol'][$indice],
                                    'Importado' => NULL,
                                    'CodDocRef' => NULL,
                                    'SerieRef' => NULL,
                                    'NumeroRef' => NULL,
                                    'FechaRef' => NULL,
                                    'FlagInterno' => 0
                                ];

                                $saldoDet = (new SaldoDet())->getSaldoDet(
                                    $post['CodEmpresa'],
                                    0,
                                    $IdMovRef,
                                    0,
                                    'IdCobroPago',
                                    [],
                                    '',
                                    ''
                                );

                                if (count($saldoDet) > 0) {
                                    if (isset($saldoDet[$indice_saldodet])) {
                                        (new SaldoDet())->actualizar($post['CodEmpresa'], $saldoDet[$indice_saldodet]['IdCobroPago'], $data);

                                        $indice_saldodet += 1;
                                    } else {
                                        (new SaldoDet())->agregar($data);
                                    }
                                } else {
                                    (new SaldoDet())->agregar($data);
                                }

                                $indice_saldoDet++;
                            } else if ($post_detalles['CtaCte'][$indice] == 1 && $post_detalles['Parametro'][$indice] != 'TOTAL' && (isset($post_detalles['CodCuenta'][$indice]) && !empty($post_detalles['CodCuenta'][$indice]))) {
                                $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                    $post['CodEmpresa'],
                                    0,
                                    $IdMovRef,
                                    'IdMovDet, NumItem',
                                    [],
                                    [
                                        array('CodCuenta' => $post_detalles['CodCuenta'][$indice])
                                    ],
                                    'Parametro IS NULL',
                                    ''
                                );

                                if (count($movimiento_det) > 0) {
                                    $IdMovDet = $movimiento_det[0]['IdMovDet'];
                                    $NumItem = $movimiento_det[0]['NumItem'];

                                    (new MovimientoDet())->eliminar($post['CodEmpresa'], '', 0, $IdMovDet, '', '', '', '', '');

                                    $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                        $post['CodEmpresa'],
                                        0,
                                        $IdMovRef,
                                        'IdMovDet',
                                        [],
                                        [
                                            array('Parametro' => 'BANCO')
                                        ],
                                        'NumItem = ' . ($NumItem - 1),
                                        ''
                                    );

                                    if (count($movimiento_det) > 0) {
                                        $IdMovDet = $movimiento_det[0]['IdMovDet'];

                                        (new MovimientoDet())->eliminar($post['CodEmpresa'], '', 0, $IdMovDet, '', '', '', '', '');
                                    }
                                }
                            }
                        }
                    } else if (!empty($IdMovRef)) {
                        (new MovimientoCab())->eliminar($post['CodEmpresa'], '', $IdMovRef);

                        (new MovimientoDet())->eliminar($post['CodEmpresa'], '', $IdMovRef, 0, '', '', '', '', '');

                        (new SaldoDet())->eliminar($post['CodEmpresa'], '', $IdMovRef, 0, null);
                    }
                }
            }

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

            return redirect()->to(base_url('app/movements/sales/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($IdMov)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                'IdMov',
                [],
                [
                    array('IdMovAplica' => $IdMov)
                ],
                '',
                '',
                ''
            );

            if (count($movimiento_cab) > 0) {
                $IdMovSaldoDet = $movimiento_cab[0]['IdMov'];

                (new SaldoDet())->eliminar($this->CodEmpresa, '', $IdMovSaldoDet, 0, null);

                $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                    $this->CodEmpresa,
                    0,
                    $IdMov,
                    'Monto, CampoLibre1',
                    [],
                    [],
                    'LENGTH(CampoLibre1) != 0',
                    ''
                );

                if (count($movimiento_det) > 0) {
                    $Monto = $movimiento_det[0]['Monto'];
                    $CampoLibre1 = explode(', ', $movimiento_det[0]['CampoLibre1']);

                    foreach ($CampoLibre1 as $indice => $valor) {
                        $CodDocumento = explode('.', explode('-', $valor)[0])[1];
                        $SerieDoc = explode('-', $valor)[1];
                        $NumeroDoc = explode('-', $valor)[2];
                        $FecEmision = str_replace('/', '-', explode('-', $valor)[3]);
                        $FecEmision = date('Y-m-d', strtotime($FecEmision));

                        $where = 'det.IdMov != "' . $IdMov . '" AND DATE(det.FecEmision) = "' . $FecEmision . '" AND det.CodDocumento = "' . $CodDocumento . '" AND det.SerieDoc = "' . $SerieDoc . '" AND det.NumeroDoc = "' . $NumeroDoc . '"';

                        $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                            $this->CodEmpresa,
                            0,
                            'det.IdMovDet, det.Saldo',
                            [
                                array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                            ],
                            [],
                            $where,
                            '',
                            ''
                        );

                        if (count($movimiento_cab) > 0) {
                            $IdMovDet = $movimiento_cab[0]['IdMovDet'];
                            $Saldo = $movimiento_cab[0]['Saldo'] + $Monto;

                            (new MovimientoDet())->actualizar($this->CodEmpresa, 0, $IdMovDet, '', '', '', ['Saldo' => $Saldo]);
                        }
                    }
                }
            }

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, '', [], [array('IdMovRef' => $IdMov)], '', '', '');

            if (count($movimiento_cab) > 0) {
                $IdMovRef = $movimiento_cab[0]['IdMov'];

                (new MovimientoCab())->eliminar($this->CodEmpresa, '', $IdMovRef);

                (new MovimientoDet())->eliminar($this->CodEmpresa, '', $IdMovRef, 0, '', '', '', '', '');

                (new SaldoDet())->eliminar($this->CodEmpresa, '', $IdMovRef, 0, null);
            }

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, '', [], [array('IdMovAplica' => $IdMov)], '', '', '');

            if (count($movimiento_cab) > 0) {
                $IdMovAplica = $movimiento_cab[0]['IdMov'];

                (new MovimientoCab())->eliminar($this->CodEmpresa, '', $IdMovAplica);

                (new MovimientoDet())->eliminar($this->CodEmpresa, '', $IdMovAplica, 0, '', '', '', '', '');

                (new SaldoDet())->eliminar($this->CodEmpresa, '', $IdMovAplica, 0, null);
            }

            (new MovimientoCab())->eliminar($this->CodEmpresa, '', $IdMov);

            (new MovimientoDet())->eliminar($this->CodEmpresa, '', $IdMov, 0, '', '', '', '', '');

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

            return redirect()->to(base_url('app/movements/sales/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function import()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $movimiento_obs = (new MovimientoObs())->getMovimientoObs($this->CodEmpresa, 0, 'VE', 'CUENTA', '', [], '', '');

                $cuentas = array();

                if (count($movimiento_obs) > 0) {
                    foreach ($movimiento_obs as $indice => $valor) {
                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Afecto'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Afecto'] = array('name' => 'Neto', 'label' => 'Neto:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Inafecto'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Inafecto'] = array('name' => 'Inafecto', 'label' => 'Inafecto:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Exonerado'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Exonerado'] = array('name' => 'Exonerado', 'label' => 'Exonerado:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Igv'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Igv'] = array('name' => 'Igv', 'label' => 'Igv:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Icbp'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Icbp'] = array('name' => 'Icbp', 'label' => 'Icbp:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Descuento'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Descuento'] = array('name' => 'Descuento', 'label' => 'Descuento:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Otro_Tributo'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Otro_Tributo'] = array('name' => 'Otro_Tributo', 'label' => 'Otro Trib:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['TotalS'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['TotalS'] = array('name' => 'TotalS', 'label' => 'Total S/:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['TotalD'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['TotalD'] = array('name' => 'TotalD', 'label' => 'Total $:', 'options' => $option_plan_contable);

                        $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['Caja'], 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', [], '', '')[0];

                        $option_plan_contable = '<option data-descripcion="' . $plan_contable['DescCuenta'] . '" value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                        $cuentas['Caja'] = array('name' => 'Caja', 'label' => 'Caja:', 'options' => $option_plan_contable);
                    }
                }

                $movimiento_obs = (new MovimientoObs())->getMovimientoObs(
                    $this->CodEmpresa,
                    0,
                    'VE',
                    'DOCUMENTO',
                    'movimientoobs.CodSunat, movimientoobs.CodDocumento, do.DescDocumento, tc.TipoDatoS',
                    [
                        array('tabla' => 'documento do', 'on' => 'do.CodDocumento = movimientoobs.CodDocumento AND do.CodEmpresa = movimientoobs.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = movimientoobs.CodSunat', 'tipo' => 'inner')
                    ],
                    '',
                    ''
                );

                $documentos = array();

                if (count($movimiento_obs) > 0) {
                    foreach ($movimiento_obs as $indice => $valor) {
                        $tipo_dato = explode('|', $valor['TipoDatoS']);
                        $longitud = isset($tipo_dato[2]) ? $tipo_dato[2] : '';
                        $serie = isset($tipo_dato[3]) ? $tipo_dato[3] : '';
                        $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                        $option_documento = '<option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $valor['CodDocumento'] . '">' . $valor['CodDocumento'] . ' - ' . $valor['DescDocumento'] . '</option>';

                        $documentos[] = array('TC' => $valor['CodSunat'], 'DOC' => $option_documento);
                    }
                }

                $documento = (new Documento())->getDocumento(
                    $this->CodEmpresa,
                    $valor['CodDocumento'],
                    'VE',
                    'CodDocumento',
                    [
                        array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left')
                    ],
                    '',
                    'documento.DescDocumento ASC'
                );

                $documentos_venta = array();

                foreach ($documento as $indice => $valor) {
                    $documentos_venta[] = $valor['CodDocumento'];
                }

                $documentos_venta = json_encode($documentos_venta);

                $monedas = json_encode((new Moneda())->getMoneda('', '', [], '', ''));

                $script = (new Empresa())->generar_script(['split.min.js', 'app/movements/sales/import.js']);

                return viewApp($this->page, 'app/movements/sales/import', [
                    'cuentas' => $cuentas,
                    'documentos' => $documentos,
                    'monedas' => $monedas,
                    'documentos_venta' => $documentos_venta,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel($IdMov)
    {
        try {
            $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                $IdMov,
                '',
                [
                    array('tabla' => 'movimientodet movdet', 'on' => 'movdet.IdMov = movimientocab.IdMov AND movdet.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'moneda mon', 'on' => 'mon.CodMoneda = movdet.CodMoneda', 'tipo' => 'inner'),
                    array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = movimientocab.CodTV AND tvcab.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                '',
                '',
                ''
            )[0];

            $excel = new Excel();

            $excel->creacion('N° de Movimiento - ' . $movimiento_cab['Codmov']);

            $columnas = array('CUENTA', 'NOMBRE', 'AUXILIAR NOMBRE', 'DEBE SOL', 'HABER SOL', 'DEBE DOL', 'HABER DOL', 'T.D', 'DOC', 'FECHA DO');

            $excel->setValues($columnas);

            $excel->body(6, 'columnas');

            $excel->setCelda('A1', 'Número: ' . $movimiento_cab['Codmov']);
            $excel->setCelda('B2', 'Fecha: ' . date('d-m-Y', strtotime($movimiento_cab['FecContable'])));
            $excel->setCelda('A2', 'Voucher: ' . $movimiento_cab['DescVoucher']);
            $excel->setCelda('A3', 'Moneda: ' . $movimiento_cab['DescMoneda']);
            $excel->setCelda('B3', 'T. de Cambio: ' . $movimiento_cab['ValorTC']);
            $excel->setCelda('A4', 'Glosa: ' . $movimiento_cab['Glosa']);

            $result = (new MovimientoDet())->getMovimientoDet(
                $this->CodEmpresa,
                0,
                $IdMov,
                '
                    movimientodet.CodCuenta,
                    pc.DescCuenta,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    movimientodet.DebeSol,
                    movimientodet.HaberSol,
                    movimientodet.DebeDol,
                    movimientodet.HaberDol,
                    movimientodet.CodDocumento,
                    CONCAT(movimientodet.SerieDoc, "-", movimientodet.NumeroDoc) AS NumeroDocumento,
                    movimientodet.FecEmision
                ',
                [
                    array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = movimientodet.CodCuenta AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'socionegocio sn', 'on' => 'sn.IdSocioN = movimientodet.IdSocioN AND sn.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left')
                ],
                [],
                '',
                ''
            );

            foreach ($result as $indice => $valor) {
                $values = array(
                    array('value' => $valor['CodCuenta'], 'style' => 'left'),
                    $valor['DescCuenta'],
                    $valor['razonsocial'],
                    $valor['DebeSol'],
                    $valor['HaberSol'],
                    $valor['DebeDol'],
                    $valor['HaberDol'],
                    $valor['CodDocumento'],
                    $valor['NumeroDocumento'],
                    date('d-m-Y', strtotime($valor['FecEmision']))
                );

                $excel->setValues($values);

                $excel->body(6 + ($indice + 1), 'valor');
            }

            $excel->footer('ventas_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf($IdMov)
    {
        try {
            $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                $IdMov,
                '',
                [
                    array('tabla' => 'movimientodet movdet', 'on' => 'movdet.IdMov = movimientocab.IdMov AND movdet.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'moneda mon', 'on' => 'mon.CodMoneda = movdet.CodMoneda', 'tipo' => 'inner'),
                    array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = movimientocab.CodTV AND tvcab.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                '',
                '',
                ''
            )[0];

            $html = '
            <table>
                <tr>
                    <td><small>Número: ' . $movimiento_cab['Codmov'] . '</small></td>
                    <td><small>Fecha: ' . date('d-m-Y', strtotime($movimiento_cab['FecContable'])) . '</small></td>
                </tr>
                <tr>
                    <td><small>Voucher: ' . $movimiento_cab['DescVoucher'] . '</small></td>
                </tr>
                <tr>
                    <td><small>Moneda: ' . $movimiento_cab['DescMoneda'] . '</small></td>
                    <td><small>T. de Cambio: ' . $movimiento_cab['ValorTC'] . '</small></td>
                </tr>
                <tr>
                    <td><small>Glosa: ' . $movimiento_cab['Glosa'] . '</small></td>
                </tr>
            </table>
            <br>
        ';

            $result = (new MovimientoDet())->getMovimientoDet(
                $this->CodEmpresa,
                0,
                $IdMov,
                '
                    movimientodet.CodCuenta,
                    pc.DescCuenta,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    movimientodet.DebeSol,
                    movimientodet.HaberSol,
                    movimientodet.DebeDol,
                    movimientodet.HaberDol,
                    movimientodet.CodDocumento,
                    CONCAT(movimientodet.SerieDoc, "-", movimientodet.NumeroDoc) AS NumeroDocumento,
                    movimientodet.FecEmision
                ',
                [
                    array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = movimientodet.CodCuenta AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'socionegocio sn', 'on' => 'sn.IdSocioN = movimientodet.IdSocioN AND sn.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left')
                ],
                [],
                '',
                ''
            );

            $columnas = array('CUENTA', 'NOMBRE', 'AUXILIAR NOMBRE', 'DEBE SOL', 'HABER SOL', 'DEBE DOL', 'HABER DOL', 'T.D', 'DOC', 'FECHA DO');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['CodCuenta'] . '</td>
                    <td class="nowrap" align="left">' . $valor['DescCuenta'] . '</td>
                    <td class="nowrap" align="left">' . $valor['razonsocial'] . '</td>
                    <td align="left">' . number_format($valor['DebeSol'], 2, '.', ',') . '</td>
                    <td align="left">' . number_format($valor['HaberSol'], 2, '.', ',') . '</td>
                    <td align="left">' . number_format($valor['DebeDol'], 2, '.', ',') . '</td>
                    <td align="left">' . number_format($valor['HaberDol'], 2, '.', ',') . '</td>
                    <td align="left">' . $valor['CodDocumento'] . '</td>
                    <td align="left">' . $valor['NumeroDocumento'] . '</td>
                    <td align="left">' . date('d-m-Y', strtotime($valor['FecEmision'])) . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('ventas_reporte');
            $pdf->creacion('Voucher de Movimiento - ' . $movimiento_cab['Codmov'], $tr, $html, 'A4', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function editar_detalles($IdMov)
    {
        try {
            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                $this->CodEmpresa,
                0,
                $IdMov,
                '
                    movimientodet.IdMovDet,
                    movimientodet.IdMov,
                    movimientodet.CodEmpresa,
                    movimientodet.NumItem,
                    movimientodet.CodCuenta,
                    movimientodet.CodMoneda,
                    movimientodet.IdSocioN,
                    movimientodet.ValorTC,
                    movimientodet.DebeSol,
                    movimientodet.HaberSol,
                    movimientodet.DebeDol,
                    movimientodet.HaberDol,
                    movimientodet.FecEmision,
                    movimientodet.FecVcto,
                    movimientodet.CodDocumento,
                    movimientodet.SerieDoc,
                    movimientodet.NumeroDoc,
                    movimientodet.NumeroDocF,
                    movimientodet.TipoOperacion,
                    movimientodet.Parametro,
                    movimientodet.PorcRetencion,
                    movimientodet.IdenContProy,
                    movimientodet.Importado,
                    movimientodet.Declarar_Per,
                    movimientodet.CtaCte,
                    movimientodet.IdTipOpeDetra,
                    pc.RelacionCuenta,
                    pc.DescCuenta,
                    mo.Abrev,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                    do.DescDocumento,
                    do.CodSunat,
                    tc.TipoDatoS,
                    an1.IdAnexo,
                    an1.DescAnexo AS DescAnexo1,
                    cp.codcondpago,
                    cp.desccondpago,
                    cc.CodcCosto,
                    cc.DesccCosto,
                    dt.IdDetraccion,
                    dt.Tasa,
                    dt.DescDetra,
                    an2.IdAnexo,
                    an2.DescAnexo AS DescAnexo2,
                    an2.CodInterno AS CodInterno2,
                    an3.IdAnexo,
                    an3.DescAnexo AS DescAnexo3,
                    an3.CodInterno AS CodInterno3,
                    af.IdActivo,
                    af.codActivo,
                    af.descripcion AS descripcionAF
                ',
                [
                    array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = movimientodet.CodCuenta AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'moneda mo', 'on' => 'mo.CodMoneda = movimientodet.CodMoneda', 'tipo' => 'left'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = movimientodet.IdSocioN AND socionegocio.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = movimientodet.CodDocumento AND do.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = do.CodSunat', 'tipo' => 'left'),
                    array('tabla' => 'anexos an1', 'on' => 'an1.IdAnexo = movimientodet.TipoOperacion AND an1.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'condicionpago cp', 'on' => 'cp.codcondpago = movimientodet.CodCondPago AND cp.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'centrocosto cc', 'on' => 'cc.CodcCosto = movimientodet.CodCcosto AND cc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'detraccion dt', 'on' => 'dt.IdDetraccion = movimientodet.PorcDetraccion AND dt.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'anexos an2', 'on' => 'an2.IdAnexo = movimientodet.IdTipOpeDetra AND an2.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'anexos an3', 'on' => 'an3.IdAnexo = movimientodet.Declarar_Est AND an3.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'activosfijos af', 'on' => 'af.IdActivo = movimientodet.IdActivo AND af.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left')
                ],
                [],
                '',
                'movimientodet.NumItem ASC'
            );

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, 'IdMov', [], [array('IdMovAplica' => $IdMov)], '', '', '');

            $IdMovAplica = 0;

            if (count($movimiento_cab) > 0) {
                $IdMovAplica = $movimiento_cab[0]['IdMov'];
            }

            $tr = '';
            $Referencia = 0;
            $Importado = 0;

            foreach ($movimiento_det as $indice => $valor) {
                $readonly_referencia = '';
                $disabled_referencia = '';

                $movimiento_det_referencia = (new MovimientoDet())->getMovimientoDet(
                    $valor['CodEmpresa'],
                    0,
                    0,
                    '',
                    [],
                    [
                        array('FecEmision' => date('Y-m-d', strtotime($valor['FecEmision'])), 'CodDocumento' => $valor['CodDocumento'], 'SerieDoc' => $valor['SerieDoc'], 'NumeroDoc' => $valor['NumeroDoc'])
                    ],
                    'IdMov != ' . $valor['IdMov'] . ' AND IdMov != ' . $IdMovAplica,
                    ''
                );

                if (count($movimiento_det_referencia) > 0 && $IdMovAplica != 0) {
                    $Referencia = 1;
                    $readonly_referencia = 'readonly';
                    $disabled_referencia = 'disabled';
                }

                if(isset($valor['Importado']) && !empty($valor['Importado'])) $Importado = 1;

                $socio_negocio = '<input type="text" name="IdSocioN[]" class="IdSocioN form-control form-control-sm background-transparente border-none" id="IdSocioN' . $valor['NumItem'] . '" readonly />';

                $tipo_dato = explode('|', $valor['TipoDatoS']);
                $longitud = $tipo_dato[2];
                $serie = $tipo_dato[3];
                $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                $tipo_operacion = '<input type="text" name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm background-transparente border-none" id="TipoOperacion' . $valor['NumItem'] . '" readonly />';

                $centro_costo = '<input type="text" name="CodCcosto[]" class="CodCcosto form-control form-control-sm background-transparente border-none" id="CodCcosto' . $valor['NumItem'] . '" readonly />';

                $condicion_pago = '<input type="text" name="CodCondPago[]" class="CodCondPago form-control form-control-sm background-transparente border-none" id="CodCondPago' . $valor['NumItem'] . '" readonly />';

                $documento_retencion = '<input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm background-transparente border-none" id="DocRetencion' . $valor['NumItem'] . '" readonly />';

                $activo_fijo = '<input type="text" name="IdActivo[]" class="IdActivo form-control form-control-sm background-transparente border-none" id="IdActivo' . $valor['NumItem'] . '" readonly />';

                $background_total = '';

                $readonly_numero_final = '';

                if ($valor['CodSunat'] == '01') $readonly_numero_final = 'readonly';

                if (!empty($valor['ruc'])) {
                    $descripcion_IDSocioN = $valor['ruc'] . ' - ' . $valor['razonsocial'];
                } else {
                    $descripcion_IDSocioN = $valor['razonsocial'];
                }

                if ($valor['RelacionCuenta'] == 1 || $valor['RelacionCuenta'] == 3) {
                    $background_total = 'background-total';

                    $socio_negocio = '
                    <select name="IdSocioN[]" class="IdSocioN form-control form-control-sm" id="IdSocioN' . $valor['NumItem'] . '" ' . $disabled_referencia . '>
                        <option value="' . $valor['IdSocioN'] . '" selected>' . $descripcion_IDSocioN . '</option>
                    </select>
                ';

                    $tipo_operacion = '
                    <select name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm" id="TipoOperacion' . $valor['NumItem'] . '">
                        <option value="' . $valor['TipoOperacion'] . '">' . $valor['DescAnexo1'] . '</option>
                    </select>
                ';

                    $condicion_pago = '
                    <select name="CodCondPago[]" class="CodCondPago form-control form-control-sm" id="CodCondPago' . $valor['NumItem'] . '" ' . $disabled_referencia . '>
                        <option value="' . $valor['codcondpago'] . '">' . $valor['desccondpago'] . '</option>
                    </select>
                ';

                    $documento_retencion = '<input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm background-transparente border-none" id="DocRetencion' . $valor['NumItem'] . '" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />';
                }

                if ($valor['RelacionCuenta'] == 2 || $valor['RelacionCuenta'] == 3) {
                    $centro_costo = '
                    <select name="CodCcosto[]" class="CodCcosto form-control form-control-sm" id="CodCcosto' . $valor['NumItem'] . '"' . $disabled_referencia . '>
                        <option value="' . $valor['CodcCosto'] . '">' . $valor['CodcCosto'] . ' - ' . $valor['DesccCosto'] . '</option>
                    </select>
                ';
                }

                if ($valor['RelacionCuenta'] == 4) {
                    $activo_fijo = '
                    <select name="IdActivo[]" class="IdActivo form-control form-control-sm" id="IdActivo' . $valor['NumItem'] . '"' . $disabled_referencia . '>
                        <option value="' . $valor['IdActivo'] . '">' . $valor['codActivo'] . ' - ' . $valor['descripcionAF'] . '</option>
                    </select>
                ';
                }

                if (!empty($valor['IdDetraccion'])) {
                    $descripcion_IdDetraccion = $valor['Tasa'] . '% - ' . $valor['DescDetra'];
                } else {
                    $descripcion_IdDetraccion = '';
                }

                if (!empty($valor['IdTipOpeDetra']) && $valor['IdTipOpeDetra'] != 0) {
                    $descripcion_IdTipOpeDetra = $valor['CodInterno2'] . ' - ' . $valor['DescAnexo2'];
                } else {
                    $descripcion_IdTipOpeDetra = 'Seleccione';
                }

                if (!empty($valor['Declarar_Est']) && $valor['Declarar_Est'] != 0) {
                    $descripcion_Declarar_Est = $valor['CodInterno3'] . ' - ' . $valor['DescAnexo3'];
                } else {
                    $descripcion_Declarar_Est = 'Seleccione';
                }

                $tr .= '<tr id="tr_' . $valor['NumItem'] . '" class="clase_ingreso_ventas ' . $background_total . '">
                <td class="vertical-align-middle text-center ' . $background_total . '">
                    <input type="radio" name="Seleccionar" class="Seleccionar" id="Seleccionar' . $valor['NumItem'] . '">
                </td>
                <td class=" ' . $background_total . '">
                    <input type="hidden" name="IdMovDet[]" value="' . $valor['IdMovDet'] . '" />
                    <input type="text" name="NumItem[]" class="NumItem form-control form-control-sm background-transparente border-none" id="NumItem' . $valor['NumItem'] . '" value="' . $valor['NumItem'] . '" readonly />
                </td>
                <td id="td_ctacte_' . $valor['NumItem'] . '" class="td_ctacte display-none">
                        <input type="hidden" name="CtaCte[]" class="CtaCte" id="CtaCte' . $valor['NumItem'] . '" value="' . $valor['CtaCte'] . '" />
                    </td>
                <td class="' . $background_total . '">
                    <select name="CodCuenta[]" class="CodCuenta form-control form-control-sm" id="CodCuenta' . $valor['NumItem'] . '" onchange="cambiar_cuenta(' . $valor['NumItem'] . ')"' . $disabled_referencia . '>
                        <option value="' . $valor['CodCuenta'] . '">' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <select name="CodMoneda[]" class="CodMoneda form-control form-control-sm" id="CodMoneda' . $valor['NumItem'] . '" onchange="cambiar_moneda(' . $valor['NumItem'] . ')"' . $disabled_referencia . '>
                        <option value="' . $valor['CodMoneda'] . '">' . $valor['Abrev'] . '</option>
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="ValorTC[]" class="ValorTC form-control form-control-sm background-transparente border-none" id="ValorTC' . $valor['NumItem'] . '" value="' . $valor['ValorTC'] . '" oninput="cambiar_tipo_cambio_from_table(' . $valor['NumItem'] . ')" onkeypress="esNumero(event)" readonly />
                </td>
                <td class="background-soles">
                    <input type="text" name="DebeSol[]" class="DebeSol form-control form-control-sm" id="DebeSol' . $valor['NumItem'] . '" value="' . number_format($valor['DebeSol'], 2, '.', '') . '" oninput="cambiar_debe_soles(' . $valor['NumItem'] . ')" onkeydown="cambiar_debe_soles_keydown(event, ' . $valor['NumItem'] . ')" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />
                </td>
                <td class="background-soles">
                    <input type="text" name="HaberSol[]" class="HaberSol form-control form-control-sm" id="HaberSol' . $valor['NumItem'] . '" value="' . number_format($valor['HaberSol'], 2, '.', '') . '" oninput="cambiar_haber_soles(' . $valor['NumItem'] . ')" onkeydown="cambiar_haber_soles_keydown(event, ' . $valor['NumItem'] . ')" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />
                </td>
                <td class="background-dolar">
                    <input type="text" name="DebeDol[]" class="DebeDol form-control form-control-sm" id="DebeDol' . $valor['NumItem'] . '" value="' . number_format($valor['DebeDol'], 2, '.', '') . '" oninput="cambiar_debe_dolar(' . $valor['NumItem'] . ')" onkeydown="cambiar_debe_dolar_keydown(event, ' . $valor['NumItem'] . ')" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />
                </td>
                <td class="background-dolar">
                    <input type="text" name="HaberDol[]" class="HaberDol form-control form-control-sm" id="HaberDol' . $valor['NumItem'] . '" value="' . number_format($valor['HaberDol'], 2, '.', '') . '" oninput="cambiar_haber_dolar(' . $valor['NumItem'] . ')" onkeydown="cambiar_haber_dolar_keydown(event, ' . $valor['NumItem'] . ')" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />
                </td>
                <td class="' . $background_total . '">
                    <div class="input-group input-group-sm input-group-vc">
                        <input type="text" name="FecEmision[]" class="FecEmision form-control mydatepicker" placeholder="dd/mm/yyyy" id="FecEmision' . $valor['NumItem'] . '" data-value="' . date('d/m/Y', strtotime($valor['FecEmision'])) . '" value="' . date('d/m/Y', strtotime($valor['FecEmision'])) . '" onchange="cambiar_fecha_emision(' . $valor['NumItem'] . ')" ' . $readonly_referencia . '>
                        <span class="input-group-text">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                </td>
                <td class="' . $background_total . '">
                    <div class="input-group input-group-sm input-group-vc">
                        <input type="text" name="FecVcto[]" class="FecVcto form-control mydatepicker" placeholder="dd/mm/yyyy" id="FecVcto' . $valor['NumItem'] . '" data-value="' . date('d/m/Y', strtotime($valor['FecVcto'])) . '" value="' . date('d/m/Y', strtotime($valor['FecVcto'])) . '" onchange="cambiar_fecha_vencimiento(' . $valor['NumItem'] . ')" ' . $readonly_referencia . '>
                        <span class="input-group-text">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                </td>
                <td id="td_socio_negocio_' . $valor['NumItem'] . '" class="td_socio_negocio ' . $background_total . '">
                    ' . $socio_negocio . '
                </td>
                <td class="' . $background_total . '">
                    <select name="CodDocumento[]" class="CodDocumento form-control form-control-sm" id="CodDocumento' . $valor['NumItem'] . '" onchange="cambiar_comprobante(' . $valor['NumItem'] . ')" ' . $disabled_referencia . '>
                        <option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $valor['CodDocumento'] . '">' . $valor['CodDocumento'] . ' - ' . $valor['DescDocumento'] . '</option>
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="SerieDoc[]" class="SerieDoc form-control form-control-sm" id="SerieDoc' . $valor['NumItem'] . '" value="' . $valor['SerieDoc'] . '" oninput="verificar_serie_from_table(' . $valor['NumItem'] . ')" onfocusout="cambiar_serie_from_table(' . $valor['NumItem'] . ')" ' . $readonly_referencia . ' />
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="NumeroDoc[]" class="NumeroDoc form-control form-control-sm" id="NumeroDoc' . $valor['NumItem'] . '" value="' . $valor['NumeroDoc'] . '" oninput="cambiar_numero_inicial(' . $valor['NumItem'] . ')" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="NumeroDocF[]" class="NumeroDocF form-control form-control-sm" id="NumeroDocF' . $valor['NumItem'] . '" value="' . $valor['NumeroDocF'] . '" onkeypress="esNumero(event)" ' . $readonly_numero_final . ' ' . $readonly_referencia . ' />
                </td>
                <td id="td_tipo_operacion_' . $valor['NumItem'] . '" class="td_tipo_operacion ' . $background_total . '">
                    ' . $tipo_operacion . '
                </td>
                <td id="td_centro_costo_' . $valor['NumItem'] . '" class="td_centro_costo ' . $background_total . '">
                    ' . $centro_costo . '
                </td>
                <td id="td_condicion_pago_' . $valor['NumItem'] . '" class="td_condicion_pago ' . $background_total . '">
                    ' . $condicion_pago . '
                </td>
                <td id="td_documento_retencion_' . $valor['NumItem'] . '" class="td_documento_retencion ' . $background_total . '">
                    ' . $documento_retencion . '
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="DocDetraccion[]" class="DocDetraccion form-control form-control-sm background-transparente border-none" id="DocDetraccion' . $valor['NumItem'] . '" onkeypress="esNumero(event)" ' . $readonly_referencia . ' />
                </td>
                <td class="' . $background_total . '">
                    <select name="Parametro[]" class="Parametro form-control form-control-sm" id="Parametro' . $valor['NumItem'] . '" onchange="cambiar_parametro(' . $valor['NumItem'] . ')" ' . $disabled_referencia . '>
                        <option value="' . $valor['Parametro'] . '">' . $valor['Parametro'] . '</option>   
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="PorcRetencion[]" class="PorcRetencion form-control form-control-sm background-transparente border-none" id="PorcRetencion' . $valor['NumItem'] . '" value="' . $valor['PorcRetencion'] . '" readonly />
                </td>
                <td class="' . $background_total . '">
                    <select name="IdDetraccion[]" class="IdDetraccion form-control form-control-sm" id="IdDetraccion' . $valor['NumItem'] . '" ' . $disabled_referencia . '>
                        <option value="' . $valor['IdDetraccion'] . '">' . $descripcion_IdDetraccion . '</option>
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <div class="input-group input-group-sm input-group-vc">
                        <input type="text" name="FechaDetraccion[]" class="FechaDetraccion form-control background-transparente border-none mydatepicker" placeholder="dd/mm/yyyy" id="FechaDetraccion' . $valor['NumItem'] . '" readonly">
                        <span class="input-group-text">
                            <i class="fa fa-calendar"></i>
                        </span>
                    </div>
                </td>
                <td class="' . $background_total . '">
                    <select name="IdTipOpeDetra[]" class="IdTipOpeDetra form-control form-control-sm" id="IdTipOpeDetra' . $valor['NumItem'] . '" ' . $readonly_referencia . '>
                        <option value="' . $valor['IdTipOpeDetra'] . '">' . $descripcion_IdTipOpeDetra . '</option>
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <input type="text" name="IdenContProy[]" class="IdenContProy form-control form-control-sm" id="IdenContProy' . $valor['NumItem'] . '" value="' . $valor['IdenContProy'] . '" ' . $readonly_referencia . ' />
                </td>
                <td class="' . $background_total . '">
                    <select name="Declarar_Per[]" class="Declarar_Per form-control form-control-sm" id="Declarar_Per' . $valor['NumItem'] . '">
                        <option value="' . $valor['Declarar_Per'] . '">' . $valor['Declarar_Per'] . '</option>
                    </select>
                </td>
                <td class="' . $background_total . '">
                    <select name="Declarar_Est[]" class="Declarar_Est form-control form-control-sm" id="Declarar_Est' . $valor['NumItem'] . '">
                        ' . $descripcion_Declarar_Est . '
                    </select>
                </td>
                <td id="td_activo_fijo_' . $valor['NumItem'] . '" class="td_activo_fijo ' . $background_total . '">
                    ' . $activo_fijo . '
                </td>
                <td align="center">
                    <button type="button" class="Eliminar btn btn-sm btn-danger" id="Eliminar' . $valor['NumItem'] . '" onclick="eliminar_fila(' . $valor['NumItem'] . ')">Eliminar</button>
                </td>
            </tr>';
            }

            $tr .= '
            <tr class="clase_ingreso_ventas">
                <td class="vertical-align-middle text-center">
                    <input type="radio" name="Seleccionar" class="Seleccionar" id="SeleccionarUltimo" checked>
                </td>
            </tr>';

            return array('tr' => $tr, 'Referencia' => $Referencia, 'Importado' => $Importado);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function editar_detalles_referencias($IdMov)
    {
        try {
            $CampoLibre1 = array();

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, 'IdMov', [],  [array('IdMovAplica' => $IdMov)], '', '', '');

            $tr = '';

            if (count($movimiento_cab) > 0) {
                $IdMovAplica = $movimiento_cab[0]['IdMov'];

                $movimiento_det = (new MovimientoDet())->getMovimientoDet($this->CodEmpresa, 0, $IdMov, 'CampoLibre1', [], [], 'CampoLibre1 IS NOT NULL', '');

                foreach ($movimiento_det as $indice => $valor) {
                    $CampoLibre1 = explode(', ', $valor['CampoLibre1']);
                }

                $where = '';

                foreach ($CampoLibre1 as $indice => $valor) {
                    $CodDocumento = explode('.', explode('-', $valor)[0])[1];
                    $SerieDoc = explode('-', $valor)[1];
                    $NumeroDoc = explode('-', $valor)[2];
                    $FecEmision = str_replace('/', '-', explode('-', $valor)[3]);
                    $FecEmision = date('Y-m-d', strtotime($FecEmision));

                    $where = 'det.IdMov != "' . $IdMovAplica . '" AND DATE(det.FecEmision) = "' . $FecEmision . '" AND det.CodDocumento = "' . $CodDocumento . '" AND det.SerieDoc = "' . $SerieDoc . '" AND det.NumeroDoc = "' . $NumeroDoc . '"';

                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                        $this->CodEmpresa,
                        0,
                        'movimientocab.IdMov, Codmov, det.IdMovDet',
                        [
                            array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                        ],
                        [],
                        $where,
                        '',
                        ''
                    );

                    $IdMov = '';
                    $IdMovDet = '';
                    $Codmov = '';

                    if (count($movimiento_cab) > 0) {
                        $IdMov = $movimiento_cab[0]['IdMov'];
                        $IdMovDet = $movimiento_cab[0]['IdMovDet'];
                        $Codmov = $movimiento_cab[0]['Codmov'];
                    }

                    $where = 'movimientodet.IdMov = "' . $IdMovAplica . '" AND DATE(movimientodet.FecEmision) = "' . $FecEmision . '" AND movimientodet.CodDocumento = "' . $CodDocumento . '" AND movimientodet.SerieDoc = "' . $SerieDoc . '" AND movimientodet.NumeroDoc = "' . $NumeroDoc . '"';

                    $movimientos = (new MovimientoDet())->getMovimientoDet(
                        $this->CodEmpresa,
                        0,
                        0,
                        '
                        do.DescDocumento,
                        movimientodet.CodDocumento,
                        movimientodet.SerieDoc,
                        movimientodet.NumeroDoc,
                        movimientodet.FecEmision,
                        movimientodet.TotalS,
                        movimientodet.TotalD,
                        movimientodet.CodCuenta,
                        cab.Codmov,
                        movimientodet.IdMov,
                        movimientodet.IdMovDet,
                        (SELECT det.IdMovDet FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.CtaCte = 1 AND det.IdMov = ' . $IdMov . ') AS IdMovDetPadre,
                        (SELECT det.CodMoneda FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.CtaCte = 1 AND det.IdMov = ' . $IdMov . ') AS CodMoneda,
                        (SELECT det.Monto FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.CtaCte = 1 AND det.IdMov = ' . $IdMov . ') AS Monto,
                        (SELECT det.Saldo FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.CtaCte = 1 AND det.IdMov = ' . $IdMov . ') AS Saldo,
                        (SELECT det.ValorTC FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.CtaCte = 1 AND det.IdMov = ' . $IdMov . ') AS ValorTC
                    ',
                        [
                            array('tabla' => 'movimientocab cab', 'on' => 'cab.IdMov = movimientodet.IdMov AND cab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                            array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = cab.CodTV AND tvcab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                            array('tabla' => 'documento do', 'on' => 'do.CodDocumento = movimientodet.CodDocumento AND do.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner')
                        ],
                        [],
                        $where,
                        ''
                    );

                    if (count($movimientos) > 0) {
                        foreach ($movimientos as $indice_auxiliar => $valor) {
                            $saldo_soles = 0;
                            $saldo_dolares = 0;

                            if ($valor['CodMoneda'] == 'MO001') {
                                $saldo_soles = number_format($valor['Monto'] - $valor['Saldo'], 2, '.', '');
                                $saldo_dolares = number_format(($valor['Monto'] - $valor['Saldo']) / $valor['ValorTC'], 2, '.', '');
                            } else if ($valor['CodMoneda'] == 'MO002') {
                                $saldo_soles = number_format(($valor['Monto'] - $valor['Saldo']) * $valor['ValorTC'], 2, '.', '');
                                $saldo_dolares = number_format($valor['Monto'] - $valor['Saldo'], 2, '.', '');
                            }

                            $tr .= '
                            <tr class="tr_referencia" id="tr_referencia_existente_' . $IdMov . '">
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="hidden" name="IdMov_Referencia[]" value="' . $valor['IdMov'] . '" />
                                    <input type="hidden" name="IdMovDet_Referencia[]" value="' . $valor['IdMovDet'] . '" />
                                    <input type="hidden" name="IdMovDetPadre_Referencia[]" value="' . $valor['IdMovDetPadre'] . '" />
                                    <input type="hidden" name="CodDocumento_Referencia[]" value="' . $valor['CodDocumento'] . '" />
                                    Existente
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['DescDocumento'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" name="SerieDoc_Referencia[]" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['SerieDoc'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" name="NumeroDoc_Referencia[]" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['NumeroDoc'] . '" readonly />
                                </td>
                                <td onclick="referencia_existente(' . $IdMov . ')">
                                    <div class="input-group input-group-sm input-group-vc">
                                        <input type="text" name="FecEmision_Referencia[]" class="form-control background-transparente border-none mydatepicker" placeholder="dd/mm/yyyy" value="' . date('d/m/Y', strtotime($valor['FecEmision'])) . '" readonly>
                                        <span class="input-group-text border-none">
                                            <i class="fa fa-calendar"></i>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" name="TotalS_Referencia[]" class="referencia_TotalS form-control form-control-sm" id="TotalSReferenciaExistente' . $IdMov . '" data-value="' . $saldo_soles . '" value="' . $saldo_soles . '" oninput="cambiar_TotalS_referencia_existente(' . $IdMov . ')" onkeypress="esNumero(event)" />
                                </td>
                                <td>
                                    <input type="text" name="TotalD_Referencia[]" class="referencia_TotalD form-control form-control-sm" id="TotalDReferenciaExistente' . $IdMov . '" data-value="' . $saldo_dolares . '" value="' . $saldo_dolares . '" oninput="cambiar_TotalD_referencia_existente(' . $IdMov . ')" onkeypress="esNumero(event)" />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" name="ValorTC_Referencia[]" role="button" class="ValorTCReferenciaExistente form-control form-control-sm background-transparente border-none" id="ValorTCReferenciaExistente' . $IdMov . '" value="' . $valor['ValorTC'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['CodCuenta'] . '" readonly />
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="text-dark underline text-decoration-none" onclick="consulta_movimiento(' . $IdMov . ')">' . $Codmov . '</a>
                                </td>
                            </tr>
                        ';
                        }
                    }
                }
            }

            return $tr;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_detalles_index()
    {
        try {
            $IdMov = intval($this->request->getPost('IdMov'));

            $result = (new MovimientoDet())->getMovimientoDet(
                $this->CodEmpresa,
                0,
                $IdMov,
                (new SocioNegocio())->getRazonSocial(false) . 'AS razonsocial,
                    IF(movimientodet.CodMoneda = "MO001", "S/", "$") AS Moneda,
                    movimientodet.ValorTC,
                    movimientodet.FecEmision,
                    movimientodet.FecVcto,
                    movimientodet.CodDocumento,
                    movimientodet.SerieDoc,
                    movimientodet.NumeroDoc,
                    movimientodet.CodCuentaLibre,
                    IF(movimientodet.CodMoneda = "MO001", BaseImpSunatS, BaseImpSunatD) AS BaseImpSunat,
                    IF(movimientodet.CodMoneda = "MO001", InafectoS, InafectoD) AS Inafecto,
                    IF(movimientodet.CodMoneda = "MO001", ExoneradoS, ExoneradoD) AS Exonerado,
                    IF(movimientodet.CodMoneda = "MO001", IGVSunatS, IGVSunatD) AS IGVSunat,
                    IF(movimientodet.CodMoneda = "MO001", PercepcionS, PercepcionD) AS Percepcion,
                    IF(movimientodet.CodMoneda = "MO001", OtroTributoS, OtroTributoD) AS OtroTributo,
                    IF(movimientodet.CodMoneda = "MO001", TotalS, TotalD) AS Total',
                [
                    array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = movimientodet.CodCuenta AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = movimientodet.IdSocioN AND socionegocio.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'moneda mo', 'on' => 'mo.CodMoneda = movimientodet.CodMoneda', 'tipo' => 'inner'),
                ],
                [],
                'pc.RelacionCuenta = 1',
                ''
            );

            $table = '
            <div class="card-header py-3">
                <span class="titulo-header-card">Detalles</span>
            </div>
            <div class="card-body">
                <div class="table-responsive-md table-wrapper">
                    <table class="table table-sm table-bordered" id="tabla_ingreso_ventas_detalles" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>M.</th>
                                <th>T. C.</th>
                                <th>F. Emisión</th>
                                <th>F. Vcmto</th>
                                <th>Comp.</th>
                                <th>Serie</th>
                                <th>Número</th>
                                <th>Cta</th>
                                <th>Bas. Imp</th>
                                <th>Inafecto</th>
                                <th>Exonerado</th>
                                <th>Igv</th>
                                <th>Percepción</th>
                                <th>Otro Tributo</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
        ';

            foreach ($result as $indice => $valor) {
                $table .=
                    '<tr>
                    <td>' . $valor['razonsocial'] . '</td>
                    <td>' . $valor['Moneda'] . '</td>
                    <td>' . $valor['ValorTC'] . '</td>
                    <td>' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($valor['FecVcto'])) . '</td>
                    <td>' . $valor['CodDocumento'] . '</td>
                    <td>' . $valor['SerieDoc'] . '</td>
                    <td>' . $valor['NumeroDoc'] . '</td>
                    <td>' . $valor['CodCuentaLibre'] . '</td>
                    <td>' . number_format($valor['BaseImpSunat'], 2, '.', ',') . '</td>
                    <td>' . number_format($valor['Inafecto'], 2, '.', ',') . '</td>
                    <td>' . number_format($valor['Exonerado'], 2, '.', ',') . '</td>
                    <td>' . number_format($valor['IGVSunat'], 2, '.', ',') . '</td>
                    <td>' . number_format($valor['Percepcion'], 2, '.', ',') . '</td>
                    <td>' . number_format($valor['OtroTributo'], 2, '.', ',') . '</td>
                    <td>' . number_format($valor['Total'], 2, '.', ',') . '</td>
                </tr>';
            }

            $table .= '
                        </tbody>
                    </table>
                </div>
            </div>
        ';

            echo $table;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_detalles_PA()
    {
        try {
            $IdMov = intval($this->request->getPost('IdMov'));

            $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                $IdMov,
                '
                    movimientocab.Periodo, 
                    movimientocab.Codmov, 
                    tvcab.DescVoucher
                ',
                [
                    array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = movimientocab.CodTV AND tvcab.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                '',
                '',
                ''
            )[0];

            $titulo = '<b>' . $movimiento_cab['DescVoucher'] . ' (' . $movimiento_cab['Periodo'] . '-' . $movimiento_cab['Codmov'] . ') <span class="float-end">Ingreso de Ventas</span></b>';

            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                $this->CodEmpresa,
                0,
                $IdMov,
                '
                    movimientodet.NumItem, 
                    movimientodet.CodCuenta, 
                    movimientodet.ValorTC, 
                    movimientodet.DebeSol, 
                    movimientodet.HaberSol, 
                    movimientodet.DebeDol, 
                    movimientodet.HaberDol, 
                    movimientodet.FecEmision, 
                    movimientodet.FecVcto, 
                    movimientodet.SerieDoc, 
                    movimientodet.NumeroDoc, 
                    movimientodet.NumeroDocF, 
                    movimientodet.DocRetencion, 
                    movimientodet.DocDetraccion, 
                    movimientodet.Parametro, 
                    movimientodet.PorcRetencion, 
                    movimientodet.PorcDetraccion, 
                    movimientodet.FechaDetraccion, 
                    movimientodet.IdenContProy, 
                    movimientodet.Declarar_Per,
                    pc.DescCuenta, 
                    pc.RelacionCuenta, 
                    IF(LENGTH(sn.razonsocial) = 0, CONCAT(sn.Nom1, " ", IF(LENGTH(sn.Nom2) = 0, "", CONCAT(sn.Nom2, " ")), sn.ApePat, " ", sn.ApeMat), sn.razonsocial) AS razonsocial, 
                    mo.Abrev, 
                    do.DescDocumento, 
                    an1.DescAnexo AS TipoOperacion, 
                    cc.DesccCosto,
                    cp.desccondpago,
                    an2.DescAnexo AS IdTipOpeDetra, 
                    an3.DescAnexo AS Declarar_Est,
                    af.descripcion AS IdActivo,
                    an4.DescAnexo AS IdOperacionAF
                ',
                [
                    array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = movimientodet.CodCuenta AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio sn', 'on' => 'sn.IdSocioN = movimientodet.IdSocioN AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'moneda mo', 'on' => 'mo.CodMoneda = movimientodet.CodMoneda', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = movimientodet.CodDocumento AND do.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'anexos an1', 'on' => 'an1.IdAnexo = movimientodet.TipoOperacion AND an1.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'centrocosto cc', 'on' => 'cc.CodcCosto = movimientodet.CodCcosto AND cc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'condicionpago cp', 'on' => 'cp.codcondpago = movimientodet.CodCondPago AND cp.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'anexos an2', 'on' => 'an2.IdAnexo = movimientodet.IdTipOpeDetra AND an2.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'anexos an3', 'on' => 'an3.IdAnexo = movimientodet.Declarar_Est AND an3.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'activosfijos af', 'on' => 'af.IdActivo = movimientodet.IdActivo AND af.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'anexos an4', 'on' => 'an4.IdAnexo = movimientodet.IdOperacionAF AND an4.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left')
                ],
                [],
                '',
                'movimientodet.NumItem ASC'
            );

            $tabla = '
                <div class="table-responsive-md table-wrapper">
                    <table class="table table-sm table-bordered" id="tabla_ingreso_ventas">
                        <thead>
                            <th>Item</th>
                            <th>Cuenta</th>
                            <th>Descripción</th>
                            <th>Moneda</th>
                            <th>Tipo Cambio</th>
                            <th>Debe Soles</th>
                            <th>Haber Soles</th>
                            <th>Debe Dolar</th>
                            <th>Haber Dolar</th>
                            <th>Fecha Emisión</th>
                            <th>Fecha Vencimiento</th>
                            <th>Razón Social</th>
                            <th>Documento</th>
                            <th>Serie</th>
                            <th>Número</th>
                            <th>Número Final</th>
                            <th>Tipo de Operación</th>
                            <th>Centro de Costo</th>
                            <th>Condición de Pago</th>
                            <th>Doc. Retención</th>
                            <th>Doc. Detracción</th>
                            <th>Parametro</th>
                            <th>% Retención</th>
                            <th>% Detracción</th>
                            <th>Fecha Detracción</th>
                            <th>TO. Det</th>
                            <th>35-Contrato-Proyecto</th>
                            <th>Periodo a Declarar</th>
                            <th>Estado a Declarar</th>
                            <th>Activo Fijo</th>
                            <th>Operación a Fijo</th>
                        </thead>
                        <tbody>
            ';

            $total_DebeSol = 0;
            $total_HaberSol = 0;
            $total_DebeDol = 0;
            $total_HaberDol = 0;

            foreach ($movimiento_det as $indice => $valor) {
                $total_DebeSol += $valor['DebeSol'];
                $total_HaberSol += $valor['HaberSol'];
                $total_DebeDol += $valor['DebeDol'];
                $total_HaberDol += $valor['HaberDol'];

                $background = $valor['RelacionCuenta'] == 1 || $valor['RelacionCuenta'] == 3 ? 'background-total-pa' : '';

                $tabla .= '
                    <tr class="' . $background . '">
                        <td class="' . $background . '">' . $valor['NumItem'] . '</td>
                        <td class="' . $background . '">' . $valor['CodCuenta'] . '</td>
                        <td class="' . $background . '">' . $valor['DescCuenta'] . '</td>
                        <td class="' . $background . '">' . $valor['Abrev'] . '</td>
                        <td class="' . $background . '">' . $valor['ValorTC'] . '</td>
                        <td class="' . $background . '">' . number_format($valor['DebeSol'], 2, '.', ',') . '</td>
                        <td class="' . $background . '">' . number_format($valor['HaberSol'], 2, '.', ',') . '</td>
                        <td class="' . $background . '">' . number_format($valor['DebeDol'], 2, '.', ',') . '</td>
                        <td class="' . $background . '">' . number_format($valor['HaberDol'], 2, '.', ',') . '</td>
                        <td class="' . $background . '">' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                        <td class="' . $background . '">' . date('d/m/Y', strtotime($valor['FecVcto'])) . '</td>
                        <td class="' . $background . '">' . $valor['razonsocial'] . '</td>
                        <td class="' . $background . '">' . $valor['DescDocumento'] . '</td>
                        <td class="' . $background . '">' . $valor['SerieDoc'] . '</td>
                        <td class="' . $background . '">' . $valor['NumeroDoc'] . '</td>
                        <td class="' . $background . '">' . $valor['NumeroDocF'] . '</td>
                        <td class="' . $background . '">' . $valor['TipoOperacion'] . '</td>
                        <td class="' . $background . '">' . $valor['DesccCosto'] . '</td>
                        <td class="' . $background . '">' . $valor['desccondpago'] . '</td>
                        <td class="' . $background . '">' . $valor['DocRetencion'] . '</td>
                        <td class="' . $background . '">' . $valor['DocDetraccion'] . '</td>
                        <td class="' . $background . '">' . $valor['Parametro'] . '</td>
                        <td class="' . $background . '">' . number_format($valor['PorcRetencion'], 2, '.', ',') . '</td>
                        <td class="' . $background . '">' . number_format($valor['PorcDetraccion'], 2, '.', ',') . '</td>
                        <td class="' . $background . '">' . $valor['FechaDetraccion'] . '</td>
                        <td class="' . $background . '">' . $valor['IdTipOpeDetra'] . '</td>
                        <td class="' . $background . '">' . $valor['IdenContProy'] . '</td>
                        <td class="' . $background . '">' . $valor['Declarar_Per'] . '</td>
                        <td class="' . $background . '">' . $valor['Declarar_Est'] . '</td>
                        <td class="' . $background . '">' . $valor['IdActivo'] . '</td>
                        <td class="' . $background . '">' . $valor['IdOperacionAF'] . '</td>
                    </tr>
                ';
            }

            $tabla .= '
                    </tbody>
                </table>
            </div>
            ';

            $totales = '
                <table class="table table-sm table-bordered mt-3">
                    <tbody>
                        <tr>
                            <td>' . number_format($total_DebeSol, 2, '.', '') . '</td>
                            <td>' . number_format($total_HaberSol, 2, '.', '') . '</td>
                            <td>' . number_format($total_DebeDol, 2, '.', '') . '</td>
                            <td>' . number_format($total_HaberDol, 2, '.', '') . '</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <th>Debe Soles</th>
                        <th>Haber Soles</th>
                        <th>Debe Dolar</th>
                        <th>Haber Dolar</th>
                    </tfoot>
                </table>
            ';

            $tabla .= $totales;

            echo json_encode(array('titulo' => $titulo, 'tabla' => $tabla));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function parametros_CodTV()
    {
        try {
            $post = $this->request->getPost();

            $tipo_voucher_det = (new TipoVoucherDet())->getTipoVoucherDet($this->CodEmpresa, '', '', $post['CodTV'], '', [], '', '');

            $parametros_CodTV = array();

            $cantidad_Afecto = 1;
            $cantidad_Anticipo = 1;
            $cantidad_Descuento = 1;
            $cantidad_Igv = 1;
            $cantidad_Percepcion = 1;
            $cantidad_Isc = 1;
            $cantidad_Inafecto = 1;
            $cantidad_Exonerado = 1;
            $cantidad_Total = 1;
            $cantidad_Otro_tributo = 1;
            $cantidad_Icbp = 1;

            foreach ($tipo_voucher_det as $indice => $valor) {
                switch ($valor['Parametro']) {
                    case 'AFECTO':
                        if ($cantidad_Afecto == 1) {
                            $parametros_CodTV['Afecto'] = $valor['CodCuenta'];

                            $cantidad_Afecto++;
                        }

                        break;
                    case 'ANTICIPO':
                        if ($cantidad_Anticipo == 1) {
                            $parametros_CodTV['Anticipo'] = $valor['CodCuenta'];

                            $cantidad_Anticipo++;
                        }

                        break;
                    case 'DESCUENTO':
                        if ($cantidad_Descuento == 1) {
                            $parametros_CodTV['Descuento'] = $valor['CodCuenta'];

                            $cantidad_Descuento++;
                        }

                        break;
                    case 'IGV':
                        if ($cantidad_Igv == 1) {
                            $parametros_CodTV['Igv'] = $valor['CodCuenta'];

                            $cantidad_Igv++;
                        }

                        break;
                    case 'PERCEPCION':
                        if ($cantidad_Percepcion == 1) {
                            $parametros_CodTV['Percepcion'] = $valor['CodCuenta'];

                            $cantidad_Percepcion++;
                        }

                        break;
                    case 'ISC':
                        if ($cantidad_Isc == 1) {
                            $parametros_CodTV['Isc'] = $valor['CodCuenta'];

                            $cantidad_Isc++;
                        }

                        break;
                    case 'INAFECTO':
                        if ($cantidad_Inafecto == 1) {
                            $parametros_CodTV['Inafecto'] = $valor['CodCuenta'];

                            $cantidad_Inafecto++;
                        }

                        break;
                    case 'EXONERADO':
                        if ($cantidad_Exonerado == 1) {
                            $parametros_CodTV['Exonerado'] = $valor['CodCuenta'];

                            $cantidad_Exonerado++;
                        }

                        break;
                    case 'TOTAL':
                        if ($cantidad_Total == 1) {
                            $parametros_CodTV['Total'] = $valor['CodCuenta'];

                            $cantidad_Total++;
                        }

                        break;
                    case 'OTRO TRIBUTO':
                        if ($cantidad_Otro_tributo == 1) {
                            $parametros_CodTV['Total'] = $valor['CodCuenta'];

                            $cantidad_Otro_tributo++;
                        }

                        break;
                    case 'ICBP':
                        if ($cantidad_Icbp == 1) {
                            $parametros_CodTV['Icbp'] = $valor['CodCuenta'];

                            $cantidad_Icbp++;
                        }

                        break;
                }
            }

            echo json_encode($parametros_CodTV);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_tipo_cambio()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'consultar') {
                $FecEmision = date('Y-m-d', strtotime(strval($this->request->getPost('FecEmision'))));

                $tipo_cambio = (new TipoCambio())->getTipoCambio($this->CodEmpresa, $FecEmision, 'ValorVenta', [], '', '');

                if (count($tipo_cambio) > 0) {
                    echo $tipo_cambio[0]['ValorVenta'];
                } else {
                    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=' . $FecEmision,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 2,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Referer: https://apis.net.pe/tipo-de-cambio-sunat-api',
                            'Authorization: Bearer ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    echo json_decode($response)->venta ?? 0.000;
                }
            } else if ($tipo == 'consultar_database') {
                $FecEmision = date('Y-m-d', strtotime(str_replace('/', '-', strval($this->request->getPost('FecEmision')))));

                $tipo_cambio = (new TipoCambio())->getTipoCambio($this->CodEmpresa, $FecEmision, 'ValorVenta', [], '', '');

                $ValorTC = 1.000;

                if (count($tipo_cambio) > 0) $ValorTC = $tipo_cambio[0]['ValorVenta'];

                echo $ValorTC;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_sunat()
    {
        try {
            $numero_documento = trim(strval($this->request->getPost('numero_documento')));
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'verificar') {
                $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], 'ruc = "' . $numero_documento . '" OR docidentidad = "' . $numero_documento . '"', '');

                if (count($socio_negocio) == 0) {
                    echo json_encode(array('existe' => false));
                } else {
                    echo json_encode(array('existe' => true));
                }
            } else if ($tipo == 'consultar') {
                if (strlen($numero_documento) == 11) {
                    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $numero_documento,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Referer: http://apis.net.pe/api-ruc',
                            'Authorization: Bearer ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $empresa = json_decode($response);

                    echo json_encode($empresa);
                } elseif (strlen($numero_documento) == 8) {
                    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $numero_documento,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 2,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Referer: https://apis.net.pe/consulta-dni-api',
                            'Authorization: Bearer ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $persona = json_decode($response);

                    echo json_encode($persona);
                }
            } else if ($tipo == 'registrar') {
                $datos = $this->request->getPost('datos');

                $this->db->disableForeignKeyChecks();

                $this->db->transBegin();

                $post['CodEmpresa'] = $this->CodEmpresa;

                $post['razonsocial'] = strtoupper(trim($datos['nombre']));
                $post['fecingreso'] = date('Y-m-d H:i:s');

                if (strlen($datos['numeroDocumento']) == 11) {
                    $post['ruc'] = $datos['numeroDocumento'];
                    $post['CodTipoDoc'] = 6;
                    $post['CodTipPer'] = '02';
                } else {
                    $post['Nom1'] = ucwords(strtolower(trim($datos['nombres'])));
                    $post['ApePat'] = ucwords(strtolower(trim($datos['apellidoPaterno'])));
                    $post['ApeMat'] = ucwords(strtolower(trim($datos['apellidoMaterno'])));
                    $post['docidentidad'] = $datos['numeroDocumento'];
                    $post['CodTipoDoc'] = 1;
                    $post['CodTipPer'] = '01';
                }

                $post['Idestado'] = 11;
                $post['IdCondicion'] = 12;
                $post['direccion1'] = $datos['direccion'];
                $post['codubigeo'] = '011501003';

                $IdSocioN = (new SocioNegocio())->agregar($post);

                $tipo_socio_negocio = (new TipoSocioNegocio())->getTipoSocioNegocio('', 'CodTipoSN', [], 'LOWER(DescTipoSN) = "cliente"', '')[0];

                $data = [
                    'CodTipoSN' => $tipo_socio_negocio['CodTipoSN'],
                    'IdSocioN' => $IdSocioN
                ];

                (new SocioNegocioXTipo())->agregar($data);

                if ($this->db->transStatus() === FALSE) {
                    $this->db->transRollback();

                    $result = false;
                } else {
                    $this->db->transCommit();

                    $result = true;
                }

                if ($result) {
                    $socio_negocio = (new SocioNegocio())->getSocioNegocio(
                        $this->CodEmpresa,
                        $IdSocioN,
                        'socionegocio.IdSocioN AS id, ' . (new SocioNegocio())->getRazonSocial(true) . ' AS text, ' . (new SocioNegocio())->getNumeroDocumento() . ' AS NumeroDocumento, ' . (new SocioNegocio())->getRazonSocial(false) . ' AS RazonSocial',
                        [],
                        '',
                        ''
                    )[0];

                    $option_socio_negocio = '<option data-numero-documento="' . $socio_negocio['NumeroDocumento'] . '" data-razon-social="' . $socio_negocio['RazonSocial'] . '" value="' . $socio_negocio['id'] . '">' . $socio_negocio['text'] . '</option>';

                    echo json_encode(array('estado' => true, 'id' => $IdSocioN, 'option' => $option_socio_negocio));
                } else {
                    echo json_encode(array('estado' => false));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registrar_socio_negocio()
    {
        try {
            $post = $this->request->getPost();

            $post['CodEmpresa'] = $this->CodEmpresa;
            $post['razonsocial'] = strtoupper(trim($post['razonsocial']));
            $post['Nom1'] = ucwords(strtolower(trim($post['Nom1'])));
            $post['Nom2'] = ucwords(strtolower(trim($post['Nom2'])));
            $post['ApePat'] = ucwords(strtolower(trim($post['ApePat'])));
            $post['ApeMat'] = ucwords(strtolower(trim($post['ApeMat'])));
            $post['Idestado'] = 11;
            $post['codubigeo'] = '011501003';

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $IdSocioN = (new SocioNegocio())->agregar($post);

            $tipo_socio_negocio = (new TipoSocioNegocio())->getTipoSocioNegocio('', 'CodTipoSN', [], 'LOWER(DescTipoSN) = "cliente"', '')[0];

            $data = [
                'CodTipoSN' => $tipo_socio_negocio['CodTipoSN'],
                'IdSocioN' => $IdSocioN
            ];

            (new SocioNegocioXTipo())->agregar($data);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $socio_negocio = (new SocioNegocio())->getSocioNegocio(
                    $this->CodEmpresa,
                    $IdSocioN,
                    'socionegocio.IdSocioN AS id, ' . (new SocioNegocio())->getRazonSocial(true) . ' AS text, ' . (new SocioNegocio())->getNumeroDocumento() . ' AS NumeroDocumento, ' . (new SocioNegocio())->getRazonSocial(false) . ' AS RazonSocial',
                    [],
                    '',
                    ''
                )[0];

                $option_socio_negocio = '<option data-numero-documento="' . $socio_negocio['NumeroDocumento'] . '" data-razon-social="' . $socio_negocio['RazonSocial'] . '" value="' . $socio_negocio['id'] . '">' . $socio_negocio['text'] . '</option>';

                echo json_encode(array('estado' => true, 'id' => $IdSocioN, 'option' => $option_socio_negocio));
            } else {
                echo json_encode(array('estado' => false));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_movimientos_nota_credito()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'nuevo') {
                $IdSocioN = $this->request->getPost('IdSocioN');

                $movimientos = (new MovimientoDet())->getMovimientoDet(
                    $this->CodEmpresa,
                    0,
                    0,
                    'CONCAT(movimientodet.CodDocumento, "/", movimientodet.SerieDoc, "-", movimientodet.NumeroDoc) AS documento, 
                    ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc, 
                    movimientodet.FecEmision, 
                    IF(movimientodet.CodMoneda = "MO001", "S/.", "$") AS moneda,
                    movimientodet.ValorTC, 
                    movimientodet.Saldo, 
                    movimientodet.CodCuenta, 
                    movimientodet.IdMovDet,
                    cab.IdMov, 
                    cab.Codmov, 
                    movimientodet.Mes',
                    [
                        array('tabla' => 'movimientocab cab', 'on' => 'cab.IdMov = movimientodet.IdMov AND cab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = movimientodet.IdSocioN AND socionegocio.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner')
                    ],
                    [
                        array('IdSocioN' => $IdSocioN, 'Parametro' => 'TOTAL')
                    ],
                    '(cab.Origen = "VEN" OR cab.Origen = "IMPORVEN") AND movimientodet.Saldo > 0 AND movimientodet.CampoLibre1 IS NULL',
                    ''
                );

                $tr = '';

                if (count($movimientos) > 0) {
                    foreach ($movimientos as $indice => $valor) {
                        $tr .= '
                            <tr>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['documento'] . '</a></td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['razonsocial'] . '</td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['ruc'] . '</td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['moneda'] . '</td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['ValorTC'] . '</td>
                                <td role="button" class="background-saldo font-weight-bold" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['Saldo'] . '</td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['CodCuenta'] . '</td>
                                <td><a href="javascript:void(0)" class="text-dark underline text-decoration-none" onclick="consulta_movimiento(' . $valor['IdMov'] . ')">' . $valor['Codmov'] . '</a></td>
                                <td role="button" ondblclick="seleccionar_movimiento_existente(' . $valor['IdMov'] . ')">' . $valor['Mes'] . '</td>
                            </tr>
                        ';
                    }

                    echo json_encode(array('estado' => true, 'data' => $tr));
                } else {
                    $tr = '<tr id="tr_vacio_documentos">
                        <td align="center" colspan="10">No hay datos para mostrar</td>
                        </tr>';

                    echo json_encode(array('estado' => false, 'data' => $tr));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_movimiento()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'nuevo') {
                $IdMov = intval($this->request->getPost('IdMov'));

                $movimientos = (new MovimientoDet())->getMovimientoDet(
                    $this->CodEmpresa,
                    0,
                    $IdMov,
                    '
                        pc.RelacionCuenta,
                        tvcab.DescVoucher,
                        cab.Codmov,
                        movimientodet.NumItem,
                        movimientodet.CodCuenta,
                        pc.DescCuenta,
                        mo.Abrev,
                        movimientodet.ValorTC,
                        movimientodet.DebeSol,
                        movimientodet.HaberSol,
                        movimientodet.DebeDol,
                        movimientodet.HaberDol,
                        movimientodet.FecEmision,
                        movimientodet.FecVcto,
                        sn.razonsocial,
                        do.DescDocumento,
                        movimientodet.SerieDoc,
                        movimientodet.NumeroDoc,
                        movimientodet.NumeroDocF,
                        an1.DescAnexo AS TipoOperacion,
                        cc.DesccCosto,
                        cp.desccondpago,
                        movimientodet.DocRetencion,
                        movimientodet.DocDetraccion,
                        movimientodet.Parametro,
                        movimientodet.PorcRetencion,
                        movimientodet.PorcDetraccion,
                        movimientodet.FechaDetraccion,
                        tp.DescTipoPago,
                        movimientodet.GlosaDet,
                        ts30.DescBieSer,
                        movimientodet.IdenContProy,
                        movimientodet.CodComprobanteCF,
                        movimientodet.SerieDocCF,
                        movimientodet.NumeroDocCF,
                        ts25.DescConvenio,
                        ts33.DescExonerado,
                        ts31.DescTrenta,
                        ts32.DescModalidad,
                        movimientodet.Declarar_Per,
                        an2.DescAnexo AS Declarar_Est,
                        af.descripcion AS DescActivoFijo,
                        an3.DescAnexo AS IdOperacionAF
                    ',
                    [
                        array('tabla' => 'movimientocab cab', 'on' => 'cab.IdMov = movimientodet.IdMov AND cab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = cab.CodTV AND tvcab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = movimientodet.CodCuenta AND pc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'socionegocio sn', 'on' => 'sn.IdSocioN = movimientodet.IdSocioN AND sn.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'moneda mo', 'on' => 'mo.CodMoneda = movimientodet.CodMoneda', 'tipo' => 'inner'),
                        array('tabla' => 'documento do', 'on' => 'do.CodDocumento = movimientodet.CodDocumento AND do.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'anexos an1', 'on' => 'an1.IdAnexo = movimientodet.TipoOperacion AND an1.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'centrocosto cc', 'on' => 'cc.CodcCosto = movimientodet.CodCcosto AND cc.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'condicionpago cp', 'on' => 'cp.codcondpago = movimientodet.CodCondPago AND cp.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'tipopago tp', 'on' => 'tp.CodTipoPago = movimientodet.CodTipoPago', 'tipo' => 'left'),
                        array('tabla' => 'ts30_bienesservicios ts30', 'on' => 'ts30.CodBieSer = movimientodet.CodBieSer', 'tipo' => 'left'),
                        array('tabla' => 'ts25_convenio ts25', 'on' => 'ts25.codConvenio = movimientodet.codConvenio', 'tipo' => 'left'),
                        array('tabla' => 'ts33_exonerado ts33', 'on' => 'ts33.CodExonerado = movimientodet.codExoneracion', 'tipo' => 'left'),
                        array('tabla' => 'ts31_tiporenta ts31', 'on' => 'ts31.CodTrenta = movimientodet.codTipoRenta', 'tipo' => 'left'),
                        array('tabla' => 'ts32_modalidad ts32', 'on' => 'ts32.CodModalidad = movimientodet.codModalidad', 'tipo' => 'left'),
                        array('tabla' => 'anexos an2', 'on' => 'an2.IdAnexo = movimientodet.Declarar_Est AND an2.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'activosfijos af', 'on' => 'af.IdActivo = movimientodet.IdActivo AND af.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left'),
                        array('tabla' => 'anexos an3', 'on' => 'an3.IdAnexo = movimientodet.IdOperacionAF AND an3.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'left')
                    ],
                    [],
                    '',
                    ''
                );

                $tr = '';

                if (count($movimientos) > 0) {
                    $titulo = '<b>' . $movimientos[0]['DescVoucher'] . ' (' . $movimientos[0]['Codmov'] . ') <span class="float-end">Ingreso de Ventas</span></b>';

                    $total_DebeSol = 0;
                    $total_HaberSol = 0;
                    $total_DebeDol = 0;
                    $total_HaberDol = 0;

                    foreach ($movimientos as $indice => $valor) {
                        $total_DebeSol += $valor['DebeSol'];
                        $total_HaberSol += $valor['HaberSol'];
                        $total_DebeDol += $valor['DebeDol'];
                        $total_HaberDol += $valor['HaberDol'];

                        $background = '';

                        if ($valor['RelacionCuenta'] == 1 || $valor['RelacionCuenta'] == 3) $background = 'background-total';

                        $tr .= '
                                <tr class="' . $background . '">
                                    <td>' . $valor['NumItem'] . '</a></td>
                                    <td>' . $valor['CodCuenta'] . '</td>
                                    <td>' . $valor['DescCuenta'] . '</td>
                                    <td>' . $valor['Abrev'] . '</td>
                                    <td>' . $valor['ValorTC'] . '</td>
                                    <td>' . number_format($valor['DebeSol'], 2, '.', ',') . '</td>
                                    <td>' . number_format($valor['HaberSol'], 2, '.', ',') . '</td>
                                    <td>' . number_format($valor['DebeDol'], 2, '.', ',') . '</td>
                                    <td>' . number_format($valor['HaberDol'], 2, '.', ',') . '</td>
                                    <td>' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                                    <td>' . date('d/m/Y', strtotime($valor['FecVcto'])) . '</td>
                                    <td>' . $valor['razonsocial'] . '</td>
                                    <td>' . $valor['DescDocumento'] . '</td>
                                    <td>' . $valor['SerieDoc'] . '</td>
                                    <td>' . $valor['NumeroDoc'] . '</td>
                                    <td>' . $valor['NumeroDocF'] . '</td>
                                    <td>' . $valor['TipoOperacion'] . '</td>
                                    <td>' . $valor['DesccCosto'] . '</td>
                                    <td>' . $valor['desccondpago'] . '</td>
                                    <td>' . $valor['DocRetencion'] . '</td>
                                    <td>' . $valor['DocDetraccion'] . '</td>
                                    <td>' . $valor['Parametro'] . '</td>
                                    <td>' . $valor['PorcRetencion'] . '</td>
                                    <td>' . $valor['PorcDetraccion'] . '</td>
                                    <td>' . $valor['FechaDetraccion'] . '</td>
                                    <td>' . $valor['DescTipoPago'] . '</td>
                                    <td>' . $valor['GlosaDet'] . '</td>
                                    <td>' . $valor['DescBieSer'] . '</td>
                                    <td>' . $valor['IdenContProy'] . '</td>
                                    <td>' . $valor['CodComprobanteCF'] . '</td>
                                    <td>' . $valor['SerieDocCF'] . '</td>
                                    <td>' . $valor['NumeroDocCF'] . '</td>
                                    <td>' . $valor['DescConvenio'] . '</td>
                                    <td>' . $valor['DescExonerado'] . '</td>
                                    <td>' . $valor['DescTrenta'] . '</td>
                                    <td>' . $valor['DescModalidad'] . '</td>
                                    <td>' . $valor['Declarar_Per'] . '</td>
                                    <td>' . $valor['Declarar_Est'] . '</td>
                                    <td>' . $valor['DescActivoFijo'] . '</td>
                                    <td>' . $valor['IdOperacionAF'] . '</td>
                                </tr>
                            ';
                    }

                    $total = '
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="table-responsive-md">
                                    <table class="table table-sm table-bordered" cellspacing="0">
                                        <thead>
                                            <th>Debe Soles</th>
                                            <th>Haber Soles</th>
                                            <th>Debe Dolar</th>
                                            <th>Haber Dolar</th>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>' . number_format($total_DebeSol, 2, '.', ',') . '</td>
                                                <td>' . number_format($total_HaberSol, 2, '.', ',') . '</td>
                                                <td>' . number_format($total_DebeDol, 2, '.', ',') . '</td>
                                                <td>' . number_format($total_HaberDol, 2, '.', ',') . '</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    ';

                    echo json_encode(array('estado' => true, 'titulo' => $titulo, 'data' => $tr, 'total' => $total));
                } else {
                    $tr = '<tr id="tr_vacio_movimiento">
                            <td align="center" colspan="40">No hay datos para mostrar</td>
                            </tr>';

                    echo json_encode(array('estado' => false, 'titulo' => '', 'data' => $tr, 'total' => ''));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function seleccionar_movimiento_existente()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'nuevo') {
                $IdMov = intval($this->request->getPost('IdMov'));

                $movimientos = (new MovimientoDet())->getMovimientoDet(
                    $this->CodEmpresa,
                    0,
                    $IdMov,
                    '
                        do.DescDocumento,
                        movimientodet.IdMovDet AS IdMovDetPadre,
                        movimientodet.CodMoneda,
                        movimientodet.CodDocumento,
                        movimientodet.SerieDoc,
                        movimientodet.NumeroDoc,
                        movimientodet.ValorTC,
                        movimientodet.FecEmision,
                        movimientodet.TotalS,
                        movimientodet.TotalD,
                        movimientodet.ValorTC,
                        movimientodet.CodCuenta,
                        movimientodet.Saldo,
                        cab.IdMov AS IdMovPadre,
                        cab.Codmov,
                        (SELECT det.IdMov FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.IdMov != ' . $IdMov . ') AS IdMov,
                        (SELECT det.IdMovDet FROM movimientodet det WHERE DATE(det.FecEmision) = DATE(movimientodet.FecEmision) AND det.CodDocumento = movimientodet.CodDocumento AND det.SerieDoc = movimientodet.SerieDoc AND det.NumeroDoc = movimientodet.NumeroDoc AND det.IdMov != ' . $IdMov . ') AS IdMovDet
                    ',
                    [
                        array('tabla' => 'movimientocab cab', 'on' => 'cab.IdMov = movimientodet.IdMov AND cab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'tipovouchercab tvcab', 'on' => 'tvcab.CodTV = cab.CodTV AND tvcab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                        array('tabla' => 'documento do', 'on' => 'do.CodDocumento = movimientodet.CodDocumento AND do.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner')
                    ],
                    [],
                    'movimientodet.Saldo > 0',
                    ''
                );

                $tr = '';

                if (count($movimientos) > 0) {
                    foreach ($movimientos as $indice => $valor) {
                        $valor['IdMov'] = empty($valor['IdMov']) || $valor['IdMov'] == NULL ? 0 : $valor['IdMov'];
                        $valor['IdMovDet'] = empty($valor['IdMovDet']) || $valor['IdMovDet'] == NULL ? 0 : $valor['IdMovDet'];

                        $saldo_soles = 0;
                        $saldo_dolares = 0;

                        if ($valor['CodMoneda'] == 'MO001') {
                            $saldo_soles = number_format($valor['Saldo'], 2, '.', '');
                            $saldo_dolares = number_format(($valor['Saldo'] / $valor['ValorTC']), 2, '.', '');
                        } else if ($valor['CodMoneda'] == 'MO002') {
                            $saldo_soles = number_format(($valor['Saldo'] * $valor['ValorTC']), 2, '.', '');
                            $saldo_dolares = number_format($valor['Saldo'], 2, '.', '');
                        }

                        $tr .= '
                            <tr class="tr_referencia" id="tr_referencia_existente_' . $IdMov . '">
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="hidden" name="IdMov_Referencia[]" value="' . $valor['IdMov'] . '" />
                                    <input type="hidden" name="IdMovDet_Referencia[]" value="' . $valor['IdMovDet'] . '" />
                                    <input type="hidden" name="IdMovDetPadre_Referencia[]" value="' . $valor['IdMovDetPadre'] . '" />
                                    <input type="hidden" name="CodDocumento_Referencia[]" value="' . $valor['CodDocumento'] . '" />
                                    Existente
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['DescDocumento'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" name="SerieDoc_Referencia[]" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['SerieDoc'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" name="NumeroDoc_Referencia[]" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['NumeroDoc'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="date" name="FecEmision_Referencia[]" role="button" class="form-control form-control-sm background-transparente border-none" value="' . date('Y-m-d', strtotime($valor['FecEmision'])) . '" readonly />
                                </td>
                                <td>
                                    <input type="text" name="TotalS_Referencia[]" class="referencia_TotalS form-control form-control-sm" id="TotalSReferenciaExistente' . $IdMov . '" data-value="' . $saldo_soles . '" value="' . $saldo_soles . '" oninput="cambiar_TotalS_referencia_existente(' . $IdMov . ')" onkeypress="esNumero(event)" />
                                </td>
                                <td>
                                    <input type="text" name="TotalD_Referencia[]" class="referencia_TotalD form-control form-control-sm" id="TotalDReferenciaExistente' . $IdMov . '" data-value="' . $saldo_dolares . '" value="' . $saldo_dolares . '" oninput="cambiar_TotalD_referencia_existente(' . $IdMov . ')" onkeypress="esNumero(event)" />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" name="ValorTC_Referencia[]" role="button" class="ValorTCReferenciaExistente form-control form-control-sm background-transparente border-none" id="ValorTCReferenciaExistente' . $IdMov . '" value="' . $valor['ValorTC'] . '" readonly />
                                </td>
                                <td role="button" onclick="referencia_existente(' . $IdMov . ')">
                                    <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" value="' . $valor['CodCuenta'] . '" readonly />
                                </td>
                                <td>
                                    <a href="javascript:void(0)" class="text-dark underline text-decoration-none" onclick="consulta_movimiento(' . $valor['IdMovPadre'] . ')">' . $valor['Codmov'] . '</a>
                                </td>
                            </tr>
                        ';
                    }

                    echo json_encode(array('estado' => true, 'data' => $tr));
                } else {
                    $tr = '<tr id="tr_vacio_referencia">
                            <td align="center" colspan="10">No hay datos para mostrar</td>
                            </tr>';

                    echo json_encode(array('estado' => false, 'data' => $tr));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function seleccionar_movimiento_manual()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $IdReferenciaManual = $this->request->getPost('IdReferenciaManual');
            $ValorTC = $this->request->getPost('ValorTC');

            if ($tipo == 'nuevo') {
                $tr = '
                    <tr class="tr_referencia" id="tr_referencia_manual_' . $IdReferenciaManual . '">
                        <td role="button" onclick="referencia_manual(' . $IdReferenciaManual . ')">
                            Manual
                        </td>
                        <td>
                            <select class="CodDocumentoReferenciaManual form-control form-control-sm">
                            
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" />
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm" />
                        </td>
                        <td>
                            <input type="date" class="form-control form-control-sm" />
                        </td>
                        <td>
                            <input type="text" class="referencia_TotalS form-control form-control-sm" id="TotalSReferenciaManual' . $IdReferenciaManual . '" oninput="cambiar_TotalS_referencia_manual(' . $IdReferenciaManual . ')" onkeypress="esNumero(event)" />
                        </td>
                        <td>
                            <input type="text" class="referencia_TotalD form-control form-control-sm" id="TotalDReferenciaManual' . $IdReferenciaManual . '" oninput="cambiar_TotalD_referencia_manual(' . $IdReferenciaManual . ')" onkeypress="esNumero(event)" />
                        </td>
                        <td role="button" onclick="referencia_manual(' . $IdReferenciaManual . ')">
                            <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" id="ValorTCReferenciaManual' . $IdReferenciaManual . '" value="' . $ValorTC . '" readonly />
                        </td>
                        <td role="button" onclick="referencia_manual(' . $IdReferenciaManual . ')">
                            <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" readonly />
                        </td>
                        <td>
                            <input type="text" role="button" class="form-control form-control-sm background-transparente border-none" readonly />
                        </td>
                    </tr>
                ';

                echo json_encode(array('estado' => true, 'data' => $tr));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_codigo_cuenta()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $id = $this->request->getPost('id');
            $CodCuenta = strval($this->request->getPost('CodCuenta'));
            $IdSocioN = intval($this->request->getPost('IdSocioN'));
            $TipoOperacion = intval($this->request->getPost('TipoOperacion'));
            $CodCondPago = strval($this->request->getPost('CodCondPago'));

            if ($tipo == 'nuevo') {
                $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $CodCuenta, 'RelacionCuenta', [], '', '');

                if (count($plan_contable) > 0) {
                    $RelacionCuenta = $plan_contable[0]['RelacionCuenta'];

                    if ($RelacionCuenta == 1 || $RelacionCuenta == 3) {
                        $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, $IdSocioN, 'IdSocioN, ' . (new SocioNegocio())->getRazonSocial(true) . ' AS razonsocial', [], '', '')[0];

                        $socio_negocio = '
                            <select name="IdSocioN[]" class="IdSocioN form-control form-control-sm" id="IdSocioN' . $id . '">
                                <option value="' . $socio_negocio['IdSocioN'] . '">' . $socio_negocio['razonsocial'] . '</option>
                            </select>';

                        $tipo_operacion = (new Anexo())->getAnexo($this->CodEmpresa, $TipoOperacion, 5, '', '', [], '', '')[0];

                        $tipo_operacion = '
                            <select name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm" id="TipoOperacion' . $id . '">
                                <option value="' . $tipo_operacion['IdAnexo'] . '">' . $tipo_operacion['DescAnexo'] . '</option>
                            </select>';

                        $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, $CodCondPago, '', [], '', '')[0];

                        $condicion_pago = '
                            <select name="CodCondPago[]" class="CodCondPago form-control form-control-sm" id="CodCondPago' . $id . '">
                                <option value="' . $condicion_pago['codcondpago'] . '">' . $condicion_pago['desccondpago'] . '</option>
                            </select>';

                        $documento_retencion = '<input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm" id="DocRetencion' . $id . '" onkeypress="esNumero(event)" />';

                        $ctacte = '<input type="hidden" name="CtaCte[]" class="CtaCte" id="CtaCte' . $id . '" value="1" />';
                    } else {
                        $socio_negocio = '<input type="text" name="IdSocioN[]" class="IdSocioN form-control form-control-sm background-transparente border-none" id="IdSocioN' . $id . '" readonly />';

                        $tipo_operacion = '<input type="text" name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm background-transparente border-none" id="TipoOperacion' . $id . '" readonly />';

                        $condicion_pago = '<input type="text" name="CodCondPago[]" class="CodCondPago form-control form-control-sm background-transparente border-none" id="CodCondPago' . $id . '" readonly />';

                        $documento_retencion = '<input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm background-transparente border-none" id="DocRetencion' . $id . '" readonly />';

                        $ctacte = '<input type="hidden" name="CtaCte[]" class="CtaCte" id="CtaCte' . $id . '" value="0" />';
                    }

                    if ($RelacionCuenta == 2 || $RelacionCuenta == 3) {
                        $centro_costo = '
                            <select name="CodCcosto[]" class="CodCcosto form-control form-control-sm" id="CodCcosto' . $id . '">
                                
                            </select>';
                    } else {
                        $centro_costo = '<input type="text" name="CodCcosto[]" class="CodCcosto form-control form-control-sm background-transparente border-none" id="CodCcosto' . $id . '" readonly />';
                    }

                    if ($RelacionCuenta == 4) {
                        $activo_fijo = '
                            <select name="IdActivo[]" class="IdActivo form-control form-control-sm" id="IdActivo' . $id . '">
                                
                            </select>';
                    } else {
                        $activo_fijo = '<input type="text" name="IdActivo[]" class="IdActivo form-control form-control-sm background-transparente border-none" id="IdActivo' . $id . '" readonly />';
                    }

                    echo json_encode(array('estado' => true, 'RelacionCuenta' => $RelacionCuenta, 'ctacte' => $ctacte, 'socio_negocio' => $socio_negocio, 'tipo_operacion' => $tipo_operacion, 'condicion_pago' => $condicion_pago, 'documento_retencion' => $documento_retencion, 'centro_costo' => $centro_costo, 'activo_fijo' => $activo_fijo));
                } else {
                    echo json_encode(array('estado' => false, 'mensaje' => 'No existe el Código de Cuenta en el Plan Contable'));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_codigo()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $subtipo = $this->request->getPost('subtipo');

            if ($subtipo == 'documento') {
                $CodDocumento = trim(strval($this->request->getPost('CodDocumento')));
                $Serie = trim(strval($this->request->getPost('Serie')));
                $NumeroDoc = trim(strval($this->request->getPost('NumeroDoc')));

                if ($tipo == 'nuevo') {
                    $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                        $this->CodEmpresa,
                        0,
                        0,
                        'movimientodet.IdMov, movimientodet.FecEmision',
                        [
                            array('tabla' => 'movimientocab cab', 'on' => 'cab.IdMov = movimientodet.IdMov AND cab.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner')
                        ],
                        [
                            array('CodDocumento' => $CodDocumento, 'SerieDoc' => $Serie, 'NumeroDoc' => $NumeroDoc)
                        ],
                        '',
                        ''
                    );

                    $existe_codigo = array('existe' => false);

                    if (count($movimiento_det) > 0) {
                        $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, $movimiento_det[0]['IdMov'], 'Codmov', [], [], '', '', '')[0];

                        $existe_codigo = array('existe' => true, 'periodo' => date('Y', strtotime($movimiento_det[0]['FecEmision'])), 'mes' => date('m', strtotime($movimiento_det[0]['FecEmision'])), 'movi' => $movimiento_cab['Codmov']);
                    }

                    echo json_encode($existe_codigo);
                }
            } else if ($subtipo == 'voucher') {
                $Codmov = trim(strval($this->request->getPost('Codmov')));

                $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, '', [], [array('Codmov' => $Codmov)], '', '', '');

                if (count($movimiento_cab) > 0) {
                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab($this->CodEmpresa, 0, 'MAX(SUBSTRING(Codmov, 6)) AS codigo', [], [array('Origen' => array('VEN', 'IMPORVEN'))], '', '', '');

                    $codigo_voucher_maximo = 'VEN' . date('m') . '000001';

                    if ($movimiento_cab[0]['codigo']) {
                        $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                        if (strlen($movimiento_cab[0]['codigo']) == 1) {
                            $codigo_voucher_maximo = 'VEN' . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                            $codigo_voucher_maximo = 'VEN' . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                            $codigo_voucher_maximo = 'VEN' . date('m') . '000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                            $codigo_voucher_maximo = 'VEN' . date('m') . '00' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                            $codigo_voucher_maximo = 'VEN' . date('m') . '0' . $movimiento_cab[0]['codigo'];
                        } else {
                            $codigo_voucher_maximo = 'VEN' . date('m') . $movimiento_cab[0]['codigo'];
                        }
                    }

                    echo json_encode(array('estado' => true, 'codigo' => $codigo_voucher_maximo));
                } else {
                    echo json_encode(array('estado' => false));
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_tipo_vouchers()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            $CodTV = strtoupper(trim(strval($this->request->getPost('CodTV'))));

            if ($tipo == 'nuevo') {
                $CodMoneda = strtoupper(trim(strval($this->request->getPost('CodMoneda'))));
                $ValorTC = trim(strval($this->request->getPost('ValorTC')));
                $FecEmision = trim(strval($this->request->getPost('FecEmision')));
                $FecVcto = trim(strval($this->request->getPost('FecVcto')));
                $IdSocioN = trim(strval($this->request->getPost('IdSocioN')));
                $CodDocumento = trim(strval($this->request->getPost('CodDocumento')));
                $SerieDoc = trim(strval($this->request->getPost('Serie')));
                $NumeroDoc = trim(strval($this->request->getPost('NumeroDoc')));
                $NumeroDocF = trim(strval($this->request->getPost('NumeroDocF')));
                $TipoOperacion = trim(strval($this->request->getPost('TipoOperacion')));
                $CodCondPago = trim(strval($this->request->getPost('CodCondPago')));

                $Afecto = trim(strval($this->request->getPost('Afecto')));
                $Anticipo = trim(strval($this->request->getPost('Anticipo')));
                $Descuento = trim(strval($this->request->getPost('Descuento')));
                $Igv = trim(strval($this->request->getPost('Igv')));
                $Percepcion = trim(strval($this->request->getPost('Percepcion')));
                $ISC = trim(strval($this->request->getPost('ISC')));
                $Inafecto = trim(strval($this->request->getPost('Inafecto')));
                $Exonerado = trim(strval($this->request->getPost('Exonerado')));
                $Otro_tributo = trim(strval($this->request->getPost('Otro_tributo')));
                $ICBP = trim(strval($this->request->getPost('ICBP')));
                $Total = trim(strval($this->request->getPost('Total')));

                $Total = $Total == 0 ? number_format($Total, 2, '.', '') : $Total;

                $CodSunat = (new Documento())->getDocumento($this->CodEmpresa, $CodDocumento, '', 'CodSunat', [], '', '')[0]['CodSunat'];

                $tipo_voucher_detalles = (new TipoVoucherDet())->getTipoVoucherDet(
                    $this->CodEmpresa,
                    '',
                    '',
                    $CodTV,
                    '
                        tipovoucherdet.Parametro,
                        tipovoucherdet.Debe_Haber,
                        pc.CodCuenta,
                        pc.DescCuenta,
                        pc.RelacionCuenta
                    ',
                    [
                        array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = tipovoucherdet.CodCuenta AND pc.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    ''
                );

                $tr = '';

                $cantidad_Total = 1;
                $cantidad_Afecto = 1;
                $cantidad_Anticipo = 1;
                $cantidad_Descuento = 1;
                $cantidad_Igv = 1;
                $cantidad_Percepcion = 1;
                $cantidad_ISC = 1;
                $cantidad_Inafecto = 1;
                $cantidad_Exonerado = 1;
                $cantidad_Otro_tributo = 1;
                $cantidad_Icbp = 1;

                foreach ($tipo_voucher_detalles as $indice => $valor) {
                    $socio_negocio = '<input type="text" name="IdSocioN[]" class="IdSocioN form-control form-control-sm background-transparente border-none" id="IdSocioN' . ($indice + 1) . '" readonly />';

                    $tipo_operacion = '<input type="text" name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm background-transparente border-none" id="TipoOperacion' . ($indice + 1) . '" readonly />';

                    $centro_costo = '<input type="text" name="CodCcosto[]" class="CodCcosto form-control form-control-sm background-transparente border-none" id="CodCcosto' . ($indice + 1) . '" readonly />';

                    $condicion_pago = '<input type="text" name="CodCondPago[]" class="CodCondPago form-control form-control-sm background-transparente border-none" id="CodCondPago' . ($indice + 1) . '" readonly />';

                    $documento_retencion = '<input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm background-transparente border-none" id="DocRetencion' . ($indice + 1) . '" readonly />';

                    $activo_fijo = '<input type="text" name="IdActivo[]" class="IdActivo form-control form-control-sm background-transparente border-none" id="IdActivo' . ($indice + 1) . '" readonly />';

                    $background_total = '';

                    $readonly_numero_final = '';

                    if ($CodSunat == '01') $readonly_numero_final = 'readonly';

                    $CtaCte = 0;

                    if ($valor['RelacionCuenta'] == 1 || $valor['RelacionCuenta'] == 3) {
                        $background_total = 'background-total';

                        $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, $IdSocioN, 'IdSocioN, ' . (new SocioNegocio())->getRazonSocial(true) . ' AS razonsocial', [], '', '')[0];

                        $socio_negocio = '
                            <select name="IdSocioN[]" class="IdSocioN form-control form-control-sm" id="IdSocioN' . ($indice + 1) . '">
                                <option value="' . $socio_negocio['IdSocioN'] . '">' . $socio_negocio['razonsocial'] . '</option>
                            </select>';

                        $tipo_operacion = (new Anexo())->getAnexo($this->CodEmpresa, $TipoOperacion, 5, '', '', [], '', '')[0];

                        $tipo_operacion = '
                            <select name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm" id="TipoOperacion' . ($indice + 1) . '">
                                <option value="' . $tipo_operacion['IdAnexo'] . '">' . $tipo_operacion['DescAnexo'] . '</option>
                            </select>';

                        $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, $CodCondPago, '', [], '', '')[0];

                        $condicion_pago = '
                            <select name="CodCondPago[]" class="CodCondPago form-control form-control-sm" id="CodCondPago' . ($indice + 1) . '">
                                <option value="' . $condicion_pago['codcondpago'] . '">' . $condicion_pago['desccondpago'] . '</option>
                            </select>';

                        $documento_retencion = '<input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm" id="DocRetencion' . ($indice + 1) . '" onkeypress="esNumero(event)" />';

                        $CtaCte = 1;
                    }

                    if ($valor['RelacionCuenta'] == 2 || $valor['RelacionCuenta'] == 3) {
                        $centro_costo = '
                            <select name="CodCcosto[]" class="CodCcosto form-control form-control-sm" id="CodCcosto' . ($indice + 1) . '">
                                
                            </select>';
                    }

                    if ($valor['RelacionCuenta'] == 4) {
                        $activo_fijo = '
                            <select name="IdActivo[]" class="IdActivo form-control form-control-sm" id="IdActivo' . ($indice + 1) . '">
                                
                            </select>';
                    }

                    $DebeSol = 0;
                    $HaberSol = 0;
                    $DebeDol = 0;
                    $HaberDol = 0;

                    switch ($valor['Parametro']) {
                        case 'TOTAL':
                            if ($cantidad_Total == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Total;
                                        $DebeDol = $Total / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Total;
                                        $DebeSol = $Total * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Total;
                                        $HaberDol = $Total / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Total;
                                        $HaberSol = $Total * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Total++;

                            break;
                        case 'AFECTO':
                            if ($cantidad_Afecto == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Afecto;
                                        $DebeDol = $Afecto / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Afecto;
                                        $DebeSol = $Afecto * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Afecto;
                                        $HaberDol = $Afecto / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Afecto;
                                        $HaberSol = $Afecto * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Afecto++;

                            break;
                        case 'ANTICIPO':
                            if ($cantidad_Anticipo == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Anticipo;
                                        $DebeDol = $Anticipo / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Anticipo;
                                        $DebeSol = $Anticipo * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Anticipo;
                                        $HaberDol = $Anticipo / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Anticipo;
                                        $HaberSol = $Anticipo * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Anticipo++;

                            break;
                        case 'DESCUENTO':
                            if ($cantidad_Descuento == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Descuento;
                                        $DebeDol = $Descuento / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Descuento;
                                        $DebeSol = $Descuento * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Descuento;
                                        $HaberDol = $Descuento / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Descuento;
                                        $HaberSol = $Descuento * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Descuento++;

                            break;
                        case 'IGV':
                            if ($cantidad_Igv == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Igv;
                                        $DebeDol = $Igv / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Igv;
                                        $DebeSol = $Igv * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Igv;
                                        $HaberDol = $Igv / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Igv;
                                        $HaberSol = $Igv * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Igv++;

                            break;
                        case 'PERCEPCION':
                            if ($cantidad_Percepcion == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Percepcion;
                                        $DebeDol = $Percepcion / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Percepcion;
                                        $DebeSol = $Percepcion * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Percepcion;
                                        $HaberDol = $Percepcion / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Percepcion;
                                        $HaberSol = $Percepcion * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Percepcion++;

                            break;
                        case 'ISC':
                            if ($cantidad_ISC == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $ISC;
                                        $DebeDol = $ISC / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $ISC;
                                        $DebeSol = $ISC * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $ISC;
                                        $HaberDol = $ISC / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $ISC;
                                        $HaberSol = $ISC * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_ISC++;

                            break;
                        case 'INAFECTO':
                            if ($cantidad_Inafecto == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Inafecto;
                                        $DebeDol = $Inafecto / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Inafecto;
                                        $DebeSol = $Inafecto * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Inafecto;
                                        $HaberDol = $Inafecto / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Inafecto;
                                        $HaberSol = $Inafecto * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Inafecto++;

                            break;
                        case 'EXONERADO':
                            if ($cantidad_Exonerado == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Exonerado;
                                        $DebeDol = $Exonerado / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Exonerado;
                                        $DebeSol = $Exonerado * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Exonerado;
                                        $HaberDol = $Exonerado / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Exonerado;
                                        $HaberSol = $Exonerado * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Exonerado++;

                            break;
                        case 'OTRO TRIBUTO':
                            if ($cantidad_Otro_tributo == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $Otro_tributo;
                                        $DebeDol = $Otro_tributo / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $Otro_tributo;
                                        $DebeSol = $Otro_tributo * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $Otro_tributo;
                                        $HaberDol = $Otro_tributo / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $Otro_tributo;
                                        $HaberSol = $Otro_tributo * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Otro_tributo++;

                            break;
                        case 'ICBP':
                            if ($cantidad_Icbp == 1) {
                                if ($valor['Debe_Haber'] == 'D') {
                                    if ($CodMoneda == 'MO001') {
                                        $DebeSol = $ICBP;
                                        $DebeDol = $ICBP / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $DebeDol = $ICBP;
                                        $DebeSol = $ICBP * $ValorTC;
                                    }
                                } else if ($valor['Debe_Haber'] == 'H') {
                                    if ($CodMoneda == 'MO001') {
                                        $HaberSol = $ICBP;
                                        $HaberDol = $ICBP / $ValorTC;
                                    } else if ($CodMoneda == 'MO002') {
                                        $HaberDol = $ICBP;
                                        $HaberSol = $ICBP * $ValorTC;
                                    }
                                }
                            }

                            $cantidad_Icbp++;

                            break;
                    }

                    $DebeSol_auxiliar = number_format($DebeSol, 2, '.', '');
                    $HaberSol_auxiliar = number_format($HaberSol, 2, '.', '');
                    $DebeDol_auxiliar = number_format($DebeDol, 2, '.', '');
                    $HaberDol_auxiliar = number_format($HaberDol, 2, '.', '');

                    if ($CodSunat == '07') {
                        $DebeSol = $HaberSol_auxiliar;
                        $HaberSol = $DebeSol_auxiliar;
                        $DebeDol = $HaberDol_auxiliar;
                        $HaberDol = $DebeDol_auxiliar;
                    } else {
                        $DebeSol = $DebeSol_auxiliar;
                        $HaberSol = $HaberSol_auxiliar;
                        $DebeDol = $DebeDol_auxiliar;
                        $HaberDol = $HaberDol_auxiliar;
                    }

                    $moneda = (new Moneda())->getMoneda($CodMoneda, '', [], '', '')[0];

                    $documento = (new Documento())->getDocumento(
                        $this->CodEmpresa,
                        $CodDocumento,
                        'VE',
                        '',
                        [
                            array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left')
                        ],
                        '',
                        'documento.DescDocumento ASC'
                    )[0];

                    $tipo_dato = explode('|', $documento['TipoDatoS']);
                    $longitud = isset($tipo_dato[2]) ? $tipo_dato[2] : '';
                    $serie = isset($tipo_dato[3]) ? $tipo_dato[3] : '';
                    $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                    $parametro = (new Parametro())->getParametro($valor['Parametro'])[0];

                    $tr .= '<tr id="tr_' . ($indice + 1) . '" class="clase_ingreso_ventas ' . $background_total . '">
                    <td class="vertical-align-middle text-center ' . $background_total . '">
                        <input type="radio" name="Seleccionar" class="Seleccionar" id="Seleccionar' . ($indice + 1) . '">
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="hidden" name="IdMovDet[]" value="0" />
                        <input type="text" name="NumItem[]" class="NumItem form-control form-control-sm background-transparente border-none" id="NumItem' . ($indice + 1) . '" value="' . ($indice + 1) . '" readonly />
                    </td>
                    <td id="td_ctacte_' . ($indice + 1) . '" class="td_ctacte display-none">
                        <input type="hidden" name="CtaCte[]" class="CtaCte" id="CtaCte' . ($indice + 1) . '" value="' . $CtaCte . '" />
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="CodCuenta[]" class="CodCuenta form-control form-control-sm" id="CodCuenta' . ($indice + 1) . '" onchange="cambiar_cuenta(' . ($indice + 1) . ')">
                            <option value="' . $valor['CodCuenta'] . '">' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="CodMoneda[]" class="CodMoneda form-control form-control-sm" id="CodMoneda' . ($indice + 1) . '" onchange="cambiar_moneda(' . ($indice + 1) . ')">
                            <option value="' . $moneda['CodMoneda'] . '">' . $moneda['Abrev'] . '</option>
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="ValorTC[]" class="ValorTC form-control form-control-sm background-transparente border-none" id="ValorTC' . ($indice + 1) . '" value="' . $ValorTC . '" oninput="cambiar_tipo_cambio_from_table(' . ($indice + 1) . ')" onkeypress="esNumero(event)" readonly />
                    </td>
                    <td class="background-soles">
                        <input type="text" name="DebeSol[]" class="DebeSol form-control form-control-sm" id="DebeSol' . ($indice + 1) . '" value="' . $DebeSol . '" oninput="cambiar_debe_soles(' . ($indice + 1) . ')" onkeydown="cambiar_debe_soles_keydown(event, ' . ($indice + 1) . ')" onkeypress="esNumero(event);" />
                    </td>
                    <td class="background-soles">
                        <input type="text" name="HaberSol[]" class="HaberSol form-control form-control-sm" id="HaberSol' . ($indice + 1) . '" value="' . $HaberSol . '" oninput="cambiar_haber_soles(' . ($indice + 1) . ')" onkeydown="cambiar_haber_soles_keydown(event, ' . ($indice + 1) . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class="background-dolar">
                        <input type="text" name="DebeDol[]" class="DebeDol form-control form-control-sm" id="DebeDol' . ($indice + 1) . '" value="' . $DebeDol . '" oninput="cambiar_debe_dolar(' . ($indice + 1) . ')" onkeydown="cambiar_debe_dolar_keydown(event, ' . ($indice + 1) . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class="background-dolar">
                        <input type="text" name="HaberDol[]" class="HaberDol form-control form-control-sm" id="HaberDol' . ($indice + 1) . '" value="' . $HaberDol . '" oninput="cambiar_haber_dolar(' . ($indice + 1) . ')" onkeydown="cambiar_haber_dolar_keydown(event, ' . ($indice + 1) . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class=" ' . $background_total . '">
                        <div class="input-group input-group-sm input-group-vc">
                            <input type="text" name="FecEmision[]" class="FecEmision form-control mydatepicker" placeholder="dd/mm/yyyy" id="FecEmision' . ($indice + 1) . '" data-value="' . $FecEmision . '" value="' . $FecEmision . '" onchange="cambiar_fecha_emision(' . ($indice + 1) . ')">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>
                    <td class=" ' . $background_total . '">
                        <div class="input-group input-group-sm input-group-vc">
                            <input type="text" name="FecVcto[]" class="FecVcto form-control mydatepicker" placeholder="dd/mm/yyyy" id="FecVcto' . ($indice + 1) . '" data-value="' . $FecVcto . '" value="' . $FecVcto . '" onchange="cambiar_fecha_vencimiento(' . ($indice + 1) . ')">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>
                    <td id="td_socio_negocio_' . ($indice + 1) . '" class="td_socio_negocio ' . $background_total . '">
                        ' . $socio_negocio . '
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="CodDocumento[]" class="CodDocumento form-control form-control-sm" id="CodDocumento' . ($indice + 1) . '" onchange="cambiar_comprobante(' . ($indice + 1) . ')">
                            <option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $documento['CodDocumento'] . '">' . $documento['CodDocumento'] . ' - ' . $documento['DescDocumento'] . '</option>
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="SerieDoc[]" class="SerieDoc form-control form-control-sm" id="SerieDoc' . ($indice + 1) . '" value="' . $SerieDoc . '" oninput="verificar_serie_from_table(' . ($indice + 1) . ')" onfocusout="cambiar_serie_from_table(' . ($indice + 1) . ')" />
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="NumeroDoc[]" class="NumeroDoc form-control form-control-sm" id="NumeroDoc' . ($indice + 1) . '" value="' . $NumeroDoc . '" oninput="cambiar_numero_inicial(' . ($indice + 1) . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="NumeroDocF[]" class="NumeroDocF form-control form-control-sm" id="NumeroDocF' . ($indice + 1) . '" value="' . $NumeroDocF . '" onkeypress="esNumero(event)" ' . $readonly_numero_final . ' />
                    </td>
                    <td id="td_tipo_operacion_' . ($indice + 1) . '" class="td_tipo_operacion ' . $background_total . '">
                        ' . $tipo_operacion . '
                    </td>
                    <td id="td_centro_costo_' . ($indice + 1) . '" class="td_centro_costo ' . $background_total . '">
                        ' . $centro_costo . '
                    </td>
                    <td id="td_condicion_pago_' . ($indice + 1) . '" class="td_condicion_pago ' . $background_total . '">
                        ' . $condicion_pago . '
                    </td>
                    <td id="td_documento_retencion_' . ($indice + 1) . '" class="td_documento_retencion ' . $background_total . '">
                        ' . $documento_retencion . '
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="DocDetraccion[]" class="DocDetraccion form-control form-control-sm" id="DocDetraccion' . ($indice + 1) . '" onkeypress="esNumero(event)" />
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="Parametro[]" class="Parametro form-control form-control-sm" id="Parametro' . ($indice + 1) . '" onchange="cambiar_parametro(' . ($indice + 1) . ')">
                            <option value="' . $parametro['id'] . '">' . $parametro['text'] . '</option>
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="PorcRetencion[]" class="PorcRetencion form-control form-control-sm background-transparente border-none" id="PorcRetencion' . ($indice + 1) . '" value="0" readonly />
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="IdDetraccion[]" class="IdDetraccion form-control form-control-sm" id="IdDetraccion' . ($indice + 1) . '">
                            
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <div class="input-group input-group-sm input-group-vc">
                            <input type="text" name="FechaDetraccion[]" class="FechaDetraccion form-control background-transparente border-none mydatepicker" placeholder="dd/mm/yyyy" id="FechaDetraccion' . ($indice + 1) . '" readonly">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="IdTipOpeDetra[]" class="IdTipOpeDetra form-control form-control-sm" id="IdTipOpeDetra' . ($indice + 1) . '">
                            
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <input type="text" name="IdenContProy[]" class="IdenContProy form-control form-control-sm" id="IdenContProy' . ($indice + 1) . '" />
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="Declarar_Per[]" class="Declarar_Per form-control form-control-sm" id="Declarar_Per' . ($indice + 1) . '">
                            
                        </select>
                    </td>
                    <td class=" ' . $background_total . '">
                        <select name="Declarar_Est[]" class="Declarar_Est form-control form-control-sm" id="Declarar_Est' . ($indice + 1) . '">
                            
                        </select>
                    </td>
                    <td id="td_activo_fijo_' . ($indice + 1) . '" class="td_activo_fijo ' . $background_total . '">
                        ' . $activo_fijo . '
                    </td>
                    <td align="center">
                        <button type="button" class="Eliminar btn btn-sm btn-danger" id="Eliminar' . ($indice + 1) . '" onclick="eliminar_fila(' . ($indice + 1) . ')">Eliminar</button>
                    </td>
                </tr>
                ';
                }

                $tr .= '
                <tr class="clase_ingreso_ventas">
                    <td class="vertical-align-middle text-center">
                        <input type="radio" name="Seleccionar" class="Seleccionar" id="SeleccionarUltimo" checked>
                    </td>
                </tr>';

                echo $tr;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function agregar_mas_detalle_fila()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'nuevo') {
                $NumItem = trim(strval($this->request->getPost('NumItem')));
                $CodMoneda = trim(strval($this->request->getPost('CodMoneda')));
                $ValorTC = trim(strval($this->request->getPost('ValorTC')));
                $FecEmision = str_replace('/', '-', trim(strval($this->request->getPost('FecEmision'))));
                $FecVcto = str_replace('/', '-', trim(strval($this->request->getPost('FecVcto'))));
                $SerieDoc = trim(strval($this->request->getPost('Serie')));
                $NumeroDoc = trim(strval($this->request->getPost('NumeroDoc')));
                $NumeroDocF = trim(strval($this->request->getPost('NumeroDocF')));
                $Parametro = trim(strval($this->request->getPost('Parametro')));
                $CodDocumento = trim(strval($this->request->getPost('CodDocumento')));

                $CodSunat = (new Documento())->getDocumento($this->CodEmpresa, $CodDocumento, '', 'CodSunat', [], '', '')[0]['CodSunat'];

                $readonly_numero_final = '';

                if ($CodSunat == '01') $readonly_numero_final = 'readonly';

                $DebeSol = number_format(0, 2, '.', '');
                $HaberSol = number_format(0, 2, '.', '');
                $DebeDol = number_format(0, 2, '.', '');
                $HaberDol = number_format(0, 2, '.', '');

                $moneda = (new Moneda())->getMoneda($CodMoneda, '', [], '', '')[0];

                $documento = (new Documento())->getDocumento(
                    $this->CodEmpresa,
                    $CodDocumento,
                    'VE',
                    '',
                    [
                        array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left')
                    ],
                    '',
                    'documento.DescDocumento ASC'
                )[0];

                $tipo_dato = explode('|', $documento['TipoDatoS']);
                $longitud = isset($tipo_dato[2]) ? $tipo_dato[2] : '';
                $serie = isset($tipo_dato[3]) ? $tipo_dato[3] : '';
                $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                $parametro = (new Parametro())->getParametro($Parametro)[0];

                $tr = '<tr id="tr_' . $NumItem . '" class="clase_ingreso_ventas">
                    <td class="vertical-align-middle text-center">
                        <input type="radio" name="Seleccionar" class="Seleccionar" id="Seleccionar' . $NumItem . '">
                    </td>
                    <td>
                        <input type="hidden" name="IdMovDet[]" value="0" />
                        <input type="text" name="NumItem[]" class="NumItem form-control form-control-sm background-transparente border-none" id="NumItem' . $NumItem . '" value="' . $NumItem . '" readonly />
                    </td>
                    <td id="td_ctacte_' . $NumItem . '" class="td_ctacte display-none">
                        <input type="hidden" name="CtaCte[]" class="CtaCte" id="CtaCte' . $NumItem . '" value="0" />
                    </td>
                    <td>
                        <select name="CodCuenta[]" class="CodCuenta form-control form-control-sm" id="CodCuenta' . $NumItem . '" onchange="cambiar_cuenta(' . $NumItem . ')">

                        </select>
                    </td>
                    <td>
                        <select name="CodMoneda[]" class="CodMoneda form-control form-control-sm" id="CodMoneda' . $NumItem . '" onchange="cambiar_moneda(' . $NumItem . ')">
                            <option value="' . $moneda['CodMoneda'] . '">' . $moneda['Abrev'] . '</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="ValorTC[]" class="ValorTC form-control form-control-sm background-transparente border-none" id="ValorTC' . $NumItem . '" value="' . $ValorTC . '" oninput="cambiar_tipo_cambio_from_table(' . $NumItem . ')" onkeypress="esNumero(event)" readonly />
                    </td>
                    <td class="background-soles">
                        <input type="text" name="DebeSol[]" class="DebeSol form-control form-control-sm" id="DebeSol' . $NumItem . '" value="' . $DebeSol . '" oninput="cambiar_debe_soles(' . $NumItem . ')" onkeydown="cambiar_debe_soles_keydown(event, ' . $NumItem . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class="background-soles">
                        <input type="text" name="HaberSol[]" class="HaberSol form-control form-control-sm" id="HaberSol' . $NumItem . '" value="' . $HaberSol . '" oninput="cambiar_haber_soles(' . $NumItem . ')" onkeydown="cambiar_haber_soles_keydown(event, ' . $NumItem . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class="background-dolar">
                        <input type="text" name="DebeDol[]" class="DebeDol form-control form-control-sm" id="DebeDol' . $NumItem . '" value="' . $DebeDol . '" oninput="cambiar_debe_dolar(' . $NumItem . ')" onkeydown="cambiar_debe_dolar_keydown(event, ' . $NumItem . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td class="background-dolar">
                        <input type="text" name="HaberDol[]" class="HaberDol form-control form-control-sm" id="HaberDol' . $NumItem . '" value="' . $HaberDol . '" oninput="cambiar_haber_dolar(' . $NumItem . ')" onkeydown="cambiar_haber_dolar_keydown(event, ' . $NumItem . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td>
                        <div class="input-group input-group-sm input-group-vc">
                            <input type="text" name="FecEmision[]" class="FecEmision form-control mydatepicker" placeholder="dd/mm/yyyy" id="FecEmision' . $NumItem . '" data-value="' . date('d/m/Y', strtotime($FecEmision)) . '" value="' . date('d/m/Y', strtotime($FecEmision)) . '" onchange="cambiar_fecha_emision(' . $NumItem . ')">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group input-group-sm input-group-vc">
                            <input type="text" name="FecVcto[]" class="FecVcto form-control mydatepicker" placeholder="dd/mm/yyyy" id="FecVcto' . $NumItem . '" data-value="' . date('d/m/Y', strtotime($FecVcto)) . '" value="' . date('d/m/Y', strtotime($FecVcto)) . '" onchange="cambiar_fecha_vencimiento(' . $NumItem . ')">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>
                    <td id="td_socio_negocio_' . $NumItem . '" class="td_socio_negocio">
                        <input type="text" name="IdSocioN[]" class="IdSocioN form-control form-control-sm background-transparente border-none" id="IdSocioN' . $NumItem . '" readonly />
                    </td>
                    <td>
                        <select name="CodDocumento[]" class="CodDocumento form-control form-control-sm" id="CodDocumento' . $NumItem . '" onchange="cambiar_comprobante(' . $NumItem . ')">
                            <option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $documento['CodDocumento'] . '">' . $documento['CodDocumento'] . ' - ' . $documento['DescDocumento'] . '</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="SerieDoc[]" class="SerieDoc form-control form-control-sm" id="SerieDoc' . $NumItem . '" value="' . $SerieDoc . '" oninput="verificar_serie_from_table(' . $NumItem . ')" onfocusout="cambiar_serie_from_table(' . $NumItem . ')" />
                    </td>
                    <td>
                        <input type="text" name="NumeroDoc[]" class="NumeroDoc form-control form-control-sm" id="NumeroDoc' . $NumItem . '" value="' . $NumeroDoc . '" oninput="cambiar_numero_inicial(' . $NumItem . ')" onkeypress="esNumero(event)" />
                    </td>
                    <td>
                        <input type="text" name="NumeroDocF[]" class="NumeroDocF form-control form-control-sm" id="NumeroDocF' . $NumItem . '" value="' . $NumeroDocF . '" onkeypress="esNumero(event)" ' . $readonly_numero_final . ' />
                    </td>
                    <td id="td_tipo_operacion_' . $NumItem . '" class="td_tipo_operacion">
                        <input type="text" name="TipoOperacion[]" class="TipoOperacion form-control form-control-sm background-transparente border-none" id="TipoOperacion' . $NumItem . '" readonly />
                    </td>
                    <td id="td_centro_costo_' . $NumItem . '" class="td_centro_costo">
                        <input type="text" name="CodCcosto[]" class="CodCcosto form-control form-control-sm background-transparente border-none" id="CodCcosto' . $NumItem . '" readonly />
                    </td>
                    <td id="td_condicion_pago_' . $NumItem . '" class="td_condicion_pago">
                        <input type="text" name="CodCondPago[]" class="CodCondPago form-control form-control-sm background-transparente border-none" id="CodCondPago' . $NumItem . '" readonly />
                    </td>
                    <td id="td_documento_retencion_' . $NumItem . '" class="td_documento_retencion">
                        <input type="text" name="DocRetencion[]" class="DocRetencion form-control form-control-sm background-transparente border-none" id="DocRetencion' . $NumItem . '" readonly />
                    </td>
                    <td>
                        <input type="text" name="DocDetraccion[]" class="DocDetraccion form-control form-control-sm background-transparente border-none" id="DocDetraccion' . $NumItem . '" readonly />
                    </td>
                    <td>
                        <select name="Parametro[]" class="Parametro form-control form-control-sm" id="Parametro' . $NumItem . '" onchange="cambiar_parametro(' . $NumItem . ')">
                            <option value="' . $parametro['id'] . '">' . $parametro['text'] . '</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="PorcRetencion[]" class="PorcRetencion form-control form-control-sm background-transparente border-none" id="PorcRetencion' . $NumItem . '" readonly />
                    </td>
                    <td>
                        <select name="IdDetraccion[]" class="IdDetraccion form-control form-control-sm" id="IdDetraccion' . $NumItem . '">
                            <option value="">Seleccione</option>
                        </select>
                    </td>
                    <td>
                        <div class="input-group input-group-sm input-group-vc">
                            <input type="text" name="FechaDetraccion[]" class="FechaDetraccion form-control background-transparente border-none mydatepicker" placeholder="dd/mm/yyyy" id="FechaDetraccion' . $NumItem . '" readonly">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </td>
                    <td>
                        <select name="IdTipOpeDetra[]" class="IdTipOpeDetra form-control form-control-sm" id="IdTipOpeDetra' . $NumItem . '">
                            <option value="">Seleccione</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="IdenContProy[]" class="IdenContProy form-control form-control-sm" id="IdenContProy' . $NumItem . '" />
                    </td>
                    <td>
                        <select name="Declarar_Per[]" class="Declarar_Per form-control form-control-sm" id="Declarar_Per' . $NumItem . '">
                            <option value="">Seleccione</option>
                        </select>
                    </td>
                    <td>
                        <select name="Declarar_Est[]" class="Declarar_Est form-control form-control-sm" id="Declarar_Est' . $NumItem . '">
                            <option value="">Seleccione</option>
                        </select>
                    </td>
                    <td id="td_activo_fijo_' . $NumItem . '" class="td_activo_fijo">
                        <input type="text" name="IdActivo[]" class="IdActivo form-control form-control-sm background-transparente border-none" id="IdActivo' . $NumItem . '" readonly />
                    </td>
                    <td align="center">
                        <button type="button" class="Eliminar btn btn-sm btn-danger" id="Eliminar' . $NumItem . '" onclick="eliminar_fila(' . $NumItem . ')">Eliminar</button>
                    </td>
                </tr>
                ';

                echo $tr;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function datos_movimiento_det()
    {
        try {
            $data = [
                'CodEmpresa' => $this->CodEmpresa,
                'IdMov' => NULL,
                'Periodo' => NULL,
                'Mes' => NULL,
                'ValorTC' => 0,
                'DebeSol' => 0,
                'HaberSol' => 0,
                'DebeDol' => 0,
                'HaberDol' => 0,
                'CodMoneda' => NULL,
                'FecEmision' => NULL,
                'FecVcto' => NULL,
                'IdSocioN' => NULL,
                'CodDocumento' => NULL,
                'SerieDoc' => NULL,
                'NumeroDoc' => NULL,
                'NumeroDocF' => NULL,
                'FecEmisionRef' => NULL,
                'CodDocumentoRef' => NULL,
                'SerieDocRef' => NULL,
                'NumeroDocRef' => NULL,
                'Destino' => NULL,
                'RegistroSunat' => NULL,
                'TipoOperacion' => NULL,
                'BaseImpSunatS' => 0,
                'InafectoS' => 0,
                'ExoneradoS' => 0,
                'ISCS' => 0,
                'IGVSunatS' => 0,
                'PercepcionS' => 0,
                'OtroTributoS' => 0,
                'Retencion4S' => 0,
                'TotalS' => 0,
                'DescuentoS' => 0,
                'AnticipoS' => 0,
                'IcbpS' => 0,
                'BaseImpSunatD' => 0,
                'InafectoD' => 0,
                'ExoneradoD' => 0,
                'ISCD' => 0,
                'IGVSunatD' => 0,
                'PercepcionD' => 0,
                'OtroTributoD' => 0,
                'Retencion4D' => 0,
                'TotalD' => 0,
                'DescuentoD' => 0,
                'AnticipoD' => 0,
                'IcbpD' => 0,
                'CodCcosto' => NULL,
                'CodCondPago' => NULL,
                'DocRetencion' => NULL,
                'DocDetraccion' => NULL,
                'PorcRetencion' => 0,
                'PorcDetraccion' => 0,
                'FechaDetraccion' => NULL,
                'CodTipoPago' => NULL,
                'Parametro' => NULL,
                'GlosaDet' => '',
                'Monto' => 0,
                'Saldo' => 0,
                'CtaCte' => 0,
                'CodCuentaLibre' => NULL,
                'CampoLibre1' => NULL,
                'codCuentaDestino' => NULL,
                'IdTipOpeDetra' => NULL,
                'CodTipoSN' => NULL,
                'TCcierre' => NULL,
                'TipoPC' => NULL,
                'NumCheque' => NULL,
                'IdenContProy' => NULL,
                'Importado' => NULL,
                'CodTipoCliente' => NULL,
                'Declarar_Per' => NULL,
                'Declarar_Est' => NULL,
                'IdActivo' => NULL,
                'Validacion' => 0,
                'FlagInterno' => 0
            ];

            return $data;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function historial_importar()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $subtipo = $this->request->getPost('subtipo');

            if ($tipo == 'nuevo') {
                if ($subtipo == 'consulta') {
                    $historicoImp = (new HistoricoImp())->getHistoricoImp($this->CodEmpresa, 0, 'idHistImp, Fecha', [], '', '');

                    $resultado = array('estado' => false);

                    if (count($historicoImp) > 0) {
                        $tr = '';

                        foreach ($historicoImp as $indice => $valor) {
                            $tr .= '<tr id="tr_historial_' . $valor['idHistImp'] . '" class="tr_historial">';
                            $tr .= '<td role="button" onclick="set_historial(' . $valor['idHistImp'] . ')">' . $valor['Fecha'] . '</td>';
                            $tr .= '</tr>';
                        }

                        $resultado = array('estado' => true, 'tr' => $tr);
                    }

                    echo json_encode($resultado);
                } else if ($subtipo == 'eliminar') {
                    $id = $this->request->getPost('id');

                    $this->db->disableForeignKeyChecks();

                    $this->db->transBegin();

                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                        $this->CodEmpresa,
                        0,
                        'IdMov, IdMovAplica',
                        [],
                        [],
                        'IdMovAplica IS NOT NULL AND LENGTH(IdMovAplica) > 0 AND Importado = ' . $id,
                        '',
                        ''
                    );
        
                    if (count($movimiento_cab) > 0) {
                        $IdMov = $movimiento_cab[0]['IdMov'];
                        $IdMovSaldoDet = $movimiento_cab[0]['IdMovAplica'];
        
                        $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                            $this->CodEmpresa,
                            0,
                            $IdMovSaldoDet,
                            'Monto, CampoLibre1',
                            [],
                            [],
                            'LENGTH(CampoLibre1) != 0',
                            ''
                        );
        
                        if (count($movimiento_det) > 0) {
                            $Monto = $movimiento_det[0]['Monto'];
                            $CampoLibre1 = explode(', ', $movimiento_det[0]['CampoLibre1']);
        
                            foreach ($CampoLibre1 as $indice => $valor) {
                                $CodDocumento = explode('.', explode('-', $valor)[0])[1];
                                $SerieDoc = explode('-', $valor)[1];
                                $NumeroDoc = explode('-', $valor)[2];
                                $FecEmision = str_replace('/', '-', explode('-', $valor)[3]);
                                $FecEmision = date('Y-m-d', strtotime($FecEmision));
        
                                $where = 'det.IdMov != "' . $IdMov . '" AND DATE(det.FecEmision) = "' . $FecEmision . '" AND det.CodDocumento = "' . $CodDocumento . '" AND det.SerieDoc = "' . $SerieDoc . '" AND det.NumeroDoc = "' . $NumeroDoc . '"';
        
                                $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                                    $this->CodEmpresa,
                                    0,
                                    'det.IdMovDet, det.Saldo',
                                    [
                                        array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner')
                                    ],
                                    [],
                                    $where,
                                    '',
                                    ''
                                );
        
                                if (count($movimiento_cab) > 0) {
                                    $IdMovDet = $movimiento_cab[0]['IdMovDet'];
                                    $Saldo = $movimiento_cab[0]['Saldo'] + $Monto;
        
                                    (new MovimientoDet())->actualizar($this->CodEmpresa, 0, $IdMovDet, '', '', '', ['Saldo' => $Saldo]);
                                }
                            }
                        }
                    }

                    (new MovimientoCab())->eliminar($this->CodEmpresa, $id, 0);

                    (new MovimientoDet())->eliminar($this->CodEmpresa, $id, 0, 0, '', '', '', '', '');

                    (new SaldoDet())->eliminar($this->CodEmpresa, $id, 0, 0, null);

                    (new HistoricoImp())->eliminar($this->CodEmpresa, $id);

                    if ($this->db->transStatus() === FALSE) {
                        $this->db->transRollback();

                        $result = false;
                    } else {
                        $this->db->transCommit();

                        $result = true;
                    }

                    if ($result) {
                        $estado = true;
                    } else {
                        $estado = false;
                    }

                    $historicoImp = (new HistoricoImp())->getHistoricoImp($this->CodEmpresa, 0, 'idHistImp, Fecha', [], '', '');

                    $resultado = array('estado' => $estado);

                    if (count($historicoImp) > 0) {
                        $tr = '';

                        foreach ($historicoImp as $indice => $valor) {
                            $tr .= '<tr id="tr_historial_' . $valor['idHistImp'] . '" class="tr_historial">';
                            $tr .= '<td role="button" onclick="set_historial(' . $valor['idHistImp'] . ')">' . $valor['Fecha'] . '</td>';
                            $tr .= '</tr>';
                        }

                        $resultado = array('estado' => $estado, 'tr' => $tr);
                    }

                    echo json_encode($resultado);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function importar_registrar_socio_negocio()
    {
        try {
            $numero_documentos = $this->request->getPost('numero_documentos');

            $resultado = array();

            foreach ($numero_documentos as $indice => $valor) {
                if (strlen($valor) == 11) {
                    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $valor,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Referer: http://apis.net.pe/api-ruc',
                            'Authorization: Bearer ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $empresa = json_decode($response);

                    if (isset($empresa->error)) {
                        $resultado[] = array('error' => 'RUC ' . $valor . ' invalido');
                    } else {
                        $this->db->disableForeignKeyChecks();

                        $this->db->transBegin();

                        $data = [
                            'CodEmpresa' => $this->CodEmpresa,
                            'razonsocial' => $empresa->nombre,
                            'ruc' => $valor,
                            'direccion1' => $empresa->direccion,
                            'codubigeo' => '01',
                            'fecingreso' => date('Y-m-d h:i:s'),
                            'CodTipPer' => '02',
                            'CodTipoDoc' => '6',
                            'Idestado' => 11,
                            'IdCondicion' => 12
                        ];

                        $IdSocioN = (new SocioNegocio())->agregar($data);

                        $tipo_socio_negocio = (new TipoSocioNegocio())->getTipoSocioNegocio('', '', [], 'LOWER(DescTipoSN) = "cliente"', '')[0];

                        $data = [
                            'CodTipoSN' => $tipo_socio_negocio['CodTipoSN'],
                            'IdSocioN' => $IdSocioN
                        ];

                        (new SocioNegocioXTipo())->agregar($data);

                        if ($this->db->transStatus() === FALSE) {
                            $this->db->transRollback();

                            $result = false;
                        } else {
                            $this->db->transCommit();

                            $result = true;
                        }

                        if ($result) {
                            $resultado[] = array('success' => 'RUC ' . $valor . ' registrado', 'documento' => $valor);
                        } else {
                            $resultado[] = array('error' => 'RUC ' . $valor . ' no se registro');
                        }
                    }
                } elseif (strlen($valor) == 8) {
                    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://api.apis.net.pe/v1/dni?numero=' . $valor,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_SSL_VERIFYPEER => 0,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 2,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'Referer: https://apis.net.pe/consulta-dni-api',
                            'Authorization: Bearer ' . $token
                        ),
                    ));

                    $response = curl_exec($curl);

                    curl_close($curl);

                    $persona = json_decode($response);

                    if (isset($persona->error)) {
                        $resultado[] = array('error' => 'DNI ' . $valor . ' invalido');
                    } else {
                        $this->db->disableForeignKeyChecks();

                        $this->db->transBegin();

                        $nombres = explode(" ", $persona->nombres);
                        $Nom1 = ucwords(strtolower($nombres[0]));
                        $Nom2 = isset($nombres[1]) && !empty($nombres[1]) ? ucwords(strtolower($nombres[1])) : NULL;

                        $data = [
                            'CodEmpresa' => $this->CodEmpresa,
                            'ApePat' => ucwords(strtolower($persona->apellidoPaterno)),
                            'ApeMat' => ucwords(strtolower($persona->apellidoMaterno)),
                            'Nom1' => $Nom1,
                            'Nom2' => $Nom2,
                            'razonsocial' => $persona->nombre,
                            'docidentidad' => $valor,
                            'codubigeo' => '01',
                            'fecingreso' => date('Y-m-d h:i:s'),
                            'CodTipPer' => '01',
                            'CodTipoDoc' => '1',
                            'Idestado' => 11,
                            'IdCondicion' => 12
                        ];

                        $IdSocioN = (new SocioNegocio())->agregar($data);

                        $tipo_socio_negocio = (new TipoSocioNegocio())->getTipoSocioNegocio('', '', [], 'LOWER(DescTipoSN) = "cliente"', '')[0];

                        $data = [
                            'CodTipoSN' => $tipo_socio_negocio['CodTipoSN'],
                            'IdSocioN' => $IdSocioN
                        ];

                        (new SocioNegocioXTipo())->agregar($data);

                        if ($this->db->transStatus() === FALSE) {
                            $this->db->transRollback();

                            $result = false;
                        } else {
                            $this->db->transCommit();

                            $result = true;
                        }

                        if ($result) {
                            $resultado[] = array('success' => 'DNI ' . $valor . ' registrado', 'documento' => $valor);
                        } else {
                            $resultado[] = array('error' => 'DNI ' . $valor . ' no se registro');
                        }
                    }
                } else {
                    $resultado[] = array('error' => 'Numero de documento ' . $valor . ' invalido');
                }
            }

            echo json_encode($resultado);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_importar()
    {
        try {
            $post = $this->request->getPost();

            $array = array();

            parse_str($post['form'], $array);

            $data_obs = [
                'CodEmpresa' => $this->CodEmpresa,
                'Origen' => 'VE',
                'Tipo' => 'CUENTA',
                'Afecto' => $array['Neto_Cuenta'],
                'Inafecto' => $array['Inafecto_Cuenta'],
                'Exonerado' => $array['Exonerado_Cuenta'],
                'Igv' => $array['Igv_Cuenta'],
                'Icbp' => $array['Icbp_Cuenta'],
                'Descuento' => $array['Descuento_Cuenta'],
                'Otro_Tributo' => $array['Otro_Tributo_Cuenta'],
                'TotalS' => $array['TotalS_Cuenta'],
                'TotalD' => $array['TotalD_Cuenta'],
                'Caja' => $array['Caja_Cuenta']
            ];

            (new MovimientoObs())->actualizar($this->CodEmpresa, 'VE', 'CUENTA', null, $data_obs);

            $movimiento_obs = (new MovimientoObs())->getMovimientoObs($this->CodEmpresa, 0, 'VE', 'DOCUMENTO', 'IdMovObservacion', [], '', '');

            foreach ($movimiento_obs as $indice => $valor) {
                (new MovimientoObs())->actualizar($this->CodEmpresa, '', '', $valor['IdMovObservacion'], ['CodDocumento' => $array['Documento'][$indice]]);
            }

            foreach ($array as $indice => $valor) {
                if (mb_strpos($indice, '_Cuenta') !== false) {
                    unset($array[$indice]);
                }
            }

            unset($array['Documento']);

            $Observaciones_Ruc_Clie = '';
            $Observaciones_Fecha = '';
            $Observaciones_Ruc_Clie_Existe = '';
            $Observaciones_Ruc_Clie_Existe_array = array();
            $Observaciones_CodDoc_Existe = '';
            $Observaciones_NroDocDel = '';
            $Observaciones_Cond_Pago = '';
            $Observaciones_Cond_Pago_Existe = '';
            $Observaciones_Fecha_Corresponde = '';
            $Observaciones_Moneda = '';
            $Observaciones_Moneda_Existe = '';
            $Observaciones_Documento_Existe = '';
            $Observaciones_Documento_Referencia_Existe = '';
            $Observaciones_Documento_Referencia_Contado_Existe = '';
            $Observaciones_Neto_Es_Numero = '';
            $Observaciones_Isc_Es_Numero = '';
            $Observaciones_Descuento_Es_Numero = '';
            $Observaciones_Igv_Es_Numero = '';
            $Observaciones_Percepcion_Es_Numero = '';
            $Observaciones_Inafecto_Es_Numero = '';
            $Observaciones_Exonerado_Es_Numero = '';
            $Observaciones_Otros_Trib_Es_Numero = '';
            $Observaciones_ICBP_Es_Numero = '';
            $Observaciones_Total_Es_Numero = '';

            foreach ($array['NumItem'] as $indice => $valor) {
                if (empty(trim($array['Ruc_Clie'][$indice]))) {
                    $Observaciones_Ruc_Clie .= '<tr>';
                    $Observaciones_Ruc_Clie .= '<td></td>';
                    $Observaciones_Ruc_Clie .= '<td>la Columna Ruc_Clie no puede estar vacio</td>';
                    $Observaciones_Ruc_Clie .= '<td>0</td>';
                    $Observaciones_Ruc_Clie .= '</tr>';
                }

                // if (strtotime(trim(str_replace('/', '-', $array['Fecha'][$indice]))) == false) {
                //     $Observaciones_Fecha .= '<tr>';
                //     $Observaciones_Fecha .= '<td></td>';
                //     $Observaciones_Fecha .= '<td>Fecha Incorrecta</td>';
                //     $Observaciones_Fecha .= '<td>0</td>';
                //     $Observaciones_Fecha .= '</tr>';
                // }

                if (isset($array['Ruc_Clie'][$indice])) {
                    $Ruc_Clie = 0;

                    if (!empty($array['Ruc_Clie'][$indice])) $Ruc_Clie = trim($array['Ruc_Clie'][$indice]);

                    $where = 'IdSocioN = ' . $Ruc_Clie . ' OR ruc = "' . $Ruc_Clie . '" OR docidentidad = "' . $Ruc_Clie . '"';

                    $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, '', [], $where, '');

                    if (count($socio_negocio) == 0) {
                        $Observaciones_Ruc_Clie_Existe .= '<tr>';
                        $Observaciones_Ruc_Clie_Existe .= '<td class="Ruc_Clie_Existe">' . $Ruc_Clie . '</td>';
                        $Observaciones_Ruc_Clie_Existe .= '<td>Ruc\Doc. Identidad\id No Existe</td>';
                        $Observaciones_Ruc_Clie_Existe .= '<td>5</td>';
                        $Observaciones_Ruc_Clie_Existe .= '</tr>';

                        $Observaciones_Ruc_Clie_Existe_array[] = $Ruc_Clie;
                    }
                }

                if (isset($array['CodDoc'][$indice])) {
                    $CodDoc = trim($array['CodDoc'][$indice]);

                    $documento = (new Documento())->getDocumento($this->CodEmpresa, $CodDoc, '', '', [], '', '');

                    if (count($documento) == 0) {
                        $Observaciones_CodDoc_Existe .= '<tr>';
                        $Observaciones_CodDoc_Existe .= '<td>' . $CodDoc . '</td>';
                        $Observaciones_CodDoc_Existe .= '<td>Código de Documento No Existe</td>';
                        $Observaciones_CodDoc_Existe .= '<td>0</td>';
                        $Observaciones_CodDoc_Existe .= '</tr>';
                    }
                }

                if (empty(trim($array['NroDocDel'][$indice]))) {
                    $CodDoc = trim($array['CodDoc'][$indice]);

                    $Observaciones_NroDocDel .= '<tr>';
                    $Observaciones_NroDocDel .= '<td>' . $CodDoc . '</td>';
                    $Observaciones_NroDocDel .= '<td>Número Documento vacio</td>';
                    $Observaciones_NroDocDel .= '<td>0</td>';
                    $Observaciones_NroDocDel .= '</tr>';
                }

                if (!empty(trim($array['NroDocDel'][$indice])) && !is_numeric(trim($array['NroDocDel'][$indice]))) {
                    $NroDocDel = trim($array['NroDocDel'][$indice]);

                    $Observaciones_NroDocDel .= '<tr>';
                    $Observaciones_NroDocDel .= '<td>' . $NroDocDel . '</td>';
                    $Observaciones_NroDocDel .= '<td>Número Documento no es Numérico</td>';
                    $Observaciones_NroDocDel .= '<td>0</td>';
                    $Observaciones_NroDocDel .= '</tr>';
                }

                if (!empty(trim($array['NroDocDel'][$indice])) && is_numeric(trim($array['NroDocDel'][$indice]))) {
                    $NroDocDel = trim($array['NroDocDel'][$indice]);

                    $array_NroDocDel = array();

                    foreach ($array['NroDocDel'] as $indice_auxiliar => $valor_auxiliar) {
                        if (is_numeric(trim($array['NroDocDel'][$indice_auxiliar]))) $array_NroDocDel[] = intval(trim($array['NroDocDel'][$indice_auxiliar]));
                    }

                    $cantidad = array_count_values($array_NroDocDel)[intval($NroDocDel)];

                    if ($cantidad > 1) {
                        $Observaciones_NroDocDel .= '<tr>';
                        $Observaciones_NroDocDel .= '<td>' . $NroDocDel . ' (' . ($cantidad - 1) . ')</td>';
                        $Observaciones_NroDocDel .= '<td>Número Documento se repite</td>';
                        $Observaciones_NroDocDel .= '<td>0</td>';
                        $Observaciones_NroDocDel .= '</tr>';
                    }
                }

                if (empty(trim($array['Cond_Pago'][$indice]))) {
                    $Observaciones_Cond_Pago .= '<tr>';
                    $Observaciones_Cond_Pago .= '<td></td>';
                    $Observaciones_Cond_Pago .= '<td>Condición Pago No Existe</td>';
                    $Observaciones_Cond_Pago .= '<td>0</td>';
                    $Observaciones_Cond_Pago .= '</tr>';
                }

                if (!empty(trim($array['Cond_Pago'][$indice]))) {
                    $Cond_Pago = trim($array['Cond_Pago'][$indice]);

                    $where = 'UPPER(codcondpago) = "' . strtoupper($Cond_Pago) . '" OR UPPER(desccondpago) = "' . strtoupper($Cond_Pago) . '"';

                    $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', '', [], $where, '');

                    if (count($condicion_pago) == 0) {
                        $Observaciones_Cond_Pago_Existe .= '<tr>';
                        $Observaciones_Cond_Pago_Existe .= '<td>' . $Cond_Pago . '</td>';
                        $Observaciones_Cond_Pago_Existe .= '<td>Condición Pago No Existe</td>';
                        $Observaciones_Cond_Pago_Existe .= '<td>0</td>';
                        $Observaciones_Cond_Pago_Existe .= '</tr>';
                    }
                }

                if (strtotime(str_replace('/', '-', trim($array['Fecha'][$indice]))) == true) {
                    $Fecha = trim($array['Fecha'][$indice]);

                    if (date('Y', strtotime(str_replace('/', '-', $Fecha))) != date('Y') || date('m', strtotime(str_replace('/', '-', $Fecha))) != date('m')) {
                        $CodDoc = trim($array['CodDoc'][$indice]);
                        $SerieDoc = trim($array['SerieDoc'][$indice]);
                        $NroDocDel = trim($array['NroDocDel'][$indice]);

                        $Observaciones_Fecha_Corresponde .= '<tr>';
                        $Observaciones_Fecha_Corresponde .= '<td>' . $CodDoc . '-' . $SerieDoc . '-' . $NroDocDel . ' (' . date('d/m/Y', strtotime($Fecha)) . ')</td>';
                        $Observaciones_Fecha_Corresponde .= '<td>Fecha no Corresponde</td>';
                        $Observaciones_Fecha_Corresponde .= '<td>0</td>';
                        $Observaciones_Fecha_Corresponde .= '</tr>';
                    }
                }

                if (empty(trim($array['Moneda'][$indice]))) {
                    $Observaciones_Moneda .= '<tr>';
                    $Observaciones_Moneda .= '<td></td>';
                    $Observaciones_Moneda .= '<td>Moneda No Existe</td>';
                    $Observaciones_Moneda .= '<td>0</td>';
                    $Observaciones_Moneda .= '</tr>';
                }

                if (!empty(trim($array['Moneda'][$indice]))) {
                    $Moneda = trim($array['Moneda'][$indice]);

                    $where = 'UPPER(CodMoneda) = "' . strtoupper($Moneda) . '" OR UPPER(DescMoneda) = "' . strtoupper($Moneda) . '"';

                    $moneda = (new Moneda())->getMoneda('', '', [], $where, '');

                    if (count($moneda) == 0) {
                        $Observaciones_Moneda_Existe .= '<tr>';
                        $Observaciones_Moneda_Existe .= '<td>' . $Moneda . '</td>';
                        $Observaciones_Moneda_Existe .= '<td>Moneda No Existe</td>';
                        $Observaciones_Moneda_Existe .= '<td>0</td>';
                        $Observaciones_Moneda_Existe .= '</tr>';
                    }
                }

                if (!empty(trim($array['CodDoc'][$indice]))) {
                    $CodDoc = trim($array['CodDoc'][$indice]);
                    $SerieDoc = trim($array['SerieDoc'][$indice]);
                    $NroDocDel = trim($array['NroDocDel'][$indice]);

                    $where = 'UPPER(CodDocumento) = "' . strtoupper($CodDoc) . '" AND UPPER(SerieDoc) = "' . strtoupper($SerieDoc) . '" AND UPPER(NumeroDoc) = "' . strtoupper($NroDocDel) . '"';

                    $movimiento_det = (new MovimientoDet())->getMovimientoDet($this->CodEmpresa, 0, 0, '', [], [], $where, '');

                    if (count($movimiento_det) > 0) {
                        $Observaciones_Documento_Existe .= '<tr>';
                        $Observaciones_Documento_Existe .= '<td>' . $CodDoc . $SerieDoc . $NroDocDel . '</td>';
                        $Observaciones_Documento_Existe .= '<td>Este documento ya existe en el sistema</td>';
                        $Observaciones_Documento_Existe .= '<td>0</td>';
                        $Observaciones_Documento_Existe .= '</tr>';
                    }
                }

                if (!empty(trim($array['Ruc_Clie'][$indice])) && !empty(trim($array['CodDocRef'][$indice])) && !empty(trim($array['SerieDocRef'][$indice])) && !empty(trim($array['NumDocRef'][$indice]))) {
                    $Ruc_Clie = trim($array['Ruc_Clie'][$indice]);
                    $CodDocRef = strtoupper(trim($array['CodDocRef'][$indice]));
                    $SerieDocRef = strtoupper(trim($array['SerieDocRef'][$indice]));
                    $NumDocRef = strtoupper(trim($array['NumDocRef'][$indice]));

                    $where = 'IdSocioN = ' . $Ruc_Clie . ' OR ruc = "' . $Ruc_Clie . '" OR docidentidad = "' . $Ruc_Clie . '"';

                    $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, 'IdSocioN', [], $where, '');

                    if (count($socio_negocio) > 0) {
                        $IdSocioN = $socio_negocio[0]['IdSocioN'];

                        $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                            $this->CodEmpresa,
                            0,
                            0,
                            '',
                            [],
                            [
                                array('IdSocioN' => $IdSocioN, 'CodDocumento' => $CodDocRef, 'SerieDoc' => $SerieDocRef, 'NumeroDoc' => $NumDocRef)
                            ],
                            '',
                            ''
                        );

                        if (count($movimiento_det) == 0) {
                            $Observaciones_Documento_Referencia_Existe .= '<tr>';
                            $Observaciones_Documento_Referencia_Existe .= '<td>' . $CodDocRef . $SerieDoc . $NumDocRef . 'RUC: ' . $Ruc_Clie . '</td>';
                            $Observaciones_Documento_Referencia_Existe .= '<td>No Existe Provisión del Documento Referencia</td>';
                            $Observaciones_Documento_Referencia_Existe .= '<td>0</td>';
                            $Observaciones_Documento_Referencia_Existe .= '</tr>';
                        } else {
                            $CodCondPago = $movimiento_det[0]['CodCondPago'];

                            if ($CodCondPago == 'CP000') {
                                $Observaciones_Documento_Referencia_Contado_Existe .= '<tr>';
                                $Observaciones_Documento_Referencia_Contado_Existe .= '<td>' . $CodDocRef . $SerieDoc . $NumDocRef . '</td>';
                                $Observaciones_Documento_Referencia_Contado_Existe .= '<td>Comprobante de referencia no puede estar al contado</td>';
                                $Observaciones_Documento_Referencia_Contado_Existe .= '<td>0</td>';
                                $Observaciones_Documento_Referencia_Contado_Existe .= '</tr>';
                            }
                        }
                    }
                }

                if (!empty(trim($array['Neto'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Neto'][$indice])))) {
                    $Observaciones_Neto_Es_Numero .= '<tr>';
                    $Observaciones_Neto_Es_Numero .= '<td>' . trim($array['Neto'][$indice]) . '</td>';
                    $Observaciones_Neto_Es_Numero .= '<td>Neto No Es Numero</td>';
                    $Observaciones_Neto_Es_Numero .= '<td>0</td>';
                    $Observaciones_Neto_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Isc'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Isc'][$indice])))) {
                    $Observaciones_Isc_Es_Numero .= '<tr>';
                    $Observaciones_Isc_Es_Numero .= '<td>' . trim($array['Isc'][$indice]) . '</td>';
                    $Observaciones_Isc_Es_Numero .= '<td>Isc No Es Numero</td>';
                    $Observaciones_Isc_Es_Numero .= '<td>0</td>';
                    $Observaciones_Isc_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Descuento'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Descuento'][$indice])))) {
                    $Observaciones_Descuento_Es_Numero .= '<tr>';
                    $Observaciones_Descuento_Es_Numero .= '<td>' . trim($array['Descuento'][$indice]) . '</td>';
                    $Observaciones_Descuento_Es_Numero .= '<td>Descuento No Es Numero</td>';
                    $Observaciones_Descuento_Es_Numero .= '<td>0</td>';
                    $Observaciones_Descuento_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Igv'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Igv'][$indice])))) {
                    $Observaciones_Igv_Es_Numero .= '<tr>';
                    $Observaciones_Igv_Es_Numero .= '<td>' . trim($array['Igv'][$indice]) . '</td>';
                    $Observaciones_Igv_Es_Numero .= '<td>Igv No Es Numero</td>';
                    $Observaciones_Igv_Es_Numero .= '<td>0</td>';
                    $Observaciones_Igv_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Percepcion'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Percepcion'][$indice])))) {
                    $Observaciones_Percepcion_Es_Numero .= '<tr>';
                    $Observaciones_Percepcion_Es_Numero .= '<td>' . trim($array['Percepcion'][$indice]) . '</td>';
                    $Observaciones_Percepcion_Es_Numero .= '<td>Percepcion No Es Numero</td>';
                    $Observaciones_Percepcion_Es_Numero .= '<td>0</td>';
                    $Observaciones_Percepcion_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Inafecto'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Inafecto'][$indice])))) {
                    $Observaciones_Inafecto_Es_Numero .= '<tr>';
                    $Observaciones_Inafecto_Es_Numero .= '<td>' . trim($array['Inafecto'][$indice]) . '</td>';
                    $Observaciones_Inafecto_Es_Numero .= '<td>Inafecto No Es Numero</td>';
                    $Observaciones_Inafecto_Es_Numero .= '<td>0</td>';
                    $Observaciones_Inafecto_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Exonerado'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Exonerado'][$indice])))) {
                    $Observaciones_Exonerado_Es_Numero .= '<tr>';
                    $Observaciones_Exonerado_Es_Numero .= '<td>' . trim($array['Exonerado'][$indice]) . '</td>';
                    $Observaciones_Exonerado_Es_Numero .= '<td>Exonerado No Es Numero</td>';
                    $Observaciones_Exonerado_Es_Numero .= '<td>0</td>';
                    $Observaciones_Exonerado_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Otros_Trib'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Otros_Trib'][$indice])))) {
                    $Observaciones_Otros_Trib_Es_Numero .= '<tr>';
                    $Observaciones_Otros_Trib_Es_Numero .= '<td>' . trim($array['Otros_Trib'][$indice]) . '</td>';
                    $Observaciones_Otros_Trib_Es_Numero .= '<td>Otros_Trib No Es Numero</td>';
                    $Observaciones_Otros_Trib_Es_Numero .= '<td>0</td>';
                    $Observaciones_Otros_Trib_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['ICBP'][$indice])) && !is_numeric(str_replace(',', '', trim($array['ICBP'][$indice])))) {
                    $Observaciones_ICBP_Es_Numero .= '<tr>';
                    $Observaciones_ICBP_Es_Numero .= '<td>' . trim($array['ICBP'][$indice]) . '</td>';
                    $Observaciones_ICBP_Es_Numero .= '<td>ICBP No Es Numero</td>';
                    $Observaciones_ICBP_Es_Numero .= '<td>0</td>';
                    $Observaciones_ICBP_Es_Numero .= '</tr>';
                }

                if (!empty(trim($array['Total'][$indice])) && !is_numeric(str_replace(',', '', trim($array['Total'][$indice])))) {
                    $Observaciones_Total_Es_Numero .= '<tr>';
                    $Observaciones_Total_Es_Numero .= '<td>' . trim($array['Total'][$indice]) . '</td>';
                    $Observaciones_Total_Es_Numero .= '<td>Total No Es Numero</td>';
                    $Observaciones_Total_Es_Numero .= '<td>0</td>';
                    $Observaciones_Total_Es_Numero .= '</tr>';
                }
            }

            $Observaciones = $Observaciones_Ruc_Clie .
                $Observaciones_Fecha .
                $Observaciones_Ruc_Clie_Existe .
                $Observaciones_CodDoc_Existe .
                $Observaciones_NroDocDel .
                $Observaciones_Cond_Pago .
                $Observaciones_Cond_Pago_Existe .
                $Observaciones_Fecha_Corresponde .
                $Observaciones_Moneda .
                $Observaciones_Moneda_Existe .
                $Observaciones_Documento_Existe .
                $Observaciones_Documento_Referencia_Existe .
                $Observaciones_Documento_Referencia_Contado_Existe .
                $Observaciones_Neto_Es_Numero .
                $Observaciones_Isc_Es_Numero .
                $Observaciones_Descuento_Es_Numero .
                $Observaciones_Igv_Es_Numero .
                $Observaciones_Percepcion_Es_Numero .
                $Observaciones_Inafecto_Es_Numero .
                $Observaciones_Exonerado_Es_Numero .
                $Observaciones_Otros_Trib_Es_Numero .
                $Observaciones_ICBP_Es_Numero .
                $Observaciones_Total_Es_Numero;

            if (strlen($Observaciones) > 0) {
                $resultado = array('estado' => false, 'estado_observacion' => true, 'observaciones' => $Observaciones, 'rucs' => $Observaciones_Ruc_Clie_Existe_array);

                echo json_encode($resultado);
            } else {
                $this->db->disableForeignKeyChecks();

                $this->db->transBegin();

                $predeterminado = (new Predeterminado())->getPredeterminado('CodTV_ve, CodTV_cc, CodTV_di');

                $CodTV_ve = $predeterminado['CodTV_ve'];

                $CodTV_cc = $predeterminado['CodTV_cc'];

                $CodTV_di = $predeterminado['CodTV_di'];

                $codigo_voucher_maximo = $CodTV_ve . date('m') . '000001';

                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '000001';

                $data = [
                    'CodEmpresa' => $this->CodEmpresa,
                    'Periodo' => date('Y'),
                    'Mes' => date('m'),
                    'Tipo' => 4,
                    'Fecha' => date('Y-m-d h:i:s'),
                    'Descripcion' => NULL
                ];

                $idHistImp = (new HistoricoImp())->agregar($data);

                foreach ($array['NumItem'] as $indice => $valor) {
                    $NumItem = 1;
                    $NumItem_cc = 1;
                    $NumItem_di = 1;

                    $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                        $this->CodEmpresa,
                        0,
                        'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                        [],
                        [
                            array('Origen' => array($CodTV_ve, 'IMPORVEN'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                        ],
                        '',
                        '',
                        ''
                    );

                    if ($movimiento_cab[0]['codigo']) {
                        $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                        if (strlen($movimiento_cab[0]['codigo']) == 1) {
                            $codigo_voucher_maximo = $CodTV_ve . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                            $codigo_voucher_maximo = $CodTV_ve . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                            $codigo_voucher_maximo = $CodTV_ve . date('m') . '000' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                            $codigo_voucher_maximo = $CodTV_ve . date('m') . '00' . $movimiento_cab[0]['codigo'];
                        } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                            $codigo_voucher_maximo = $CodTV_ve . date('m') . '0' . $movimiento_cab[0]['codigo'];
                        } else {
                            $codigo_voucher_maximo = $CodTV_ve . date('m') . $movimiento_cab[0]['codigo'];
                        }
                    }

                    $movimiento_obs = (new MovimientoObs())->getMovimientoObs($this->CodEmpresa, 0, 'VE', 'DOCUMENTO', 'CodSunat', [], 'CodDocumento = "' . trim($array['CodDoc'][$indice]) . '"', '');

                    $CodSunat = $movimiento_obs[0]['CodSunat'];

                    $where = 'IdSocioN = ' . trim($array['Ruc_Clie'][$indice]) . ' OR ruc = "' . trim($array['Ruc_Clie'][$indice]) . '" OR docidentidad = "' . trim($array['Ruc_Clie'][$indice]) . '"';

                    $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, 0, 'IdSocioN', [], $where, '');

                    $IdSocioN = $socio_negocio[0]['IdSocioN'];

                    $where = 'UPPER(codcondpago) = "' . strtoupper(trim($array['Cond_Pago'][$indice])) . '" OR UPPER(desccondpago) = "' . strtoupper(trim($array['Cond_Pago'][$indice])) . '"';

                    $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', 'codcondpago', [], $where, '');

                    $CodTV = '';

                    if ($condicion_pago[0]['codcondpago'] == 'CP000') {
                        $CodTV = 'VEC';
                    } else if ($condicion_pago[0]['codcondpago'] == 'CP001') {
                        $CodTV = 'VEN';
                    }

                    $where = 'UPPER(CodMoneda) = "' . strtoupper(trim($array['Moneda'][$indice])) . '" OR UPPER(DescMoneda) = "' . strtoupper(trim($array['Moneda'][$indice])) . '"';

                    $moneda = (new Moneda())->getMoneda('', 'CodMoneda', [], $where, '');

                    $TotalSol = 0;
                    $TotalDol = 0;

                    if ($moneda[0]['CodMoneda'] == 'MO001') {
                        $TotalSol = str_replace('-', '', str_replace(',', '', trim($array['Total'][$indice])));
                    } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                        $TotalDol = str_replace('-', '', str_replace(',', '', trim($array['Total'][$indice])));
                    }

                    $FecEmision = date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice]))));

                    $tipo_cambio = (new TipoCambio())->getTipoCambio($this->CodEmpresa, $FecEmision, 'ValorVenta', [], '', '');

                    if (count($tipo_cambio) > 0) {
                        $ValorTC = $tipo_cambio[0]['ValorVenta'];
                    } else {
                        $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=' . $FecEmision,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 2,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Referer: https://apis.net.pe/tipo-de-cambio-sunat-api',
                                'Authorization: Bearer ' . $token
                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);

                        $ValorTC = json_decode($response)->venta;
                    }

                    $data = [
                        'CodEmpresa' => $this->CodEmpresa,
                        'Periodo' => date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                        'Mes' => date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                        'Codmov' => $codigo_voucher_maximo,
                        'CodTV' => $CodTV,
                        'IdMovRef' => NULL,
                        'IdMovAplica' => NULL,
                        'FecContable' => date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))) . ' 00:00:00',
                        'TotalSol' => $TotalSol,
                        'TotalDol' => $TotalDol,
                        'Origen' => 'IMPORVEN',
                        'Glosa' => empty(trim($array['Glosa'][$indice])) ? 'POR LAS VENTAS DEL MES IMPORTADAS DEL SISTEMA' : trim($array['Glosa'][$indice]),
                        'Estado' => 0,
                        'Importado' => $idHistImp,
                        'codOtroSis' => NULL,
                        'ValorTC' => 0,
                        'Detraccion' => empty(trim($array['Detraccion'][$indice])) ? 0 : trim($array['Detraccion'][$indice]),
                        'FlagInterno' => 0
                    ];

                    $IdMov = (new MovimientoCab())->agregar($data);

                    $IdMov_cc = 0;

                    $IdMov_di = 0;

                    if ($condicion_pago[0]['codcondpago'] == 'CP000' && $CodSunat != '07') {
                        $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                            $this->CodEmpresa,
                            0,
                            'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                            [],
                            [
                                array('Origen' => array('VEN_CO', 'IMPORCOB'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                            ],
                            '',
                            '',
                            ''
                        );

                        $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '00000' . 1;

                        if ($movimiento_cab[0]['codigo']) {
                            $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                            if (strlen($movimiento_cab[0]['codigo']) == 1) {
                                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '00' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . '0' . $movimiento_cab[0]['codigo'];
                            } else {
                                $codigo_voucher_maximo_cc = $CodTV_cc . date('m') . $movimiento_cab[0]['codigo'];
                            }
                        }

                        $data = [
                            'CodEmpresa' => $this->CodEmpresa,
                            'Periodo' => date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                            'Mes' => date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                            'Codmov' => $codigo_voucher_maximo_cc,
                            'CodTV' => $CodTV_cc,
                            'IdMovRef' => $IdMov,
                            'IdMovAplica' => NULL,
                            'FecContable' => date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))) . ' 00:00:00',
                            'TotalSol' => $TotalSol,
                            'TotalDol' => $TotalDol,
                            'Origen' => 'IMPORCOB',
                            'Glosa' => 'POR LAS VENTAS COBRADAS A CLIENTES',
                            'Estado' => 0,
                            'Importado' => $idHistImp,
                            'codOtroSis' => NULL,
                            'ValorTC' => $ValorTC,
                            'Detraccion' => empty(trim($array['Detraccion'][$indice])) ? 0 : trim($array['Detraccion'][$indice]),
                            'FlagInterno' => 0
                        ];

                        $IdMov_cc = (new MovimientoCab())->agregar($data);
                    } else if ($CodSunat == '07') {
                        $movimiento_cab = (new MovimientoCab())->getMovimientoCab(
                            $this->CodEmpresa,
                            0,
                            'MAX(SUBSTRING(Codmov, 6)) AS codigo',
                            [],
                            [
                                array('Origen' => array('VEN_AP', 'IMPORVEN_AP'), 'Periodo' => date('Y'), 'Mes' => date('m'))
                            ],
                            '',
                            '',
                            ''
                        );

                        $codigo_voucher_maximo_di = $CodTV_di . date('m') . '00000' . 1;

                        if ($movimiento_cab[0]['codigo']) {
                            $movimiento_cab[0]['codigo'] = $movimiento_cab[0]['codigo'] + 1;

                            if (strlen($movimiento_cab[0]['codigo']) == 1) {
                                $codigo_voucher_maximo_di = $CodTV_di . date('m') . '00000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 2) {
                                $codigo_voucher_maximo_di = $CodTV_di . date('m') . '0000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 3) {
                                $codigo_voucher_maximo_di = $CodTV_di . date('m') . '000' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 4) {
                                $codigo_voucher_maximo_di = $CodTV_di . date('m') . '00' . $movimiento_cab[0]['codigo'];
                            } else if (strlen($movimiento_cab[0]['codigo']) == 5) {
                                $codigo_voucher_maximo_di = $CodTV_di . date('m') . '0' . $movimiento_cab[0]['codigo'];
                            } else {
                                $codigo_voucher_maximo_di = $CodTV_di . date('m') . $movimiento_cab[0]['codigo'];
                            }
                        }

                        $data = [
                            'CodEmpresa' => $this->CodEmpresa,
                            'Periodo' => date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                            'Mes' => date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                            'Codmov' => $codigo_voucher_maximo_di,
                            'CodTV' => $CodTV_di,
                            'IdMovRef' => NULL,
                            'IdMovAplica' => $IdMov,
                            'FecContable' => date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))) . ' 00:00:00',
                            'TotalSol' => $TotalSol,
                            'TotalDol' => $TotalDol,
                            'Origen' => 'IMPORVEN_AP',
                            'Glosa' => 'POR LAS VENTAS DEL MES IMPORTADAS DEL SISTEMA',
                            'Estado' => 0,
                            'Importado' => $idHistImp,
                            'codOtroSis' => NULL,
                            'ValorTC' => $ValorTC,
                            'Detraccion' => empty(trim($array['Detraccion'][$indice])) ? 0 : trim($array['Detraccion'][$indice]),
                            'FlagInterno' => 0
                        ];

                        $IdMov_di = (new MovimientoCab())->agregar($data);
                    }

                    $data = $this->datos_movimiento_det();

                    $data['IdMov'] = $IdMov;
                    $data['Periodo'] = date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice]))));
                    $data['Mes'] = date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice]))));
                    $data['ValorTC'] = $ValorTC;
                    $data['CodMoneda'] = $moneda[0]['CodMoneda'];
                    $data['FecEmision'] = date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))) . ' 00:00:00';
                    $data['FecVcto'] = empty(trim($array['Fec_Vcmto'][$indice])) ? date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))) . ' 00:00:00' : date('Y-m-d', strtotime(str_replace('/', '-', trim($array['Fec_Vcmto'][$indice])))) . ' 00:00:00';
                    $data['IdSocioN'] = $IdSocioN;
                    $data['CodDocumento'] = trim($array['CodDoc'][$indice]);
                    $data['SerieDoc'] = trim($array['SerieDoc'][$indice]);
                    $data['NumeroDoc'] = trim($array['NroDocDel'][$indice]);
                    $data['NumeroDocF'] = trim($array['NroDocAl'][$indice]);
                    $data['Destino'] = 'NO';
                    $data['RegistroSunat'] = 'NINGUNO';
                    $data['CodCcosto'] = empty(trim($array['CodCCosto'][$indice])) ? NULL : trim($array['CodCCosto'][$indice]);
                    $data['CodCondPago'] = $condicion_pago[0]['codcondpago'];
                    $data['DocRetencion'] = empty(trim($array['Cons_Reten'][$indice])) ? NULL : trim($array['Cons_Reten'][$indice]);
                    $data['DocDetraccion'] = empty(trim($array['Cons_Detra'][$indice])) ? NULL : trim($array['Cons_Detra'][$indice]);
                    $data['PorcRetencion'] = empty(trim($array['Total_Reten'][$indice])) ? 0 : trim($array['Total_Reten'][$indice]);
                    $data['PorcDetraccion'] = empty(trim($array['Total_Detra'][$indice])) ? 0 : trim($array['Total_Detra'][$indice]);
                    $data['FechaDetraccion'] = empty(trim($array['Fecha_Detra'][$indice])) ? 0 : trim($array['Fecha_Detra'][$indice]);
                    $data['CodTipoPago'] = empty(trim($array['Tipo_Pago'][$indice])) ? NULL : trim($array['Tipo_Pago'][$indice]);
                    $data['Importado'] = $idHistImp;
                    $data['CodTipoCliente'] = empty(trim($array['CodTipoCliente'][$indice])) ? NULL : trim($array['CodTipoCliente'][$indice]);

                    $Total = empty(str_replace('-', '', str_replace(',', '', trim($array['Total'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Total'][$indice])));
                    $Igv = empty(str_replace('-', '', str_replace(',', '', trim($array['Igv'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Igv'][$indice])));
                    $Neto = empty(str_replace('-', '', str_replace(',', '', trim($array['Neto'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Neto'][$indice])));
                    $Isc = empty(str_replace('-', '', str_replace(',', '', trim($array['Isc'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Isc'][$indice])));
                    $Descuento = empty(str_replace('-', '', str_replace(',', '', trim($array['Descuento'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Descuento'][$indice])));
                    $Percepcion = empty(str_replace('-', '', str_replace(',', '', trim($array['Percepcion'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Percepcion'][$indice])));
                    $Inafecto = empty(str_replace('-', '', str_replace(',', '', trim($array['Inafecto'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Inafecto'][$indice])));
                    $Exonerado = empty(str_replace('-', '', str_replace(',', '', trim($array['Exonerado'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Exonerado'][$indice])));
                    $Otros_Trib = empty(str_replace('-', '', str_replace(',', '', trim($array['Otros_Trib'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['Otros_Trib'][$indice])));
                    $ICBP = empty(str_replace('-', '', str_replace(',', '', trim($array['ICBP'][$indice])))) ? 0 : str_replace('-', '', str_replace(',', '', trim($array['ICBP'][$indice])));

                    $Valor_CRV = 1;

                    if ($CodSunat == '07') {
                        $Valor_CRV = -1;
                    }

                    $IdMovDetRef = 0;

                    if (!empty($Total) && $Total != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['CodCuenta'] = $data_obs['TotalS'];
                            $datos['DebeSol'] = $Total;
                            $datos['DebeDol'] = $Total / $ValorTC;

                            $datos['BaseImpSunatS'] = $Neto * $Valor_CRV;
                            $datos['BaseImpSunatD'] = ($Neto / $ValorTC) * $Valor_CRV;
                            $datos['InafectoS'] = $Inafecto * $Valor_CRV;
                            $datos['InafectoD'] = ($Inafecto / $ValorTC) * $Valor_CRV;
                            $datos['ExoneradoS'] = $Exonerado * $Valor_CRV;
                            $datos['ExoneradoD'] = ($Exonerado / $ValorTC) * $Valor_CRV;
                            $datos['ISCS'] = $Isc * $Valor_CRV;
                            $datos['ISCD'] = ($Isc / $ValorTC) * $Valor_CRV;
                            $datos['IGVSunatS'] = $Igv * $Valor_CRV;
                            $datos['IGVSunatD'] = ($Igv / $ValorTC) * $Valor_CRV;
                            $datos['PercepcionS'] = $Percepcion * $Valor_CRV;
                            $datos['PercepcionD'] = ($Percepcion / $ValorTC) * $Valor_CRV;
                            $datos['OtroTributoS'] = $Otros_Trib * $Valor_CRV;
                            $datos['OtroTributoD'] = ($Otros_Trib / $ValorTC) * $Valor_CRV;
                            $datos['TotalS'] = $Total * $Valor_CRV;
                            $datos['TotalD'] = ($Total / $ValorTC) * $Valor_CRV;
                            $datos['DescuentoS'] = $Descuento * $Valor_CRV;
                            $datos['DescuentoD'] = ($Descuento / $ValorTC) * $Valor_CRV;
                            $datos['IcbpS'] = $ICBP * $Valor_CRV;
                            $datos['IcbpD'] = ($ICBP / $ValorTC) * $Valor_CRV;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['CodCuenta'] = $data_obs['TotalD'];
                            $datos['DebeSol'] = $Total * $ValorTC;
                            $datos['DebeDol'] = $Total;

                            $datos['BaseImpSunatS'] = ($Neto * $ValorTC) * $Valor_CRV;
                            $datos['BaseImpSunatD'] = $Neto * $Valor_CRV;
                            $datos['InafectoS'] = ($Inafecto * $ValorTC) * $Valor_CRV;
                            $datos['InafectoD'] = $Inafecto * $Valor_CRV;
                            $datos['ExoneradoS'] = ($Exonerado * $ValorTC) * $Valor_CRV;
                            $datos['ExoneradoD'] = $Exonerado * $Valor_CRV;
                            $datos['ISCS'] = ($Isc * $ValorTC) * $Valor_CRV;
                            $datos['ISCD'] = $Isc * $Valor_CRV;
                            $datos['IGVSunatS'] = ($Igv * $ValorTC) * $Valor_CRV;
                            $datos['IGVSunatD'] = $Igv * $Valor_CRV;
                            $datos['PercepcionS'] = ($Percepcion * $ValorTC) * $Valor_CRV;
                            $datos['PercepcionD'] = $Percepcion * $Valor_CRV;
                            $datos['OtroTributoS'] = ($Otros_Trib * $ValorTC) * $Valor_CRV;
                            $datos['OtroTributoD'] = $Otros_Trib * $Valor_CRV;
                            $datos['TotalS'] = ($Total * $ValorTC) * $Valor_CRV;
                            $datos['TotalD'] = $Total * $Valor_CRV;
                            $datos['DescuentoS'] = ($Descuento * $ValorTC) * $Valor_CRV;
                            $datos['DescuentoD'] = $Descuento * $Valor_CRV;
                            $datos['IcbpS'] = ($ICBP * $ValorTC) * $Valor_CRV;
                            $datos['IcbpD'] = $ICBP * $Valor_CRV;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['RegistroSunat'] = 'VENTAS';
                        $datos['CodDocumentoRef'] = empty(trim($array['CodDocRef'][$indice])) ? NULL : trim($array['CodDocRef'][$indice]);
                        $datos['SerieDocRef'] = empty(trim($array['SerieDocRef'][$indice])) ? NULL : trim($array['SerieDocRef'][$indice]);
                        $datos['NumeroDocRef'] = empty(trim($array['NumDocRef'][$indice])) ? NULL : trim($array['NumDocRef'][$indice]);

                        if (!empty($array['CodDocRef'][$indice]) && !empty($array['SerieDocRef'][$indice]) && !empty($array['NumDocRef'][$indice])) {
                            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                $this->CodEmpresa,
                                0,
                                0,
                                'IdMovDet, FecEmision, BaseImpSunatS, BaseImpSunatD',
                                [],
                                [
                                    array('IdSocioN' => $IdSocioN, 'CodDocumento' => trim($array['CodDocRef'][$indice]), 'SerieDoc' => trim($array['SerieDocRef'][$indice]), 'NumeroDoc' => trim($array['NumDocRef'][$indice]))
                                ],
                                '',
                                ''
                            );

                            if (count($movimiento_det) > 0) {
                                $datos['FecEmisionRef'] = $movimiento_det[0]['FecEmision'];
                                $datos['CampoLibre1'] = '.' . trim($array['CodDocRef'][$indice]) . '-' .
                                    trim($array['SerieDocRef'][$indice]) . '-' .
                                    trim($array['NumDocRef'][$indice]) . '-' .
                                    date('d/m/Y', strtotime($movimiento_det[0]['FecEmision'])) . '-' .
                                    number_format($movimiento_det[0]['BaseImpSunatS'], 2, '.', '') . '-' . number_format($movimiento_det[0]['BaseImpSunatD'], 2, '.', '');
                            }
                        } else {
                            $datos['FecEmisionRef'] = NULL;
                        }

                        $datos['TipoOperacion'] = 25;
                        $datos['Parametro'] = 'TOTAL';
                        $datos['Monto'] = $Total;

                        if ($condicion_pago[0]['codcondpago'] == 'CP000') {
                            $datos['Saldo'] = 0;
                        } else if ($condicion_pago[0]['codcondpago'] == 'CP001') {
                            $datos['Saldo'] = $Total;
                        }

                        $datos['CtaCte'] = 1;
                        $datos['CodTipoSN'] = 1;
                        $datos['TCcierre'] = $ValorTC;

                        if (!empty($Neto) && $Neto != 0) {
                            $datos['CodCuentaLibre'] = $data_obs['Afecto'];
                        } else if (!empty($Inafecto) && $Inafecto != 0) {
                            $datos['CodCuentaLibre'] = $data_obs['Inafecto'];
                        } else if (!empty($Exonerado) && $Exonerado != 0) {
                            $datos['CodCuentaLibre'] = $data_obs['Exonerado'];
                        }

                        $IdMovDetRef = (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($Igv) && $Igv != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $Igv;
                            $datos['HaberDol'] = $Igv / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $Igv * $ValorTC;
                            $datos['HaberDol'] = $Igv;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Igv'];
                        $datos['Parametro'] = 'IGV';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($Neto) && $Neto != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $Neto;
                            $datos['HaberDol'] = $Neto / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $Neto * $ValorTC;
                            $datos['HaberDol'] = $Neto;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Afecto'];
                        $datos['Parametro'] = 'AFECTO';

                        (new MovimientoDet())->agregar($datos);
                    }

                    // if (!empty($Isc)) {
                    //     $datos = $data;

                    //     if ($moneda[0]['CodMoneda'] == 'MO001') {
                    //         $datos['HaberSol'] = $Isc;
                    //         $datos['HaberDol'] = $Isc / $ValorTC;
                    //     } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                    //         $datos['HaberSol'] = $Isc * $ValorTC;
                    //         $datos['HaberDol'] = $Isc;
                    //     }

                    //     $datos['NumItem'] = $NumItem++;
                    //     $datos['CodCuenta'] = NULL;
                    //     $datos['Parametro'] = 'ISC';

                    //     $this->movimientoDetModel = new MovimientoDetModel();

                    //     (new MovimientoDet())->insert($datos);
                    // }

                    if (!empty($Descuento) && $Descuento != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['DebeSol'] = $Descuento;
                            $datos['DebeDol'] = $Descuento / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['DebeSol'] = $Descuento * $ValorTC;
                            $datos['DebeDol'] = $Descuento;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Descuento'];
                        $datos['Parametro'] = 'DESCUENTO';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($Percepcion) && $Percepcion != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $Percepcion;
                            $datos['HaberDol'] = $Percepcion / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $Percepcion * $ValorTC;
                            $datos['HaberDol'] = $Percepcion;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = 40113;
                        $datos['Parametro'] = 'PERCEPCION';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($Inafecto) && $Inafecto != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $Inafecto;
                            $datos['HaberDol'] = $Inafecto / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $Inafecto * $ValorTC;
                            $datos['HaberDol'] = $Inafecto;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Inafecto'];
                        $datos['Parametro'] = 'INAFECTO';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($Exonerado) && $Exonerado != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $Exonerado;
                            $datos['HaberDol'] = $Exonerado / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $Exonerado * $ValorTC;
                            $datos['HaberDol'] = $Exonerado;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Exonerado'];
                        $datos['Parametro'] = 'EXONERADO';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($Otros_Trib) && $Otros_Trib != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['DebeSol'] = $Otros_Trib;
                            $datos['DebeDol'] = $Otros_Trib / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['DebeSol'] = $Otros_Trib * $ValorTC;
                            $datos['DebeDol'] = $Otros_Trib;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Otro_Tributo'];
                        $datos['Parametro'] = 'OTRO TRIBUTO';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if (!empty($ICBP) && $ICBP != 0) {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $ICBP;
                            $datos['HaberDol'] = $ICBP / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $ICBP * $ValorTC;
                            $datos['HaberDol'] = $ICBP;
                        }

                        $datos['NumItem'] = $NumItem++;
                        $datos['CodCuenta'] = $data_obs['Icbp'];
                        $datos['Parametro'] = 'ICBP';

                        (new MovimientoDet())->agregar($datos);
                    }

                    if ($condicion_pago[0]['codcondpago'] == 'CP000' && $CodSunat != '07') {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['DebeSol'] = $Total;
                            $datos['DebeDol'] = $Total / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['DebeSol'] = $Total * $ValorTC;
                            $datos['DebeDol'] = $Total;
                        }

                        $datos['IdMov'] = $IdMov_cc;
                        $datos['NumItem'] = $NumItem_cc++;
                        $datos['CodCuenta'] = $data_obs['Caja'];
                        $datos['RegistroSunat'] = 'NINGUNO';
                        $datos['CodCcosto'] = NULL;
                        $datos['CodCondPago'] = NULL;
                        $datos['DocRetencion'] = NULL;
                        $datos['DocDetraccion'] = NULL;
                        $datos['PorcRetencion'] = NULL;
                        $datos['PorcDetraccion'] = NULL;
                        $datos['FechaDetraccion'] = NULL;
                        $datos['CodTipoPago'] = NULL;
                        $datos['Parametro'] = 'BANCO';
                        $datos['Monto'] = 0;
                        $datos['Saldo'] = 0;
                        $datos['CodTipoCliente'] = NULL;

                        (new MovimientoDet())->agregar($datos);

                        $datos['NumItem'] = $NumItem_cc++;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['CodCuenta'] = $data_obs['TotalS'];

                            $datos['DebeSol'] = 0;
                            $datos['DebeDol'] = 0;
                            $datos['HaberSol'] = $Total;
                            $datos['HaberDol'] = $Total / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['CodCuenta'] = $data_obs['TotalD'];

                            $datos['DebeSol'] = 0;
                            $datos['DebeDol'] = 0;
                            $datos['HaberSol'] = $Total * $ValorTC;
                            $datos['HaberDol'] = $Total;
                        }

                        $datos['Parametro'] = NULL;
                        $datos['TipoPC'] = 29;

                        $IdMovDet = (new MovimientoDet())->agregar($datos);

                        if ((isset($IdMov_cc) && !empty($IdMov_cc)) &&
                            (isset($IdMovDet) && !empty($IdMovDet)) &&
                            (isset($IdMovDetRef) && !empty($IdMovDetRef))
                        ) {
                            $data = [
                                'CodEmpresa' => $this->CodEmpresa,
                                'IdMov' => $IdMov_cc,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $IdMovDetRef,
                                'Periodo' => date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                                'Mes' => date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                                'Importado' => $idHistImp,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            if ($moneda[0]['CodMoneda'] == 'MO001') {
                                $data['TotalDetSol'] = $Total;
                                $data['TotalDetDol'] = $Total / $ValorTC;
                            } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                                $data['TotalDetSol'] = $Total * $ValorTC;
                                $data['TotalDetDol'] = $Total;
                            }

                            (new SaldoDet())->agregar($data);
                        }
                    } else if ($CodSunat == '07') {
                        $datos = $data;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['HaberSol'] = $Total;
                            $datos['HaberDol'] = $Total / $ValorTC;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['HaberSol'] = $Total * $ValorTC;
                            $datos['HaberDol'] = $Total;
                        }

                        $datos['IdMov'] = $IdMov_di;
                        $datos['NumItem'] = $NumItem_di++;
                        $datos['CodCuenta'] = $data_obs['Caja'];
                        $datos['CodDocumento'] = trim($array['CodDocRef'][$indice]);
                        $datos['SerieDoc'] = trim($array['SerieDocRef'][$indice]);
                        $datos['NumeroDoc'] = trim($array['NumDocRef'][$indice]);
                        $datos['RegistroSunat'] = 'NINGUNO';
                        $datos['CodCcosto'] = NULL;
                        $datos['CodCondPago'] = NULL;
                        $datos['DocRetencion'] = NULL;
                        $datos['DocDetraccion'] = NULL;
                        $datos['PorcRetencion'] = NULL;
                        $datos['PorcDetraccion'] = NULL;
                        $datos['FechaDetraccion'] = NULL;
                        $datos['CodTipoPago'] = NULL;
                        $datos['Parametro'] = NULL;
                        $datos['Monto'] = 0;
                        $datos['Saldo'] = 0;
                        $datos['CodTipoCliente'] = NULL;

                        $IdMovDetCaja = (new MovimientoDet())->agregar($datos);

                        $datos['NumItem'] = $NumItem_di++;

                        if ($moneda[0]['CodMoneda'] == 'MO001') {
                            $datos['CodCuenta'] = $data_obs['TotalS'];

                            $datos['DebeSol'] = $Total;
                            $datos['DebeDol'] = $Total / $ValorTC;
                            $datos['HaberSol'] = 0;
                            $datos['HaberDol'] = 0;
                        } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                            $datos['CodCuenta'] = $data_obs['TotalD'];

                            $datos['DebeSol'] = $Total * $ValorTC;
                            $datos['DebeDol'] = $Total;
                            $datos['HaberSol'] = 0;
                            $datos['HaberDol'] = 0;
                        }

                        $datos['CodDocumento'] = trim($array['CodDoc'][$indice]);
                        $datos['SerieDoc'] = trim($array['SerieDoc'][$indice]);
                        $datos['NumeroDoc'] = trim($array['NroDocDel'][$indice]);
                        $datos['Parametro'] = NULL;
                        $datos['TipoPC'] = NULL;

                        $IdMovDet = (new MovimientoDet())->agregar($datos);

                        if ((isset($IdMov_di) && !empty($IdMov_di)) &&
                            (isset($IdMovDet) && !empty($IdMovDet)) &&
                            (isset($IdMovDetRef) && !empty($IdMovDetRef))
                        ) {
                            $movimiento_det = (new MovimientoDet())->getMovimientoDet(
                                $this->CodEmpresa,
                                0,
                                0,
                                'IdMovDet, Saldo',
                                [],
                                [
                                    array('IdSocioN' => $IdSocioN, 'CodDocumento' => trim($array['CodDocRef'][$indice]), 'SerieDoc' => trim($array['SerieDocRef'][$indice]), 'NumeroDoc' => trim($array['NumDocRef'][$indice]))
                                ],
                                '',
                                ''
                            )[0];

                            $data = [
                                'CodEmpresa' => $this->CodEmpresa,
                                'IdMov' => $IdMov_di,
                                'IdMovDet' => $IdMovDetCaja,
                                'IdMovDetRef' => $movimiento_det['IdMovDet'],
                                'Periodo' => date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                                'Mes' => date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                                'Importado' => $idHistImp,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            if ($moneda[0]['CodMoneda'] == 'MO001') {
                                $data['TotalDetSol'] = $Total;
                                $data['TotalDetDol'] = $Total / $ValorTC;
                            } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                                $data['TotalDetSol'] = $Total * $ValorTC;
                                $data['TotalDetDol'] = $Total;
                            }

                            (new SaldoDet())->agregar($data);

                            (new MovimientoDet())->actualizar($this->CodEmpresa, 0, $movimiento_det['IdMovDet'], '', '', '', ['Saldo' => ($movimiento_det['Saldo'] - $Total)]);

                            $data = [
                                'CodEmpresa' => $this->CodEmpresa,
                                'IdMov' => $IdMov_di,
                                'IdMovDet' => $IdMovDet,
                                'IdMovDetRef' => $IdMovDetRef,
                                'Periodo' => date('Y', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                                'Mes' => date('m', strtotime(str_replace('/', '-', trim($array['Fecha'][$indice])))),
                                'Importado' => $idHistImp,
                                'CodDocRef' => NULL,
                                'SerieRef' => NULL,
                                'NumeroRef' => NULL,
                                'FechaRef' => NULL,
                                'FlagInterno' => 0
                            ];

                            if ($moneda[0]['CodMoneda'] == 'MO001') {
                                $data['TotalDetSol'] = $Total;
                                $data['TotalDetDol'] = $Total / $ValorTC;
                            } else if ($moneda[0]['CodMoneda'] == 'MO002') {
                                $data['TotalDetSol'] = $Total * $ValorTC;
                                $data['TotalDetDol'] = $Total;
                            }

                            (new SaldoDet())->agregar($data);

                            (new MovimientoDet())->actualizar($this->CodEmpresa, 0, $IdMovDetRef, '', '', '', ['Saldo' => ($Total * 2)]);
                        }
                    }
                }

                if ($this->db->transStatus() === FALSE) {
                    $this->db->transRollback();

                    $result = false;
                } else {
                    $this->db->transCommit();

                    $result = true;
                }

                if ($result) {
                    $resultado = array('estado' => true);
                } else {
                    $resultado = array('estado' => false);
                }

                echo json_encode($resultado);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
