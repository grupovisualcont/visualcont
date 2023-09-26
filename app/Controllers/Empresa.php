<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Empresa as ModelsEmpresa;
use App\Models\Moneda;
use App\Models\TipoCambio;

class Empresa extends BaseController
{
    protected $page;
    protected $helpers;
    protected $CodEmpresa;

    protected $empresaModel;
    protected $sidebarModel;
    protected $sidebarDetallesModel;
    protected $tipoCambioModel;
    protected $monedaModel;

    public function __construct()
    {
        $this->page = 'Inicio';
        $this->helpers = ['form', 'url', 'html'];
        $this->CodEmpresa = isset($_COOKIE['empresa']) && !empty($_COOKIE['empresa']) ? $_COOKIE['empresa'] : '';

        $this->empresaModel = model('Empresa');
        $this->sidebarModel = model('Sidebar');
        $this->sidebarDetallesModel = model('SidebarDetalles');
        $this->tipoCambioModel = model('TipoCambio');
        $this->monedaModel = model('Moneda');
    }

    public function getCodEmpresa()
    {
        try {
            return $this->CodEmpresa;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login()
    {
        try {
            $validation = $this->validate([
                'usuario' => [
                    'rules' => 'required|max_length[11]',
                    'errors' => [
                        'required' => 'El campo Usuario es requerido'
                    ]
                ],
                'password' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => 'El campo Contraseña es requerido',
                    ]
                ],
            ]);

            if (!$validation) {
                return view('login/index', [
                    'validation' => $this->validator
                ]);
            } else {
                $usuario = strtoupper(strval($this->request->getPost('usuario')));
                $password = trim(strval($this->request->getPost('password')));

                $this->empresaModel = new ModelsEmpresa();

                $result = $this->empresaModel->where('CodEmpresa', $usuario)->where('Contraseña', $password)->findAll();

                if (count($result) == 1) {
                    $_SESSION['empresa'] = $usuario;

                    setcookie('empresa', $usuario, time() + 60 * 60 * 24 * 365, '/');

                    return redirect()->to(base_url() . 'app/panel/index');
                } else {
                    return view('login/index', [
                        'validation' => $this->validator,
                        'login' => 'La empresa o contraseña no existe'
                    ]);
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout()
    {
        try {
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }

            unset($this->CodEmpresa);
            setcookie('empresa', '', time() - 3600 * 24, '/');

            return redirect()->to(base_url());
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function verificar_inicio_sesion()
    {
        try {
            if (isset($_SESSION['empresa'])) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function sidebars()
    {
        try {
            $result = $this->sidebarModel->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function sidebardetalles()
    {
        try {
            $result = $this->sidebarDetallesModel->findAll();

            return $result;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_sunat()
    {
        try {
            $tipo_documento = $this->request->getPost('tipo_documento');
            $numero_documento = $this->request->getPost('numero_documento');

            if ($tipo_documento == 'ruc') {
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
            } elseif ($tipo_documento == 'dni') {
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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_tipo_cambio()
    {
        try {
            $fecha = date('Y-m-d');

            $this->tipoCambioModel = new TipoCambio();

            $tipo_cambio = $this->tipoCambioModel->where('CodEmpresa', $this->CodEmpresa)->where('FechaTipoCambio', $fecha . ' 00:00:00')->findAll();

            $this->monedaModel = new Moneda();

            $moneda = $this->monedaModel->select('CodMoneda')->where('AbrevMoneda', 'USD')->findAll();

            if (count($tipo_cambio) == 0) {
                $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=' . $fecha,
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

                $tipoCambioSunat = json_decode($response);

                if ($tipoCambioSunat->compra != NULL && $tipoCambioSunat->venta != NULL) {
                    $post['FechaTipoCambio'] = $fecha . ' 00:00:00';
                    $post['CodEmpresa'] = $this->CodEmpresa;
                    $post['CodMoneda'] = $moneda[0]['CodMoneda'];
                    $post['ValorCompra'] = $tipoCambioSunat->compra;
                    $post['ValorVenta'] = $tipoCambioSunat->venta;
                    $post['Estado'] = 1;

                    $this->tipoCambioModel = new TipoCambio();

                    $tipo_cambio = $this->tipoCambioModel->where('CodEmpresa', $this->CodEmpresa)->where('FechaTipoCambio', $post['FechaTipoCambio'])->findAll();

                    if (count($tipo_cambio) == 0) $this->tipoCambioModel->insert($post);
                }
            } else {
                $tipoCambioSunat = json_decode(json_encode(array('compra' => $tipo_cambio[0]['ValorCompra'], 'venta' => $tipo_cambio[0]['ValorVenta'])));
            }

            return $tipoCambioSunat;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generar_script($nuevo, $rutas)
    {
        try {
            $script = '';

            if (!empty($nuevo)) {
                $script .= '<script>' . $nuevo . '</script>';
            }

            if (is_array($rutas) && count($rutas) > 0) {
                foreach ($rutas as $indice => $valor) {
                    $script .= '<script src="' . base_url() . 'public/assets/js/' . $valor . '"></script>';
                }
            }

            return $script;
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
