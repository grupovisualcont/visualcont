<?php

namespace App\Controllers;

use App\Controllers\BaseController;

session_start();

class Panel extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Inicio';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();
    }

    public function index()
    {
        $data = $this->empresa->menu($this->page);

        return view('app/panel/index', $data);
    }
}
