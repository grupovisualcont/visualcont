<?php

namespace App\Controllers;

session_start();

class Home extends BaseController
{
    public function index()
    {
        try {
            if (isset($_SESSION['empresa'])) {
                return redirect()->to(base_url('app/panel/index'));
            } else {
                return view('login/index');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
