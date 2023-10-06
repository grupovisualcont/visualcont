<?php

namespace App\Controllers;

use App\Controllers\BaseController;

session_start();

class Panel extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    public function __construct()
    {
        $this->page = 'Inicio';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();
    }

    public function index()
    {
        return viewApp($this->page, 'app/panel/index');
    }
}
