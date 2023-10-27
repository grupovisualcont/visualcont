<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\EmpresaModel;

class LoginController extends BaseController
{
    public function index()
    {
        return view('login/index');
    }

    public function validarUsuario()
    {
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
        }

        $usuario = strtoupper(strval($this->request->getPost('usuario')));
        $password = trim(strval($this->request->getPost('password')));

        $result = (new EmpresaModel())->login($usuario, $password);

        if (!empty($result)) {
            $dataSession = [
                'empresa'  => $usuario,
            ];
            $this->session->set($dataSession);
            setcookie('empresa', $usuario, time() + 60 * 60 * 24 * 365, '/');

            return redirect()->to(baseUrlWeb('panel'));
        }
        
        return view('login/index', [
            'validation' => $this->validator,
            'login' => 'La empresa o contraseña no existe'
        ]);
    }

    public function logout()
    {
        $this->session->remove('empresa');
        $this->session->close();

        setcookie('empresa', '', time() - 3600 * 24, '/');

        return redirect()->to(base_url());
    }
}
