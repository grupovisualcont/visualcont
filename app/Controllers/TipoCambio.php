<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Moneda;
use App\Models\TipoCambio as ModelsTipoCambio;

class TipoCambio extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $tipoCambioModel;
    protected $monedaModel;

    public function __construct()
    {
        $this->page = 'Tipo de Cambio';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->tipoCambioModel = new ModelsTipoCambio();
        $this->monedaModel = new Moneda();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $anio = date('Y');

                $meses = $this->meses();

                foreach ($meses as $indice => $valor) {
                    if ($indice > date('m')) unset($meses[$indice]);
                }

                krsort($meses);

                $this->empresa = new Empresa();

                $script = "
                    var anio_hoy = " . $anio . ";
                    var mes_hoy = " . date('m') . ";
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/exchange_rate/index.js']);

                return viewApp($this->page, 'app/mantenience/exchange_rate/index', [
                    'anio' => $anio,
                    'meses' => $meses,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($anio, $mes)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->monedaModel = new Moneda();

                $monedas = $this->monedaModel->getMoneda('', '');

                $options_moneda = '';

                foreach ($monedas as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodMoneda'] == 'MO002') $selected = 'selected';

                    $options_moneda .= '<option value="' . $valor['CodMoneda'] . '" ' . $selected . '>' . $valor['DescMoneda'] . '</option>';
                }

                $meses = $this->meses();

                $options_meses = '';

                foreach ($meses as $indice => $valor) {
                    $selected = '';

                    if ($indice == $mes) $selected = 'selected';

                    $options_meses .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $fecha = $anio . '-' . $mes . '-01';

                $ultimo_dia = date('t', strtotime($fecha));

                $datos = '';

                for ($dia = 1; $dia <= $ultimo_dia; $dia++) {
                    $this->tipoCambioModel = new ModelsTipoCambio();

                    $tipo_cambio = $this->tipoCambioModel->getTipoCambio(
                        $this->CodEmpresa,
                        '',
                        'FechaTipoCambio, ValorCompra, ValorVenta',
                        [],
                        'YEAR(FechaTipoCambio) = ' . $anio . ' AND MONTH(FechaTipoCambio) = ' . $mes . ' AND DAY(FechaTipoCambio) = ' . $dia,
                        ''
                    );

                    $FechaTipoCambio = '';
                    $ValorCompra = '';
                    $ValorVenta = '';

                    if (count($tipo_cambio) > 0) {
                        $FechaTipoCambio = $tipo_cambio[0]['FechaTipoCambio'];
                        $ValorCompra = $tipo_cambio[0]['ValorCompra'];
                        $ValorVenta = $tipo_cambio[0]['ValorVenta'];
                    }

                    if ($dia == 1 || $dia == 11 || $dia == 21) {
                        $datos .= '
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                            <label>Dia</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                            <label>Compra</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                            <label>Venta</label>
                                        </div>
                                    </div>
                        ';
                    }

                    $datos .= '
                        <div class="row mt-2">
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <b>' . $dia . '</b>
                            </div>
                            <input type="hidden" name="FechaTipoCambio[]" value="' . $FechaTipoCambio . '" id="FechaTipoCambio' . $dia . '" />
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <input type="text" name="ValorCompra[]" id="ValorCompra' . $dia . '" class="form-control form-control-sm" value="' . $ValorCompra . '" />
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <input type="text" name="ValorVenta[]" id="ValorVenta' . $dia . '" class="form-control form-control-sm" value="' . $ValorVenta . '" />
                            </div>
                        </div>
                    ';

                    if ($dia == 10 || $dia == 20 || $dia == $ultimo_dia) {
                        $datos .= '
                            </div>
                        ';
                    }
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/exchange_rate/edit.js']);

                return viewApp($this->page, 'app/mantenience/exchange_rate/edit', [
                    'datos' => $datos,
                    'options_moneda' => $options_moneda,
                    'options_meses' => $options_meses,
                    'anio' => $anio,
                    'mes' => $mes,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
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

            $anio = $post['Anio'];
            $mes = $post['Mes'];

            $fecha = $anio . '-' . $mes . '-01';

            $ultimo_dia = date('t', strtotime($fecha));

            for ($indice = 0; $indice < $ultimo_dia; $indice++) {
                $data = [
                    'FechaTipoCambio' => $post['FechaTipoCambio'][$indice],
                    'CodEmpresa' => $post['CodEmpresa'],
                    'CodMoneda' => 'MO002',
                    'ValorCompra' => $post['ValorCompra'][$indice],
                    'ValorVenta' => $post['ValorVenta'][$indice],
                    'Estado' => 1
                ];

                $this->tipoCambioModel = new ModelsTipoCambio();


                if (!empty($post['FechaTipoCambio'][$indice])) {
                    $tipo_cambio = $this->tipoCambioModel->getTipoCambio($post['CodEmpresa'], $post['FechaTipoCambio'][$indice], '', [], '', '');

                    if (count($tipo_cambio) > 0) {
                        $this->tipoCambioModel = new ModelsTipoCambio();

                        $this->tipoCambioModel->actualizar($post['CodEmpresa'], $post['FechaTipoCambio'][$indice], $data);
                    } else {
                        $this->tipoCambioModel = new ModelsTipoCambio();

                        $this->tipoCambioModel->agregar($data);
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

            return redirect()->to(base_url('app/mantenience/exchange_rate/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel($anio, $mes)
    {
        try {
            $excel = new Excel();

            $excel->creacion('Tipo de Cambio - Reporte');

            $columnas = array('Año', 'Mes', 'Dia', 'Compra', 'Venta');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $fecha = $anio . '-' . $mes . '-01';

            $ultimo_dia = date('t', strtotime($fecha));

            $indice = 0;

            for ($dia = 1; $dia <= $ultimo_dia; $dia++) {
                $this->tipoCambioModel = new ModelsTipoCambio();

                $result = $this->tipoCambioModel->getTipoCambio(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'YEAR(FechaTipoCambio) = ' . $anio . ' AND MONTH(FechaTipoCambio) = ' . $mes . ' AND DAY(FechaTipoCambio) = ' . $dia,
                    ''
                );

                $ValorCompra = '';
                $ValorVenta = '';

                if (count($result) > 0) {
                    $ValorCompra = $result[0]['ValorCompra'];
                    $ValorVenta = $result[0]['ValorVenta'];
                }

                $values = array(
                    $anio,
                    $this->meses()[$mes],
                    $dia,
                    $ValorCompra,
                    $ValorVenta
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');

                $indice++;
            }

            $excel->footer('tipo_cambio_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf($anio, $mes)
    {
        try {
            $fecha = $anio . '-' . $mes . '-01';

            $ultimo_dia = date('t', strtotime($fecha));

            $columnas = array('Año', 'Mes', 'Dia', 'Compra', 'Venta');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            for ($dia = 1; $dia <= $ultimo_dia; $dia++) {
                $this->tipoCambioModel = new ModelsTipoCambio();

                $result = $this->tipoCambioModel->getTipoCambio(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'YEAR(FechaTipoCambio) = ' . $anio . ' AND MONTH(FechaTipoCambio) = ' . $mes . ' AND DAY(FechaTipoCambio) = ' . $dia,
                    ''
                );

                $ValorCompra = '';
                $ValorVenta = '';

                if (count($result) > 0) {
                    $ValorCompra = $result[0]['ValorCompra'];
                    $ValorVenta = $result[0]['ValorVenta'];
                }

                $tr .= '
                    <tr>
                        <td align="left">' . $anio . '</td>
                        <td align="left">' . $this->meses()[$mes] . '</td>
                        <td align="left">' . $dia . '</td>
                        <td align="left">' . $ValorCompra . '</td>
                        <td align="left">' . $ValorVenta . '</td>
                    <tr>
                ';
            }

            $pdf = new PDF();

            $pdf->setFilename('tipo_cambio_reporte');
            $pdf->creacion('Tipo de Cambio - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta()
    {
        try {
            $post = $this->request->getPost();

            $anio = $post['Anio'];
            $mes = $post['Mes'];

            $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?month=' . $mes . '&year=' . $anio,
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

            $respuesta = json_decode($response);

            $datos_api = array();

            foreach ($respuesta as $indice => $valor) {
                $datos_api[] = array('fecha' => $valor->fecha . ' 00:00:00', 'dia' => ($indice + 1), 'compra' => $valor->compra, 'venta' => $valor->venta);
            }

            echo json_encode($datos_api);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function meses()
    {
        try {
            $meses = array(1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre');

            return $meses;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
