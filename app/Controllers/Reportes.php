<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Empresa AS ModelsEmpresa;
use App\Models\Moneda;
use App\Models\MovimientoCab;
use App\Models\SocioNegocio;

class Reportes extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Reportes';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $moneda = (new Moneda())->getMoneda('', '', [], '', '');

                $options_moneda_1 = '';

                foreach ($moneda as $indice => $valor) {
                    $checked = '';

                    if ($indice == 0) $checked = 'checked';

                    $options_moneda_1 .= '
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="moneda" id="moneda' . $indice . '1" value="' . $valor['CodMoneda'] . '" ' . $checked . '>
                            <label class="form-check-label" for="moneda' . $indice . '1">' . strtoupper($valor['DescMoneda'][0]) . strtolower(substr($valor['DescMoneda'], 1)) . '</label>
                        </div>
                    ';
                }

                $options_moneda_2 = '';

                foreach ($moneda as $indice => $valor) {
                    $checked = '';

                    if ($indice == 0) $checked = 'checked';

                    $options_moneda_2 .= '
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="moneda" id="moneda' . $indice . '2" value="' . $valor['CodMoneda'] . '" ' . $checked . '>
                            <label class="form-check-label" for="moneda' . $indice . '2">' . strtoupper($valor['DescMoneda'][0]) . strtolower(substr($valor['DescMoneda'], 1)) . '</label>
                        </div>
                    ';
                }

                $options_moneda_3 = '';

                foreach ($moneda as $indice => $valor) {
                    $checked = '';

                    if ($indice == 0) $checked = 'checked';

                    $options_moneda_3 .= '
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="moneda" id="moneda' . $indice . '3" value="' . $valor['CodMoneda'] . '" ' . $checked . '>
                            <label class="form-check-label" for="moneda' . $indice . '3">' . strtoupper($valor['DescMoneda'][0]) . strtolower(substr($valor['DescMoneda'], 1)) . '</label>
                        </div>
                    ';
                }

                $periodo = array('M' => 'Mensual', 'A' => 'Acumulado');

                $options_periodo_1 = '';

                foreach ($periodo as $indice => $valor) {
                    $checked = '';

                    if ($indice == 'M') $checked = 'checked';

                    $options_periodo_1 .= '
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="periodo" id="periodo' . $indice . '1" value="' . $indice . '" ' . $checked . '>
                            <label class="form-check-label" for="periodo' . $indice . '1">' . $valor . '</label>
                        </div>
                    ';
                }

                $options_periodo_2 = '';

                foreach ($periodo as $indice => $valor) {
                    $checked = '';

                    if ($indice == 'M') $checked = 'checked';

                    $options_periodo_2 .= '
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="periodo" id="periodo' . $indice . '2" value="' . $indice . '" ' . $checked . '>
                            <label class="form-check-label" for="periodo' . $indice . '2">' . $valor . '</label>
                        </div>
                    ';
                }

                $options_periodo_3 = '';

                foreach ($periodo as $indice => $valor) {
                    $checked = '';

                    if ($indice == 'M') $checked = 'checked';

                    $options_periodo_3 .= '
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="periodo" id="periodo' . $indice . '3" value="' . $indice . '" ' . $checked . '>
                            <label class="form-check-label" for="periodo' . $indice . '3">' . $valor . '</label>
                        </div>
                    ';
                }

                $ordenar = array('SerieDoc' => 'Serie', 'NumeroDoc' => 'Numero', 'FecEmision' => 'FecEmision', 'CodTV' => 'Voucher', 'IdSocioN' => 'Cliente');

                $options_ordernar_1 = '';

                foreach ($ordenar as $indice => $valor) {
                    $selected = '';

                    if ($indice == 'SerieDoc') $selected = 'selected';

                    $options_ordernar_1 .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $options_ordernar_2 = '';

                foreach ($ordenar as $indice => $valor) {
                    $selected = '';

                    if ($indice == 'NumeroDoc') $selected = 'selected';

                    $options_ordernar_2 .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $options_ordernar_3 = '';

                foreach ($ordenar as $indice => $valor) {
                    $selected = '';

                    if ($indice == 'FecEmision') $selected = 'selected';

                    $options_ordernar_3 .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $meses = $this->meses();

                $options_mes = '';

                foreach ($meses as $indice => $valor) {
                    $selected = '';

                    if ($indice == date('m')) $selected = 'selected';

                    $options_mes .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $script = (new Empresa())->generar_script(['app/reports/sales/index.js']);

                return viewApp($this->page, 'app/reports/sales/index', [
                    'options_moneda_1' => $options_moneda_1,
                    'options_moneda_2' => $options_moneda_2,
                    'options_moneda_3' => $options_moneda_3,
                    'options_periodo_1' => $options_periodo_1,
                    'options_periodo_2' => $options_periodo_2,
                    'options_periodo_3' => $options_periodo_3,
                    'options_ordernar_1' => $options_ordernar_1,
                    'options_ordernar_2' => $options_ordernar_2,
                    'options_ordernar_3' => $options_ordernar_3,
                    'options_mes' => $options_mes,
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

    public function registro_ventas_pdf()
    {
        try {
            $post = $this->request->getPost();

            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $html = '
            <table>
                <tr>
                    <td><b>PERIODO: ' . date('Y') . '</b></td>
                    <td align="right"><b> ' . date('d/m/Y') . '</b></td>
                </tr>
                <tr>
                    <td><b>RUC: ' . $empresa['Ruc'] . '</b></td>
                </tr>
                <tr>
                    <td><b>' . $empresa['RazonSocial'] . '</td>
                </tr>
            </table>
            <br>
            <table>
                <tr>
                    <td align="center"><h3><b>REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y') . '</b></h3></td>
                </tr>
                <tr>
                    <td align="center"><h3>(Expresado en ' . $moneda . ')</h3></td>
                </tr>
            </table>
            <br>
            <br>
        ';

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $descuento = 'det.DescuentoS';
                $anticipo = 'det.AnticipoS';
                $inafecto = 'det.InafectoS';
                $igv = 'det.IGVSunatS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $descuento = 'det.DescuentoD';
                $anticipo = 'det.AnticipoD';
                $inafecto = 'det.InafectoD';
                $igv = 'det.IGVSunatD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    movimientocab.FecContable,
                    det.CodMoneda,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    do.CodSunat,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(BaseImpSunatD, 2) AS afectoD,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $descuento . ', 2) AS descuento,
                    ROUND(' . $anticipo . ', 2) AS anticipo,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_descuento = 0;
            $totales_anticipo = 0;
            $totales_inafecto = 0;
            $totales_igv = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_descuento = 0;
            $total_anticipo = 0;
            $total_inafecto = 0;
            $total_igv = 0;
            $total = 0;

            $tr = '';

            $afectoD = '';

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['total'] = $valor['afecto'] + $valor['descuento'] + $valor['anticipo'] + $valor['inafecto'] + $valor['igv'];

                $totales_afecto += $valor['afecto'];
                $totales_descuento += $valor['descuento'];
                $totales_anticipo += $valor['anticipo'];
                $totales_inafecto += $valor['inafecto'];
                $totales_igv += $valor['igv'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_descuento += $valor['descuento'];
                $total_anticipo += $valor['anticipo'];
                $total_inafecto += $valor['inafecto'];
                $total_igv += $valor['igv'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $tr .= '
                    <tr>
                        <td><h4>' . $valor['CodSunat'] . '<h4></td>
                    </tr>
                ';
                }

                if ($CodMoneda == 'MO002') {
                    $afectoD = '<td align="right">' . number_format($valor['afectoD'], 2, '.', '') . '</td>';
                } else {
                    $afectoD = '';
                }

                $tr .= '
                <tr>
                    <td>' . date('d/m/Y', strtotime($valor['FecContable'])) . '</td>
                    <td>' . $valor['CodSunat'] . '</td>
                    <td>' . $valor['SerieDoc'] . '</td>
                    <td>' . $valor['ruc'] . '</td>
                    <td class="nowrap">' . $valor['razonsocial'] . '</td>
                    ' . $afectoD . '
                    <td align="right">' . number_format($valor['afecto'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['descuento'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['anticipo'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['inafecto'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['igv'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['total'], 2, '.', ',') . '</td>
                    <td>' . $valor['Codmov'] . '</td>
                <tr>
            ';

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    if ($CodMoneda == 'MO002') {
                        $td_afectoD = '<td class="border-top"></td>';
                    } else {
                        $td_afectoD = '';
                    }

                    $tr .= '
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="2" align="right">TOTAL GRUPO:</td>
                        ' . $td_afectoD . '
                        <td class="border-top" align="right">' . number_format($total_afecto, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_descuento, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_anticipo, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_inafecto, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_igv, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total, 2, '.', ',') . '</td>
                    </tr>
                ';

                    $total_afecto = 0;
                    $total_descuento = 0;
                    $total_anticipo = 0;
                    $total_inafecto = 0;
                    $total_igv = 0;
                    $total = 0;
                }
            }

            $columnas_ImpDol = array('Fecha', 'TD', 'Serie - Número', 'RUC/DNI', 'Razón Social', 'Imp.Dol', 'Afecto', 'Dscto.', 'Anticipo', 'Inafecto', 'I.G.V.', 'Total', 'Vouchers');

            $columnas = array('Fecha', 'TD', 'Serie - Número', 'RUC/DNI', 'Razón Social', 'Afecto', 'Dscto.', 'Anticipo', 'Inafecto', 'I.G.V.', 'Total', 'Vouchers');

            $tr_columnas = '<tr>';

            foreach (strlen($afectoD) > 0 ? $columnas_ImpDol : $columnas as $indice => $valor) {
                $tr_columnas .= '<th>' . $valor . '</th>';
            }

            $tr_columnas .= '</tr>';

            $tr = $tr_columnas . $tr;

            if ($CodMoneda == 'MO002') {
                $td_afectoD = '<td></td>';
            } else {
                $td_afectoD = '';
            }

            $tr .= '
            <tr>
                <td colspan="3"></td>
                <td colspan="2" align="right">TOTAL GENERAL:</td>
                ' . $td_afectoD . '
                <td align="right">' . number_format($totales_afecto, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_descuento, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_anticipo, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_inafecto, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_igv, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales, 2, '.', ',') . '</td>
            </tr>
            <tr>
                <td colspan="5"></td>
                <td class="border-general" colspan="7"></td>
            <tr>
        ';

            $pdf = new PDF();

            $pdf->setFilename('registro_ventas_reporte');
            $pdf->creacion('registro_ventas_reporte', $tr, $html, 'A2', false);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_excel()
    {
        try {
            $post = $this->request->getPost();

            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $excel = new Excel();

            $excel->creacion(str_split('REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'), 31)[0]);

            $columnas = array('Fecha', 'TD', 'Serie - Número', 'RUC/DNI', 'Razón Social', 'Imp.Dol', 'Afecto', 'Dscto.', 'Anticipo', 'Inafecto', 'I.G.V.', 'Total', 'Vouchers');

            $excel->setValues($columnas);

            $excel->body(8, 'columnas');

            $excel->setCelda('A1', 'PERIODO: ' .  date('Y'));
            $excel->setBold('A1');
            $excel->setCelda('A2', 'RUC: ' . $empresa['Ruc']);
            $excel->setBold('A2');
            $excel->setCelda('A3', $empresa['RazonSocial']);
            $excel->setBold('A3');
            $excel->setCelda($excel->getLetra(count($columnas) - 1) . '1', date('d/m/Y'));
            $excel->setBold($excel->getLetra(count($columnas) - 1) . '1');
            $excel->combinarCelda('A5:' . $excel->getLetra(count($columnas) - 1) . '5');
            $excel->setCelda('A5', 'REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'));
            $excel->setBold('A5');
            $excel->setFontSize('A5', 16);
            $excel->combinarCelda('A6:' . $excel->getLetra(count($columnas) - 1) . '6');
            $excel->setCelda('A6', '(Expresado en ' . $moneda . ')');
            $excel->setBold('A6');
            $excel->setFontSize('A6', 14);

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $descuento = 'det.DescuentoS';
                $anticipo = 'det.AnticipoS';
                $inafecto = 'det.InafectoS';
                $igv = 'det.IGVSunatS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $descuento = 'det.DescuentoD';
                $anticipo = 'det.AnticipoD';
                $inafecto = 'det.InafectoD';
                $igv = 'det.IGVSunatD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    movimientocab.FecContable,
                    det.CodMoneda,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    do.CodSunat,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(BaseImpSunatD, 2) AS afectoD,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $descuento . ', 2) AS descuento,
                    ROUND(' . $anticipo . ', 2) AS anticipo,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_descuento = 0;
            $totales_anticipo = 0;
            $totales_inafecto = 0;
            $totales_igv = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_descuento = 0;
            $total_anticipo = 0;
            $total_inafecto = 0;
            $total_igv = 0;
            $total = 0;

            $index = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['total'] = $valor['afecto'] + $valor['descuento'] + $valor['anticipo'] + $valor['inafecto'] + $valor['igv'];

                $totales_afecto += $valor['afecto'];
                $totales_descuento += $valor['descuento'];
                $totales_anticipo += $valor['anticipo'];
                $totales_inafecto += $valor['inafecto'];
                $totales_igv += $valor['igv'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_descuento += $valor['descuento'];
                $total_anticipo += $valor['anticipo'];
                $total_inafecto += $valor['inafecto'];
                $total_igv += $valor['igv'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $values = array(
                        array('value' => $valor['CodSunat'], 'style' => 'bold')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');
                }

                $values = array(
                    date('d/m/Y', strtotime($valor['FecContable'])),
                    $valor['CodSunat'],
                    $valor['SerieDoc'],
                    $valor['ruc'],
                    $valor['razonsocial'],
                    $valor['CodMoneda'] == 'MO002' ? number_format($valor['afectoD'], 2, '.', ',') : '',
                    number_format($valor['afecto'], 2, '.', ','),
                    number_format($valor['descuento'], 2, '.', ','),
                    number_format($valor['anticipo'], 2, '.', ','),
                    number_format($valor['inafecto'], 2, '.', ','),
                    number_format($valor['igv'], 2, '.', ','),
                    number_format($valor['total'], 2, '.', ','),
                    $valor['Codmov']
                );

                $index++;

                $excel->setValues($values);

                $excel->body(8 + $index, 'valor');

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    $values = array(
                        '',
                        '',
                        '',
                        '',
                        array('value' => 'TOTAL GRUPO', 'style' => 'bold||rigth'),
                        '',
                        number_format($total_afecto, 2, '.', ','),
                        number_format($total_descuento, 2, '.', ','),
                        number_format($total_anticipo, 2, '.', ','),
                        number_format($total_inafecto, 2, '.', ','),
                        number_format($total_igv, 2, '.', ','),
                        number_format($total, 2, '.', ',')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');

                    $total_afecto = 0;
                    $total_descuento = 0;
                    $total_anticipo = 0;
                    $total_inafecto = 0;
                    $total_igv = 0;
                    $total = 0;
                }
            }

            $values = array(
                '',
                '',
                '',
                '',
                array('value' => 'TOTAL GENERAL', 'style' => 'bold||rigth'),
                '',
                number_format($totales_afecto, 2, '.', ','),
                number_format($totales_descuento, 2, '.', ','),
                number_format($totales_anticipo, 2, '.', ','),
                number_format($totales_inafecto, 2, '.', ','),
                number_format($totales_igv, 2, '.', ','),
                number_format($totales, 2, '.', ',')
            );

            $index = $index + 2;

            $excel->setValues($values);

            $excel->body(8 + $index, 'valor');

            $excel->footer('registro_ventas_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_sunat_pdf()
    {
        try {
            $post = $this->request->getPost();

            $serie = trim($post['serie']);
            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];
            $mes_anio = strlen($post['mes']) == 1 ? '0' . $post['mes'] . '/' . date('Y') : $post['mes'] . '/' . date('Y');

            if (!empty($serie)) $serie = ' AND det.SerieDoc = ' . $serie;

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $html = '
            <table>
                <tr>
                    <td><b>' . $empresa['RazonSocial'] . '</td>
                    <td align="right"><b> ' . date('d/m/Y') . '</b></td>
                </tr>
                <tr>
                    <td><b>RUC: ' . $empresa['Ruc'] . '</b></td>
                </tr>
                <tr>
                    <td><b>PERIODO: ' . $mes_anio . '</b></td>
                </tr>
                <tr>
                    <td><b>LIMA</b></td>
                </tr>
            </table>
            <br>
            <table>
                <tr>
                    <td align="center"><h3><b>REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y') . '</b></h3></td>
                </tr>
                <tr>
                    <td align="center"><h3>(Expresado en ' . $moneda . ')</h3></td>
                </tr>
            </table>
            <br>
            <br>
        ';

            $columnas = array('VOUCHER<br>NRO.', 'FECHA<br>EMIS.', 'FECHA<br>VCTO.', 'TP<br>DC', 'SERIE - NÚMERO<br>COMPROB PAGO', 'DI', 'NUMERO<br>RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'B.IMP.<br>Exportación', 'B.IMP.<br>O.Gravadas', 'IMPORTE<br>EXONER.<br>INAFECTO', 'I.S.C', 'I.G.V.<br>o<br>IPM', 'ICBP', 'OTROS<br>TRIBUTOS', 'IMPORTE<br>TOTAL', 'T/C', 'FECHA', 'TP<br>DC', 'DOCUMENTO<br>MODIFICADO');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $exonerado = 'det.ExoneradoS';
                $inafecto = 'det.InafectoS';
                $isc = 'det.ISCS';
                $igv = 'det.IGVSunatS';
                $icbp = 'det.IcbpS';
                $otros_tributos = 'det.OtroTributoS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $exonerado = 'det.ExoneradoD';
                $inafecto = 'det.InafectoD';
                $isc = 'det.ISCD';
                $igv = 'det.IGVSunatD';
                $icbp = 'det.IcbpD';
                $otros_tributos = 'det.OtroTributoD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    det.FecEmision,
                    det.FecVcto,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    det.ValorTC,
                    do.CodSunat,
                    socionegocio.CodTipoDoc,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $exonerado . ', 2) AS exonerado,
                    ROUND(' . $isc . ', 2) AS isc,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $icbp . ', 2) AS icbp,
                    ROUND(' . $otros_tributos . ', 2) AS otros_tributos,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_inafecto = 0;
            $totales_isc = 0;
            $totales_igv = 0;
            $totales_icbp = 0;
            $totales_otros_tributos = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_inafecto = 0;
            $total_isc = 0;
            $total_igv = 0;
            $total_icbp = 0;
            $total_otros_tributos = 0;
            $total = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['inafecto'] = $valor['exonerado'] != 0 ? $valor['exonerado'] : $valor['inafecto'];

                $valor['total'] = $valor['afecto'] + $valor['inafecto'] + $valor['isc'] + $valor['igv'] + $valor['icbp'] + $valor['otros_tributos'];

                $totales_afecto += $valor['afecto'];
                $totales_inafecto += $valor['inafecto'];
                $totales_isc += $valor['isc'];
                $totales_igv += $valor['igv'];
                $totales_icbp += $valor['icbp'];
                $totales_otros_tributos += $valor['otros_tributos'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_inafecto += $valor['inafecto'];
                $total_isc += $valor['isc'];
                $total_igv += $valor['igv'];
                $total_icbp += $valor['igv'];
                $total_otros_tributos += $valor['otros_tributos'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $tr .= '
                    <tr>
                        <td><h4>' . $valor['CodSunat'] . '<h4></td>
                    </tr>
                ';
                }

                $tr .= '
                <tr>
                    <td>' . $valor['Codmov'] . '</td>
                    <td>' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($valor['FecVcto'])) . '</td>
                    <td>' . $valor['CodSunat'] . '</td>
                    <td>' . $valor['SerieDoc'] . '</td>
                    <td>' . $valor['CodTipoDoc'] . '</td>
                    <td>' . $valor['ruc'] . '</td>
                    <td class="nowrap">' . $valor['razonsocial'] . '</td>
                    <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['afecto'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['inafecto'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['isc'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['igv'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['icbp'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['otros_tributos'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['total'], 2, '.', ',') . '</td>
                    <td>' . $valor['ValorTC'] . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                <tr>
            ';

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    $tr .= '
                    <tr>
                        <td colspan="8"></td>
                        <td class="border-top" align="right">' . number_format(0, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_afecto, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_inafecto, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_isc, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_igv, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_icbp, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_otros_tributos, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total, 2, '.', ',') . '</td>
                    </tr>
                ';

                    $total_afecto = 0;
                    $total_inafecto = 0;
                    $total_isc = 0;
                    $total_igv = 0;
                    $total_icbp = 0;
                    $total_otros_tributos = 0;
                    $total = 0;
                }
            }

            $tr .= '
            <tr>
                <td colspan="8"></td>
                <td class="border-bottom" colspan="8"></td>
            </tr>
            <tr>
                <td colspan="6"></td>
                <td colspan="2" align="right">TOTAL GENERAL:</td>
                <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_afecto, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_inafecto, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_isc, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_igv, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_icbp, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_otros_tributos, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales, 2, '.', ',') . '</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td class="border-general" colspan="8"></td>
            <tr>
        ';

            $pdf = new PDF();

            $pdf->setFilename('registro_ventas_sunat_reporte');
            $pdf->creacion('registro_ventas_sunat_reporte', $tr, $html, 'A2', false);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_sunat_excel()
    {
        try {
            $post = $this->request->getPost();

            $serie = trim($post['serie']);
            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];
            $mes_anio = strlen($post['mes']) == 1 ? '0' . $post['mes'] . '/' . date('Y') : $post['mes'] . '/' . date('Y');

            if (!empty($serie)) $serie = ' AND det.SerieDoc = ' . $serie;

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $excel = new Excel();

            $excel->creacion(str_split('REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'), 31)[0]);

            $columnas = array('VOUCHER NRO.', 'FECHA EMIS.', 'FECHA VCTO.', 'TP DC', 'SERIE - NÚMERO COMPROB PAGO', 'DI', 'NUMERO RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'B.IMP. Exportación', 'B.IMP. O.Gravadas', 'IMPORTE EXONER. INAFECTO', 'I.S.C', 'I.G.V. o IPM', 'ICBP', 'OTROS TRIBUTOS', 'IMPORTE TOTAL', 'T/C', 'FECHA', 'TP DC', 'DOCUMENTO MODIFICADO');

            $excel->setValues($columnas);

            $excel->body(8, 'columnas');

            $excel->setCelda('A1', 'PERIODO: ' .  $mes_anio);
            $excel->setBold('A1');
            $excel->setCelda('A2', 'RUC: ' . $empresa['Ruc']);
            $excel->setBold('A2');
            $excel->setCelda('A3', $empresa['RazonSocial']);
            $excel->setBold('A3');
            $excel->setCelda($excel->getLetra(count($columnas) - 1) . '1', date('d/m/Y'));
            $excel->setBold($excel->getLetra(count($columnas) - 1) . '1');
            $excel->combinarCelda('A5:' . $excel->getLetra(count($columnas) - 1) . '5');
            $excel->setCelda('A5', 'REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'));
            $excel->setBold('A5');
            $excel->setFontSize('A5', 16);
            $excel->combinarCelda('A6:' . $excel->getLetra(count($columnas) - 1) . '6');
            $excel->setCelda('A6', '(Expresado en ' . $moneda . ')');
            $excel->setBold('A6');
            $excel->setFontSize('A6', 14);

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $exonerado = 'det.ExoneradoS';
                $inafecto = 'det.InafectoS';
                $isc = 'det.ISCS';
                $igv = 'det.IGVSunatS';
                $icbp = 'det.IcbpS';
                $otros_tributos = 'det.OtroTributoS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $exonerado = 'det.ExoneradoD';
                $inafecto = 'det.InafectoD';
                $isc = 'det.ISCD';
                $igv = 'det.IGVSunatD';
                $icbp = 'det.IcbpD';
                $otros_tributos = 'det.OtroTributoD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    det.FecEmision,
                    det.FecVcto,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    det.ValorTC,
                    do.CodSunat,
                    socionegocio.CodTipoDoc,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $exonerado . ', 2) AS exonerado,
                    ROUND(' . $isc . ', 2) AS isc,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $icbp . ', 2) AS icbp,
                    ROUND(' . $otros_tributos . ', 2) AS otros_tributos,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_inafecto = 0;
            $totales_isc = 0;
            $totales_igv = 0;
            $totales_icbp = 0;
            $totales_otros_tributos = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_inafecto = 0;
            $total_isc = 0;
            $total_igv = 0;
            $total_icbp = 0;
            $total_otros_tributos = 0;
            $total = 0;

            $index = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['inafecto'] = $valor['exonerado'] != 0 ? $valor['exonerado'] : $valor['inafecto'];

                $valor['total'] = $valor['afecto'] + $valor['inafecto'] + $valor['isc'] + $valor['igv'] + $valor['icbp'] + $valor['otros_tributos'];

                $totales_afecto += $valor['afecto'];
                $totales_inafecto += $valor['inafecto'];
                $totales_isc += $valor['isc'];
                $totales_igv += $valor['igv'];
                $totales_icbp += $valor['icbp'];
                $totales_otros_tributos += $valor['otros_tributos'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_inafecto += $valor['inafecto'];
                $total_isc += $valor['isc'];
                $total_igv += $valor['igv'];
                $total_icbp += $valor['igv'];
                $total_otros_tributos += $valor['otros_tributos'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $values = array(
                        array('value' => $valor['CodSunat'], 'style' => 'bold')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');
                }

                $values = array(
                    $valor['Codmov'],
                    date('d/m/Y', strtotime($valor['FecEmision'])),
                    date('d/m/Y', strtotime($valor['FecVcto'])),
                    $valor['CodSunat'],
                    $valor['SerieDoc'],
                    $valor['CodTipoDoc'],
                    $valor['ruc'],
                    $valor['razonsocial'],
                    number_format(0, 2, '.', ','),
                    number_format($valor['afecto'], 2, '.', ','),
                    number_format($valor['inafecto'], 2, '.', ','),
                    number_format($valor['isc'], 2, '.', ','),
                    number_format($valor['igv'], 2, '.', ','),
                    number_format($valor['icbp'], 2, '.', ','),
                    number_format($valor['otros_tributos'], 2, '.', ','),
                    number_format($valor['total'], 2, '.', ','),
                    $valor['ValorTC']
                );

                $index++;

                $excel->setValues($values);

                $excel->body(8 + $index, 'valor');

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    $values = array(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        array('value' => 'TOTAL GRUPO', 'style' => 'bold||rigth'),
                        number_format(0, 2, '.', ','),
                        number_format($total_afecto, 2, '.', ','),
                        number_format($total_inafecto, 2, '.', ','),
                        number_format($total_isc, 2, '.', ','),
                        number_format($total_igv, 2, '.', ','),
                        number_format($total_icbp, 2, '.', ','),
                        number_format($total_otros_tributos, 2, '.', ','),
                        number_format($total, 2, '.', ',')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');

                    $total_afecto = 0;
                    $total_inafecto = 0;
                    $total_isc = 0;
                    $total_igv = 0;
                    $total_icbp = 0;
                    $total_otros_tributos = 0;
                    $total = 0;
                }
            }

            $values = array(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                array('value' => 'TOTAL GENERAL', 'style' => 'bold||rigth'),
                number_format(0, 2, '.', ','),
                number_format($totales_afecto, 2, '.', ','),
                number_format($totales_inafecto, 2, '.', ','),
                number_format($totales_isc, 2, '.', ','),
                number_format($totales_igv, 2, '.', ','),
                number_format($totales_icbp, 2, '.', ','),
                number_format($totales_otros_tributos, 2, '.', ','),
                number_format($totales, 2, '.', ',')
            );

            $index = $index + 2;

            $excel->setValues($values);

            $excel->body(8 + $index, 'valor');

            $excel->footer('registro_ventas_sunat_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_sunat_formato_14_1_pdf()
    {
        try {
            $post = $this->request->getPost();

            $serie = trim($post['serie']);
            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];
            $mes_anio = strlen($post['mes']) == 1 ? '0' . $post['mes'] . '/' . date('Y') : $post['mes'] . '/' . date('Y');

            if (!empty($serie)) $serie = ' AND det.SerieDoc = ' . $serie;

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $html = '
            <table>
                <tr>
                    <td><b>' . $empresa['RazonSocial'] . '</td>
                    <td align="right"><b> ' . date('d/m/Y') . '</b></td>
                </tr>
                <tr>
                    <td><b>RUC: ' . $empresa['Ruc'] . '</b></td>
                </tr>
                <tr>
                    <td><b>PERIODO: ' . $mes_anio . '</b></td>
                </tr>
                <tr>
                    <td><b>LIMA</b></td>
                </tr>
            </table>
            <br>
            <table>
                <tr>
                    <td align="center"><h3><b>REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y') . '</b></h3></td>
                </tr>
                <tr>
                    <td align="center"><h3>(Expresado en ' . $moneda . ')</h3></td>
                </tr>
            </table>
            <br>
            <br>
        ';

            $columnas = array('VOUCHER<br>NRO.', 'FECHA<br>EMIS.', 'FECHA<br>VCTO.', 'TP<br>DC', 'SERIE - NÚMERO<br>COMPROB PAGO', 'DI', 'NUMERO<br>RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'B.IMP.<br>Exportación', 'B.IMP.<br>O.Gravadas', 'IMPORTE<br>EXONER.<br>INAFECTO', 'I.S.C', 'I.G.V.<br>o<br>IPM', 'ICBP', 'OTROS<br>TRIBUTOS', 'IMPORTE<br>TOTAL', 'T/C', 'FECHA', 'TP<br>DC', 'DOCUMENTO<br>MODIFICADO');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $exonerado = 'det.ExoneradoS';
                $inafecto = 'det.InafectoS';
                $isc = 'det.ISCS';
                $igv = 'det.IGVSunatS';
                $icbp = 'det.IcbpS';
                $otros_tributos = 'det.OtroTributoS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $exonerado = 'det.ExoneradoD';
                $inafecto = 'det.InafectoD';
                $isc = 'det.ISCD';
                $igv = 'det.IGVSunatD';
                $icbp = 'det.IcbpD';
                $otros_tributos = 'det.OtroTributoD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    det.FecEmision,
                    det.FecVcto,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    det.ValorTC,
                    do.CodSunat,
                    socionegocio.CodTipoDoc,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $exonerado . ', 2) AS exonerado,
                    ROUND(' . $isc . ', 2) AS isc,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $icbp . ', 2) AS icbp,
                    ROUND(' . $otros_tributos . ', 2) AS otros_tributos,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_inafecto = 0;
            $totales_isc = 0;
            $totales_igv = 0;
            $totales_icbp = 0;
            $totales_otros_tributos = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_inafecto = 0;
            $total_isc = 0;
            $total_igv = 0;
            $total_icbp = 0;
            $total_otros_tributos = 0;
            $total = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['inafecto'] = $valor['exonerado'] != 0 ? $valor['exonerado'] : $valor['inafecto'];

                $valor['total'] = $valor['afecto'] + $valor['inafecto'] + $valor['isc'] + $valor['igv'] + $valor['icbp'] + $valor['otros_tributos'];

                $totales_afecto += $valor['afecto'];
                $totales_inafecto += $valor['inafecto'];
                $totales_isc += $valor['isc'];
                $totales_igv += $valor['igv'];
                $totales_icbp += $valor['icbp'];
                $totales_otros_tributos += $valor['otros_tributos'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_inafecto += $valor['inafecto'];
                $total_isc += $valor['isc'];
                $total_igv += $valor['igv'];
                $total_icbp += $valor['igv'];
                $total_otros_tributos += $valor['otros_tributos'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $tr .= '
                    <tr>
                        <td><h4>' . $valor['CodSunat'] . '<h4></td>
                    </tr>
                ';
                }

                $tr .= '
                <tr>
                    <td>' . $valor['Codmov'] . '</td>
                    <td>' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                    <td>' . date('d/m/Y', strtotime($valor['FecVcto'])) . '</td>
                    <td>' . $valor['CodSunat'] . '</td>
                    <td>' . $valor['SerieDoc'] . '</td>
                    <td>' . $valor['CodTipoDoc'] . '</td>
                    <td>' . $valor['ruc'] . '</td>
                    <td class="nowrap">' . $valor['razonsocial'] . '</td>
                    <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['afecto'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['inafecto'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['isc'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['igv'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['icbp'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['otros_tributos'], 2, '.', ',') . '</td>
                    <td align="right">' . number_format($valor['total'], 2, '.', ',') . '</td>
                    <td>' . $valor['ValorTC'] . '</td>
                    <td></td>
                    <td></td>
                    <td></td>
                <tr>
            ';

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    $tr .= '
                    <tr>
                        <td colspan="8"></td>
                        <td class="border-top" align="right">' . number_format(0, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_afecto, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_inafecto, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_isc, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_igv, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_icbp, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total_otros_tributos, 2, '.', ',') . '</td>
                        <td class="border-top" align="right">' . number_format($total, 2, '.', ',') . '</td>
                    </tr>
                ';

                    $total_afecto = 0;
                    $total_inafecto = 0;
                    $total_isc = 0;
                    $total_igv = 0;
                    $total_icbp = 0;
                    $total_otros_tributos = 0;
                    $total = 0;
                }
            }

            $tr .= '
            <tr>
                <td colspan="8"></td>
                <td class="border-bottom" colspan="8"></td>
            </tr>
            <tr>
                <td colspan="6"></td>
                <td colspan="2" align="right">TOTAL GENERAL:</td>
                <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_afecto, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_inafecto, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_isc, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_igv, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_icbp, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales_otros_tributos, 2, '.', ',') . '</td>
                <td align="right">' . number_format($totales, 2, '.', ',') . '</td>
            </tr>
            <tr>
                <td colspan="8"></td>
                <td class="border-general" colspan="8"></td>
            <tr>
        ';

            $pdf = new PDF();

            $pdf->setFilename('registro_ventas_sunat_formato_14_1_reporte');
            $pdf->creacion('registro_ventas_sunat_formato_14_1_reporte', $tr, $html, 'A2', false);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_sunat_formato_14_1_excel()
    {
        try {
            $post = $this->request->getPost();

            $serie = trim($post['serie']);
            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];
            $mes_anio = strlen($post['mes']) == 1 ? '0' . $post['mes'] . '/' . date('Y') : $post['mes'] . '/' . date('Y');

            if (!empty($serie)) $serie = ' AND det.SerieDoc = ' . $serie;

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $excel = new Excel();

            $excel->creacion(str_split('REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'), 31)[0]);

            $columnas = array('VOUCHER NRO.', 'FECHA EMIS.', 'FECHA VCTO.', 'TP DC', 'SERIE - NÚMERO COMPROB PAGO', 'DI', 'NUMERO RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'B.IMP. Exportación', 'B.IMP. O.Gravadas', 'IMPORTE EXONER. INAFECTO', 'I.S.C', 'I.G.V. o IPM', 'ICBP', 'OTROS TRIBUTOS', 'IMPORTE TOTAL', 'T/C', 'FECHA', 'TP DC', 'DOCUMENTO MODIFICADO');

            $excel->setValues($columnas);

            $excel->body(8, 'columnas');

            $excel->setCelda('A1', 'PERIODO: ' .  $mes_anio);
            $excel->setBold('A1');
            $excel->setCelda('A2', 'RUC: ' . $empresa['Ruc']);
            $excel->setBold('A2');
            $excel->setCelda('A3', $empresa['RazonSocial']);
            $excel->setBold('A3');
            $excel->setCelda($excel->getLetra(count($columnas) - 1) . '1', date('d/m/Y'));
            $excel->setBold($excel->getLetra(count($columnas) - 1) . '1');
            $excel->combinarCelda('A5:' . $excel->getLetra(count($columnas) - 1) . '5');
            $excel->setCelda('A5', 'REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'));
            $excel->setBold('A5');
            $excel->setFontSize('A5', 16);
            $excel->combinarCelda('A6:' . $excel->getLetra(count($columnas) - 1) . '6');
            $excel->setCelda('A6', '(Expresado en ' . $moneda . ')');
            $excel->setBold('A6');
            $excel->setFontSize('A6', 14);

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $exonerado = 'det.ExoneradoS';
                $inafecto = 'det.InafectoS';
                $isc = 'det.ISCS';
                $igv = 'det.IGVSunatS';
                $icbp = 'det.IcbpS';
                $otros_tributos = 'det.OtroTributoS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $exonerado = 'det.ExoneradoD';
                $inafecto = 'det.InafectoD';
                $isc = 'det.ISCD';
                $igv = 'det.IGVSunatD';
                $icbp = 'det.IcbpD';
                $otros_tributos = 'det.OtroTributoD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    det.FecEmision,
                    det.FecVcto,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    det.ValorTC,
                    do.CodSunat,
                    socionegocio.CodTipoDoc,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $exonerado . ', 2) AS exonerado,
                    ROUND(' . $isc . ', 2) AS isc,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $icbp . ', 2) AS icbp,
                    ROUND(' . $otros_tributos . ', 2) AS otros_tributos,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_inafecto = 0;
            $totales_isc = 0;
            $totales_igv = 0;
            $totales_icbp = 0;
            $totales_otros_tributos = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_inafecto = 0;
            $total_isc = 0;
            $total_igv = 0;
            $total_icbp = 0;
            $total_otros_tributos = 0;
            $total = 0;

            $index = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['inafecto'] = $valor['exonerado'] != 0 ? $valor['exonerado'] : $valor['inafecto'];

                $valor['total'] = $valor['afecto'] + $valor['inafecto'] + $valor['isc'] + $valor['igv'] + $valor['icbp'] + $valor['otros_tributos'];

                $totales_afecto += $valor['afecto'];
                $totales_inafecto += $valor['inafecto'];
                $totales_isc += $valor['isc'];
                $totales_igv += $valor['igv'];
                $totales_icbp += $valor['icbp'];
                $totales_otros_tributos += $valor['otros_tributos'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_inafecto += $valor['inafecto'];
                $total_isc += $valor['isc'];
                $total_igv += $valor['igv'];
                $total_icbp += $valor['igv'];
                $total_otros_tributos += $valor['otros_tributos'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $values = array(
                        array('value' => $valor['CodSunat'], 'style' => 'bold')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');
                }

                $values = array(
                    $valor['Codmov'],
                    date('d/m/Y', strtotime($valor['FecEmision'])),
                    date('d/m/Y', strtotime($valor['FecVcto'])),
                    $valor['CodSunat'],
                    $valor['SerieDoc'],
                    $valor['CodTipoDoc'],
                    $valor['ruc'],
                    $valor['razonsocial'],
                    number_format(0, 2, '.', ','),
                    number_format($valor['afecto'], 2, '.', ','),
                    number_format($valor['inafecto'], 2, '.', ','),
                    number_format($valor['isc'], 2, '.', ','),
                    number_format($valor['igv'], 2, '.', ','),
                    number_format($valor['icbp'], 2, '.', ','),
                    number_format($valor['otros_tributos'], 2, '.', ','),
                    number_format($valor['total'], 2, '.', ','),
                    $valor['ValorTC']
                );

                $index++;

                $excel->setValues($values);

                $excel->body(8 + $index, 'valor');

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    $values = array(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        array('value' => 'TOTAL GRUPO', 'style' => 'bold||rigth'),
                        number_format(0, 2, '.', ','),
                        number_format($total_afecto, 2, '.', ','),
                        number_format($total_inafecto, 2, '.', ','),
                        number_format($total_isc, 2, '.', ','),
                        number_format($total_igv, 2, '.', ','),
                        number_format($total_icbp, 2, '.', ','),
                        number_format($total_otros_tributos, 2, '.', ','),
                        number_format($total, 2, '.', ',')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');

                    $total_afecto = 0;
                    $total_inafecto = 0;
                    $total_isc = 0;
                    $total_igv = 0;
                    $total_icbp = 0;
                    $total_otros_tributos = 0;
                    $total = 0;
                }
            }

            $values = array(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                array('value' => 'TOTAL GENERAL', 'style' => 'bold||rigth'),
                number_format(0, 2, '.', ','),
                number_format($totales_afecto, 2, '.', ','),
                number_format($totales_inafecto, 2, '.', ','),
                number_format($totales_isc, 2, '.', ','),
                number_format($totales_igv, 2, '.', ','),
                number_format($totales_icbp, 2, '.', ','),
                number_format($totales_otros_tributos, 2, '.', ','),
                number_format($totales, 2, '.', ',')
            );

            $index = $index + 2;

            $excel->setValues($values);

            $excel->body(8 + $index, 'valor');

            $excel->footer('registro_ventas_sunat_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_sunat_A4_pdf()
    {
        try {
            $post = $this->request->getPost();

            $mostrar = isset($post['mostrar']) ? $post['mostrar'] : 'no';
            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];
            $mes_anio = strlen($post['mes']) == 1 ? '0' . $post['mes'] . '/' . date('Y') : $post['mes'] . '/' . date('Y');

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $html = '
            <table>
                <tr>
                    <td><b>PERIODO: ' . $mes_anio . '</b></td>
                    <td align="right"><b> ' . date('d/m/Y') . '</b></td>
                </tr>
                <tr>
                    <td><b>RUC: ' . $empresa['Ruc'] . '</b></td>
                </tr>
                <tr>
                    <td><b>' . $empresa['RazonSocial'] . '</td>
                </tr>
            </table>
            <br>
            <table>
                <tr>
                    <td align="center"><h3><b>FORMATO 14.1: REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y') . '</b></h3></td>
                </tr>
                <tr>
                    <td align="center"><h3>(Expresado en ' . $moneda . ')</h3></td>
                </tr>
            </table>
            <br>
            <br>
        ';

            if ($mostrar == 'no') {
                $columnas = array('FECHA<br>EMISION', 'TP<br>DC', 'SERIE - NÚMERO<br>COMPROB PAGO', 'COD.', 'DOC.<br>REF.', 'DI', 'NUMERO<br>RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'B.IMPONIBLE<br>(1)', 'EXPORTAC.', 'IMPORTE<br>EXONERADO<br>INAFECTO', 'I.S.C', 'I.G.V.<br>(1)', 'OTROS<br>TRIBUTOS', 'IMPORTE<br>TOTAL', 'VOUCHER<br>NRO.');
            } else if ($mostrar == 'si') {
                if ($CodMoneda == 'MO001') {
                    $columnas = array('FECHA<br>EMISION', 'TP<br>DC', 'SERIE - NÚMERO<br>COMPROB PAGO', 'DI', 'NUMERO<br>RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'GLOSA DE VOUCHER', 'CTA', 'TC.', 'IMP $', 'B.IMPONIBLE<br>(1)', 'EXPORTAC.', 'IMPORTE<br>EXONERADO<br>INAFECTO', 'I.S.C', 'I.G.V.<br>(1)', 'OTROS<br>TRIBUTOS', 'IMPORTE<br>TOTAL', 'VOUCHER<br>NRO.');
                } else if ($CodMoneda == 'MO002') {
                    $columnas = array('FECHA<br>EMISION', 'TP<br>DC', 'SERIE - NÚMERO<br>COMPROB PAGO', 'DI', 'NUMERO<br>RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'GLOSA DE VOUCHER', 'CUENTA<br>CONTABLE', 'B.IMPONIBLE<br>(1)', 'EXPORTAC.', 'IMPORTE<br>EXONERADO<br>INAFECTO', 'I.S.C', 'I.G.V.<br>(1)', 'OTROS<br>TRIBUTOS', 'IMPORTE<br>TOTAL', 'VOUCHER<br>NRO.');
                }
            }

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $exonerado = 'det.ExoneradoS';
                $inafecto = 'det.InafectoS';
                $isc = 'det.ISCS';
                $igv = 'det.IGVSunatS';
                $otros_tributos = 'det.OtroTributoS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $exonerado = 'det.ExoneradoD';
                $inafecto = 'det.InafectoD';
                $isc = 'det.ISCD';
                $igv = 'det.IGVSunatD';
                $otros_tributos = 'det.OtroTributoD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    movimientocab.Glosa,
                    det.FecEmision,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    det.CodDocumentoRef,
                    det.ValorTC,
                    CONCAT(det.SerieDocRef, IF(det.NumeroDocRef IS NULL OR LENGTH(det.NumeroDocRef) = 0, "", CONCAT(" - ", det.NumeroDocRef))) AS DocumentoRef,
                    do.CodSunat,
                    socionegocio.CodTipoDoc,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(det.BaseImpSunatS, 2) AS afectoS,
                    ROUND(det.BaseImpSunatD, 2) AS afectoD,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $exonerado . ', 2) AS exonerado,
                    ROUND(' . $isc . ', 2) AS isc,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $otros_tributos . ', 2) AS otros_tributos,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_afectoD = 0;
            $totales_inafecto = 0;
            $totales_isc = 0;
            $totales_igv = 0;
            $totales_otros_tributos = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_afectoD = 0;
            $total_inafecto = 0;
            $total_isc = 0;
            $total_igv = 0;
            $total_otros_tributos = 0;
            $total = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['inafecto'] = $valor['exonerado'] != 0 ? $valor['exonerado'] : $valor['inafecto'];

                $valor['total'] = number_format($valor['afecto'] + $valor['inafecto'] + $valor['isc'] + $valor['igv'] + $valor['otros_tributos'], 2, '.', '');

                $totales_afecto += $valor['afecto'];
                $totales_afectoD += $valor['afectoD'];
                $totales_inafecto += $valor['inafecto'];
                $totales_isc += $valor['isc'];
                $totales_igv += $valor['igv'];
                $totales_otros_tributos += $valor['otros_tributos'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_afectoD += $valor['afectoD'];
                $total_inafecto += $valor['inafecto'];
                $total_isc += $valor['isc'];
                $total_igv += $valor['igv'];
                $total_otros_tributos += $valor['otros_tributos'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $tr .= '
                    <tr>
                        <td><h4>' . $valor['CodSunat'] . '<h4></td>
                    </tr>
                ';
                }

                if ($mostrar == 'no') {
                    $tr .= '
                    <tr>
                        <td>' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                        <td>' . $valor['CodSunat'] . '</td>
                        <td>' . $valor['SerieDoc'] . '</td>
                        <td>' . $valor['CodDocumentoRef'] . '</td>
                        <td>' . $valor['DocumentoRef'] . '</td>
                        <td>' . $valor['CodTipoDoc'] . '</td>
                        <td>' . $valor['ruc'] . '</td>
                        <td class="nowrap">' . $valor['razonsocial'] . '</td>
                        <td align="right">' . number_format($valor['afecto'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['inafecto'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['isc'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['igv'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['otros_tributos'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['total'], 2, '.', ',') . '</td>
                        <td>' . $valor['Codmov'] . '</td>
                    <tr>
                ';
                } else if ($mostrar == 'si') {
                    $td = '';

                    if ($CodMoneda == 'MO001') {
                        $td = '
                            <td>' . $valor['ValorTC'] . '</td>
                            <td align="right">' . $valor['afectoD'] . '</td>
                        ';
                    } else if ($CodMoneda == 'MO002') {
                        $td = '';
                    }

                    $tr .= '
                    <tr>
                        <td>' . date('d/m/Y', strtotime($valor['FecEmision'])) . '</td>
                        <td>' . $valor['CodSunat'] . '</td>
                        <td>' . $valor['SerieDoc'] . '</td>
                        <td>' . $valor['CodTipoDoc'] . '</td>
                        <td>' . $valor['ruc'] . '</td>
                        <td class="nowrap">' . $valor['razonsocial'] . '</td>
                        <td class="nowrap">' . $valor['Glosa'] . '</td>
                        <td></td>
                        ' . $td . '
                        <td align="right">' . number_format($valor['afecto'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['inafecto'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['isc'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['igv'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['otros_tributos'], 2, '.', ',') . '</td>
                        <td align="right">' . number_format($valor['total'], 2, '.', ',') . '</td>
                        <td>' . $valor['Codmov'] . '</td>
                    <tr>
                ';
                }

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    if ($mostrar == 'no') {
                        $tr .= '
                        <tr>
                            <td colspan="8"></td>
                            <td class="border-top" align="right">' . number_format($total_afecto, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format(0, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_inafecto, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_isc, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_igv, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_otros_tributos, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total, 2, '.', ',') . '</td>
                        </tr>
                    ';
                    } else if ($mostrar == 'si') {
                        $td = '';
                        $colspan = '';

                        if ($CodMoneda == 'MO001') {
                            $td = '
                                <td class="border-top" align="right">' . number_format($total_afectoD, 2, '.', ',') . '</td>
                            ';
                            $colspan = 9;
                        } else if ($CodMoneda == 'MO002') {
                            $td = '';
                            $colspan = 8;
                        }

                        $tr .= '
                        <tr>
                            <td colspan="' . $colspan . '"></td>
                            ' . $td . '
                            <td class="border-top" align="right">' . number_format($total_afecto, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format(0, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_inafecto, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_isc, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_igv, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total_otros_tributos, 2, '.', ',') . '</td>
                            <td class="border-top" align="right">' . number_format($total, 2, '.', ',') . '</td>
                        </tr>
                    ';
                    }

                    $total_afecto = 0;
                    $total_afectoD = 0;
                    $total_inafecto = 0;
                    $total_isc = 0;
                    $total_igv = 0;
                    $total_otros_tributos = 0;
                    $total = 0;
                }
            }

            if ($mostrar == 'no') {
                $tr .= '
                <tr>
                    <td colspan="8"></td>
                    <td class="border-bottom" colspan="7"></td>
                </tr>
                <tr>
                    <td colspan="5"></td>
                    <td colspan="3" align="right">TOTAL GENERAL:</td>
                    <td align="right">' . number_format($totales_afecto, 2, '.', ',') . '</td>
                    <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_inafecto, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_isc, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_igv, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_otros_tributos, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales, 2, '.', ',') . '</td>
                </tr>
                <tr>
                    <td colspan="8"></td>
                    <td class="border-general" colspan="7"></td>
                <tr>
            ';
            } else if ($mostrar == 'si') {
                $td = '';
                $colspan = '';

                if ($CodMoneda == 'MO001') {
                    $td = '
                    <td align="right">' . number_format($totales_afectoD, 2, '.', ',') . '</td>
                ';
                    $colspan = 9;
                    $colspan_general = 8;
                } else if ($CodMoneda == 'MO002') {
                    $td = '';
                    $colspan = 8;
                    $colspan_general = 7;
                }

                $tr .= '
                <tr>
                    <td colspan="' . $colspan . '"></td>
                    <td class="border-bottom" colspan="' . $colspan_general . '"></td>
                </tr>
                <tr>
                    <td colspan="6"></td>
                    <td colspan="' . ($colspan - 6) . '" align="right">TOTAL GENERAL:</td>
                    ' . $td . '
                    <td align="right">' . number_format($totales_afecto, 2, '.', ',') . '</td>
                    <td align="right">' . number_format(0, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_inafecto, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_isc, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_igv, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales_otros_tributos, 2, '.', ',') . '</td>
                    <td align="right">' . number_format($totales, 2, '.', ',') . '</td>
                </tr>
                <tr>
                    <td colspan="' . $colspan . '"></td>
                    <td class="border-general" colspan="' . $colspan_general . '"></td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('registro_ventas_sunat_A4_reporte');
            $pdf->creacion('registro_ventas_sunat_A4_reporte', $tr, $html, 'A4', false);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function registro_ventas_sunat_A4_excel()
    {
        try {
            $post = $this->request->getPost();

            $mostrar = isset($post['mostrar']) ? $post['mostrar'] : 'no';
            $CodMoneda = $post['moneda'];
            $periodo = $post['periodo'] == 'M' ? 'DE' : 'A';
            $ordenar1 = $post['ordenar1'];
            $ordenar2 = $post['ordenar2'];
            $ordenar3 = $post['ordenar3'];
            $mes = $post['periodo'] == 'M' ? ' = ' . $post['mes'] : ' <= ' . $post['mes'];
            $mes_anio = strlen($post['mes']) == 1 ? '0' . $post['mes'] . '/' . date('Y') : $post['mes'] . '/' . date('Y');

            $moneda = (new Moneda())->getMoneda($CodMoneda, 'DescMoneda', [], '', '')[0]['DescMoneda'];
            $moneda = strtoupper($moneda[0]) . strtolower(substr($moneda, 1));

            $empresa = (new ModelsEmpresa())->getEmpresa($this->CodEmpresa, '', [], '', '')[0];

            $excel = new Excel();

            $excel->creacion(str_split('FORMATO 14.1 - REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'), 31)[0]);

            if ($mostrar == 'no') {
                $columnas = array('FECHA EMISION', 'TP DC', 'SERIE - NÚMERO COMPROB PAGO', 'COD.', 'DOC. REF.', 'DI', 'NUMERO RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'B.IMPONIBLE (1)', 'EXPORTAC.', 'IMPORTE EXONERADO INAFECTO', 'I.S.C', 'I.G.V. (1)', 'OTROS TRIBUTOS', 'IMPORTE TOTAL', 'VOUCHER NRO.');
            } else if ($mostrar == 'si') {
                if ($CodMoneda == 'MO001') {
                    $columnas = array('FECHA EMISION', 'TP DC', 'SERIE - NÚMERO COMPROB PAGO', 'DI', 'NUMERO RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'GLOSA DE VOUCHER', 'CTA', 'TC.', 'IMP $', 'B.IMPONIBLE (1)', 'EXPORTAC.', 'IMPORTE EXONERADO INAFECTO', 'I.S.C', 'I.G.V. (1)', 'OTROS TRIBUTOS', 'IMPORTE TOTAL', 'VOUCHER NRO.');
                } else if ($CodMoneda == 'MO002') {
                    $columnas = array('FECHA EMISION', 'TP DC', 'SERIE - NÚMERO COMPROB PAGO', 'DI', 'NUMERO RUC / DNI', 'NOMBRE O RAZON SOCIAL', 'GLOSA DE VOUCHER', 'CUENTA CONTABLE', 'B.IMPONIBLE (1)', 'EXPORTAC.', 'IMPORTE EXONERADO INAFECTO', 'I.S.C', 'I.G.V. (1)', 'OTROS TRIBUTOS', 'IMPORTE TOTAL', 'VOUCHER NRO.');
                }
            }

            $excel->setValues($columnas);

            $excel->body(8, 'columnas');

            $excel->setCelda('A1', 'PERIODO: ' .  $mes_anio);
            $excel->setBold('A1');
            $excel->setCelda('A2', 'RUC: ' . $empresa['Ruc']);
            $excel->setBold('A2');
            $excel->setCelda('A3', $empresa['RazonSocial']);
            $excel->setBold('A3');
            $excel->setCelda($excel->getLetra(count($columnas) - 1) . '1', date('d/m/Y'));
            $excel->setBold($excel->getLetra(count($columnas) - 1) . '1');
            $excel->combinarCelda('A5:' . $excel->getLetra(count($columnas) - 1) . '5');
            $excel->setCelda('A5', 'REGISTRO DE VENTAS ' . $periodo . ' ' . strtoupper($this->meses()[(int)$post['mes']]) . ' ' . date('Y'));
            $excel->setBold('A5');
            $excel->setFontSize('A5', 16);
            $excel->combinarCelda('A6:' . $excel->getLetra(count($columnas) - 1) . '6');
            $excel->setCelda('A6', '(Expresado en ' . $moneda . ')');
            $excel->setBold('A6');
            $excel->setFontSize('A6', 14);

            $this->db->query('SET sql_mode = ""');

            if ($CodMoneda == 'MO001') {
                $afecto = 'det.BaseImpSunatS';
                $exonerado = 'det.ExoneradoS';
                $inafecto = 'det.InafectoS';
                $isc = 'det.ISCS';
                $igv = 'det.IGVSunatS';
                $otros_tributos = 'det.OtroTributoS';
                $total = 'det.TotalS';
            } else if ($CodMoneda == 'MO002') {
                $afecto = 'det.BaseImpSunatD';
                $exonerado = 'det.ExoneradoD';
                $inafecto = 'det.InafectoD';
                $isc = 'det.ISCD';
                $igv = 'det.IGVSunatD';
                $otros_tributos = 'det.OtroTributoD';
                $total = 'det.TotalD';
            }

            $result = (new MovimientoCab())->getMovimientoCab(
                $this->CodEmpresa,
                0,
                '
                    movimientocab.Codmov,
                    movimientocab.Glosa,
                    det.FecEmision,
                    det.SerieDoc,
                    det.NumeroDoc,
                    det.NumeroDocF,
                    det.CodDocumentoRef,
                    det.ValorTC,
                    CONCAT(det.SerieDocRef, IF(det.NumeroDocRef IS NULL OR LENGTH(det.NumeroDocRef) = 0, "", CONCAT(" - ", det.NumeroDocRef))) AS DocumentoRef,
                    do.CodSunat,
                    socionegocio.CodTipoDoc,
                ' . (new SocioNegocio())->getNumeroDocumento() . ' AS ruc,
                ' . (new SocioNegocio())->getRazonSocial(false) . ' AS razonsocial,
                    ROUND(det.BaseImpSunatS, 2) AS afectoS,
                    ROUND(det.BaseImpSunatD, 2) AS afectoD,
                    ROUND(' . $afecto . ', 2) AS afecto,
                    ROUND(' . $inafecto . ', 2) AS inafecto,
                    ROUND(' . $exonerado . ', 2) AS exonerado,
                    ROUND(' . $isc . ', 2) AS isc,
                    ROUND(' . $igv . ', 2) AS igv,
                    ROUND(' . $otros_tributos . ', 2) AS otros_tributos,
                    ROUND(' . $total . ', 2) AS total
                ',
                [
                    array('tabla' => 'movimientodet det', 'on' => 'det.IdMov = movimientocab.IdMov AND det.CodEmpresa = movimientocab.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'documento do', 'on' => 'do.CodDocumento = det.CodDocumento AND do.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner'),
                    array('tabla' => 'socionegocio', 'on' => 'socionegocio.IdSocioN = det.IdSocioN AND socionegocio.CodEmpresa = det.CodEmpresa', 'tipo' => 'inner')
                ],
                [],
                'det.Parametro = "TOTAL" AND MONTH(movimientocab.FecContable) ' . $mes . ' AND YEAR(movimientocab.FecContable) = ' . date('Y'),
                'det.IdMov',
                'do.CodSunat ASC, ' . $ordenar1 . ' ASC, ' . $ordenar2 . ' ASC, ' . $ordenar3 . ' ASC'
            );

            $totales_afecto = 0;
            $totales_afectoD = 0;
            $totales_inafecto = 0;
            $totales_isc = 0;
            $totales_igv = 0;
            $totales_otros_tributos = 0;
            $totales = 0;

            $total_afecto = 0;
            $total_afectoD = 0;
            $total_inafecto = 0;
            $total_isc = 0;
            $total_igv = 0;
            $total_otros_tributos = 0;
            $total = 0;

            $index = 0;

            foreach ($result as $indice => $valor) {
                (!empty($valor['NumeroDocF'])) ? $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'] . '-' . $valor['NumeroDocF'] : $valor['SerieDoc'] = $valor['SerieDoc'] . '-' . $valor['NumeroDoc'];

                $valor['inafecto'] = $valor['exonerado'] != 0 ? $valor['exonerado'] : $valor['inafecto'];

                $valor['total'] = number_format($valor['afecto'] + $valor['inafecto'] + $valor['isc'] + $valor['igv'] + $valor['otros_tributos'], 2, '.', '');

                $totales_afecto += $valor['afecto'];
                $totales_afectoD += $valor['afectoD'];
                $totales_inafecto += $valor['inafecto'];
                $totales_isc += $valor['isc'];
                $totales_igv += $valor['igv'];
                $totales_otros_tributos += $valor['otros_tributos'];
                $totales += $valor['total'];

                $total_afecto += $valor['afecto'];
                $total_afectoD += $valor['afectoD'];
                $total_inafecto += $valor['inafecto'];
                $total_isc += $valor['isc'];
                $total_igv += $valor['igv'];
                $total_otros_tributos += $valor['otros_tributos'];
                $total += $valor['total'];

                if (isset($result[$indice - 1]) && $result[$indice]['CodSunat'] != $result[$indice - 1]['CodSunat'] || $indice == 0) {
                    $values = array(
                        array('value' => $valor['CodSunat'], 'style' => 'bold')
                    );

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');
                }

                if ($mostrar == 'no') {
                    $values = array(
                        date('d/m/Y', strtotime($valor['FecEmision'])),
                        $valor['CodSunat'],
                        $valor['SerieDoc'],
                        $valor['CodDocumentoRef'],
                        $valor['DocumentoRef'],
                        $valor['CodTipoDoc'],
                        $valor['ruc'],
                        $valor['razonsocial'],
                        number_format($valor['afecto'], 2, '.', ','),
                        number_format(0, 2, '.', ','),
                        number_format($valor['inafecto'], 2, '.', ','),
                        number_format($valor['isc'], 2, '.', ','),
                        number_format($valor['igv'], 2, '.', ','),
                        number_format($valor['otros_tributos'], 2, '.', ','),
                        number_format($valor['total'], 2, '.', ','),
                        $valor['Codmov']
                    );
                } else if ($mostrar == 'si') {
                    $values = array();

                    if ($CodMoneda == 'MO001') {
                        $values = array(
                            date('d/m/Y', strtotime($valor['FecEmision'])),
                            $valor['CodSunat'],
                            $valor['SerieDoc'],
                            $valor['CodTipoDoc'],
                            $valor['ruc'],
                            $valor['razonsocial'],
                            $valor['Glosa'],
                            '',
                            $valor['ValorTC'],
                            $valor['afectoD'],
                            number_format($valor['afecto'], 2, '.', ','),
                            number_format(0, 2, '.', ','),
                            number_format($valor['inafecto'], 2, '.', ','),
                            number_format($valor['isc'], 2, '.', ','),
                            number_format($valor['igv'], 2, '.', ','),
                            number_format($valor['otros_tributos'], 2, '.', ','),
                            number_format($valor['total'], 2, '.', ','),
                            $valor['Codmov']
                        );
                    } else if ($CodMoneda == 'MO002') {
                        $values = array(
                            date('d/m/Y', strtotime($valor['FecEmision'])),
                            $valor['CodSunat'],
                            $valor['SerieDoc'],
                            $valor['CodTipoDoc'],
                            $valor['ruc'],
                            $valor['razonsocial'],
                            $valor['Glosa'],
                            '',
                            number_format($valor['afecto'], 2, '.', ','),
                            number_format(0, 2, '.', ','),
                            number_format($valor['inafecto'], 2, '.', ','),
                            number_format($valor['isc'], 2, '.', ','),
                            number_format($valor['igv'], 2, '.', ','),
                            number_format($valor['otros_tributos'], 2, '.', ','),
                            number_format($valor['total'], 2, '.', ','),
                            $valor['Codmov']
                        );
                    }
                }

                $index++;

                $excel->setValues($values);

                $excel->body(8 + $index, 'valor');

                if (isset($result[$indice + 1]) && $result[$indice]['CodSunat'] != ($result[$indice + 1]['CodSunat']) || count($result) == ($indice + 1)) {
                    if ($mostrar == 'no') {
                        $values = array(
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            array('value' => 'TOTAL GRUPO', 'style' => 'bold||rigth'),
                            number_format($total_afecto, 2, '.', ','),
                            number_format(0, 2, '.', ','),
                            number_format($total_inafecto, 2, '.', ','),
                            number_format($total_isc, 2, '.', ','),
                            number_format($total_igv, 2, '.', ','),
                            number_format($total_otros_tributos, 2, '.', ','),
                            number_format($total, 2, '.', ',')
                        );
                    } else if ($mostrar == 'si') {
                        $values = array();

                        if ($CodMoneda == 'MO001') {
                            $values = array(
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                array('value' => 'TOTAL GRUPO', 'style' => 'bold||rigth'),
                                number_format($total_afectoD, 2, '.', ','),
                                number_format($total_afecto, 2, '.', ','),
                                number_format(0, 2, '.', ','),
                                number_format($total_inafecto, 2, '.', ','),
                                number_format($total_isc, 2, '.', ','),
                                number_format($total_igv, 2, '.', ','),
                                number_format($total_otros_tributos, 2, '.', ','),
                                number_format($total, 2, '.', ',')
                            );
                        } else if ($CodMoneda == 'MO002') {
                            $values = array(
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                array('value' => 'TOTAL GRUPO', 'style' => 'bold||rigth'),
                                number_format($total_afecto, 2, '.', ','),
                                number_format(0, 2, '.', ','),
                                number_format($total_inafecto, 2, '.', ','),
                                number_format($total_isc, 2, '.', ','),
                                number_format($total_igv, 2, '.', ','),
                                number_format($total_otros_tributos, 2, '.', ','),
                                number_format($total, 2, '.', ',')
                            );
                        }
                    }

                    $index++;

                    $excel->setValues($values);

                    $excel->body(8 + $index, 'valor');

                    $total_afecto = 0;
                    $total_afectoD = 0;
                    $total_inafecto = 0;
                    $total_isc = 0;
                    $total_igv = 0;
                    $total_otros_tributos = 0;
                    $total = 0;
                }
            }

            if ($mostrar == 'no') {
                $values = array(
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    array('value' => 'TOTAL GENERAL', 'style' => 'bold||rigth'),
                    number_format($totales_afecto, 2, '.', ','),
                    number_format(0, 2, '.', ','),
                    number_format($totales_inafecto, 2, '.', ','),
                    number_format($totales_isc, 2, '.', ','),
                    number_format($totales_igv, 2, '.', ','),
                    number_format($totales_otros_tributos, 2, '.', ','),
                    number_format($totales, 2, '.', ',')
                );
            } else if ($mostrar == 'si') {
                $values = array();

                if ($CodMoneda == 'MO001') {
                    $values = array(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        array('value' => 'TOTAL GENERAL', 'style' => 'bold||rigth'),
                        number_format($totales_afectoD, 2, '.', ','),
                        number_format($totales_afecto, 2, '.', ','),
                        number_format(0, 2, '.', ','),
                        number_format($totales_inafecto, 2, '.', ','),
                        number_format($totales_isc, 2, '.', ','),
                        number_format($totales_igv, 2, '.', ','),
                        number_format($totales_otros_tributos, 2, '.', ','),
                        number_format($totales, 2, '.', ',')
                    );
                } else if ($CodMoneda == 'MO002') {
                    $values = array(
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        array('value' => 'TOTAL GENERAL', 'style' => 'bold||rigth'),
                        number_format($totales_afecto, 2, '.', ','),
                        number_format(0, 2, '.', ','),
                        number_format($totales_inafecto, 2, '.', ','),
                        number_format($totales_isc, 2, '.', ','),
                        number_format($totales_igv, 2, '.', ','),
                        number_format($totales_otros_tributos, 2, '.', ','),
                        number_format($totales, 2, '.', ',')
                    );
                }
            }

            $index = $index + 2;

            $excel->setValues($values);

            $excel->body(8 + $index, 'valor');

            $excel->footer('registro_ventas_sunat_A4_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function meses()
    {
        try {
            return array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
