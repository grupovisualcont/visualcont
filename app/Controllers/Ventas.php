<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\CondicionPago;
use App\Models\Documento;
use App\Models\Moneda;
use App\Models\MovimientoCab;
use App\Models\MovimientoDet;
use App\Models\SocioNegocio;
use App\Models\TipoCambio;
use App\Models\TipoDocumentoIdentidad;
use App\Models\TipoPersona;
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
                        ''
                    );

                    if (count($movimiento_cab_aplicacion) > 0) $movimiento_cab[$indice]['IdMovAplica'] = $movimiento_cab_aplicacion[0]['IdMov'];
                }

                $script = (new Empresa())->generar_script('', ['app/movements/sales/index.js']);

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
                $tipo_voucher_cab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, 'VEN', 0, '', [], '', '')[0];

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

                $documento = (new Documento())->getDocumento($this->CodEmpresa, 'BOL', 'VE', '', [ array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left') ], '', 'documento.DescDocumento ASC')[0];

                $tipo_dato = explode('|', $documento['TipoDatoS']);
                $longitud = isset($tipo_dato[2]) ? $tipo_dato[2] : '';
                $serie = isset($tipo_dato[3]) ? $tipo_dato[3] : '';
                $es_numero = empty($tipo_dato[4]) ? 'no' : 'si';

                $option_documento = '<option data-es-numero="' . $es_numero . '" data-serie="' . $serie . '" data-longitud="' . $longitud . '" value="' . $documento['CodDocumento'] . '">' . $documento['CodDocumento'] . ' - ' . $documento['DescDocumento'] . '</option>';

                $facturas = (new Documento())->getDocumento($this->CodEmpresa, '', 'VE', 'CodDocumento', [ ], 'CodSunat = "01"', 'DescDocumento ASC');

                $notas_credito = (new Documento())->getDocumento($this->CodEmpresa, '', 'VE', 'CodDocumento', [ ], 'CodSunat = "07"', 'DescDocumento ASC');

                $moneda = (new Moneda())->getMoneda('MO001', '', [], '', '')[0];

                $option_moneda = '<option value="' . $moneda['CodMoneda'] . '">' . $moneda['DescMoneda'] . '</option>';

                $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', '', [], 'Tipo = 168', '')[0];

                $option_condicion_pago_credito = '<option value="' . $condicion_pago['codcondpago'] . '">' . $condicion_pago['desccondpago'] . '</option>';

                $tipo_cambio = (new Empresa())->consulta_tipo_cambio();

                $tipo_operacion = (new Anexo())->getAnexo($this->CodEmpresa, 0, 5, '', '', [], 'CodInterno = 1', '')[0];

                $option_tipo_operacion = '<option value="' . $tipo_operacion['IdAnexo'] . '">' . $tipo_operacion['DescAnexo'] . '</option>';

                $tipo_persona = (new TipoPersona())->getTipoPersona('01', '', [], '', '')[0];

                $option_tipo_persona = '<option value="' . $tipo_persona['CodTipPer'] . '">' . $tipo_persona['DescPer'] . '</option>';

                $tipo_documento_identidad = (new TipoDocumentoIdentidad())->getTipoDocumentoIdentidad('1', '', [], '', '')[0];

                $option_tipo_documento_identidad = '<option data-tipo-dato="' . $tipo_documento_identidad['TipoDato'] . '" value="' . $tipo_documento_identidad['CodTipoDoc'] . '">' . $tipo_documento_identidad['DesDocumento'] . '</option>';

                $condicion = (new Anexo())->getAnexo($this->CodEmpresa, 0, 2, '', '', [], 'CodInterno = 0', '')[0];

                $option_condicion = '<option data-descripcion="' . $condicion['DescAnexo'] . '" value="' . $condicion['IdAnexo'] . '">' . $condicion['DescAnexo'] . '</option>';

                $forma_pago_contado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 6, '', '', [], 'CodInterno = 1', '')[0];

                $option_forma_pago_contado = '<option data-codigo-interno="' . $forma_pago_contado['CodInterno'] . '" value="' . $forma_pago_contado['IdAnexo'] . '">' . $forma_pago_contado['DescAnexo'] . '</option>';

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

                // $options_plan_contable = $this->options_plan_contable('', true, 'CodCuenta LIKE "104%" OR CodCuenta LIKE "105%" OR CodCuenta LIKE "106%"')['options'];

                // $options_tipo_pago = $this->options_tipo_pago('', true)['options'];

                $script = "
                    var Codmov = '" . $codigo_voucher_maximo . "';
                    var mes = '" . date('m') . "';
                    var datos_ruc_CodTipPer = '" . $datos_ruc['CodTipPer'] . "';
                    var datos_ruc_CodTipoDoc = '" . $datos_ruc['CodTipoDoc'] . "';
                    var datos_ruc_N_tip = '" . $datos_ruc['N_tip'] . "';
                    var datos_extranjero_CodTipPer = '" . $datos_extranjero['CodTipPer'] . "';
                    var datos_extranjero_CodTipoDoc = '" . $datos_extranjero['CodTipoDoc'] . "';
                    var Referencia = 0;
                    var options_forma_pago_contado = '" . $option_forma_pago_contado . "';
                    var options_condicion_pago_credito = '" . $option_condicion_pago_credito . "';
                    var options_forma_pago_credito = '';
                    var facturas = JSON.parse('" . json_encode($facturas) . "');
                    var notas_debito_credito = JSON.parse('" . json_encode($notas_credito) . "');
                ";

                $script = (new Empresa())->generar_script($script, ['app/movements/sales/create.js']);

                return viewApp($this->page, 'app/movements/sales/create2', [
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
                    array('tabla' => 'socionegocio sn', 'on' => 'sn.IdSocioN = movimientodet.IdSocioN AND sn.CodEmpresa = movimientodet.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'moneda mo', 'on' => 'mo.CodMoneda = movimientodet.CodMoneda', 'tipo' => 'inner'),
                ],
                [
                    array('Parametro' => 'TOTAL')
                ],
                '',
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
                        <td>' . $valor['NumItem'] . '</td>
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
                        <td>' . number_format($valor['PorcRetencion'], 2, '.', ',') . '</td>
                        <td>' . number_format($valor['PorcDetraccion'], 2, '.', ',') . '</td>
                        <td>' . $valor['FechaDetraccion'] . '</td>
                        <td>' . $valor['IdTipOpeDetra'] . '</td>
                        <td>' . $valor['IdenContProy'] . '</td>
                        <td>' . $valor['Declarar_Per'] . '</td>
                        <td>' . $valor['Declarar_Est'] . '</td>
                        <td>' . $valor['IdActivo'] . '</td>
                        <td>' . $valor['IdOperacionAF'] . '</td>
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
                $FecEmision = date('Y-m-d', strtotime(strval($this->request->getPost('FecEmision'))));

                $tipo_cambio = (new TipoCambio())->getTipoCambio($this->CodEmpresa, $FecEmision, 'ValorVenta', [], '', '');

                $ValorTC = 1.000;

                if (count($tipo_cambio) > 0) $ValorTC = $tipo_cambio[0]['ValorVenta'];

                echo $ValorTC;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
