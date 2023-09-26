<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Empresa as ModelsEmpresa;

session_start();

class Panel extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $empresaModel;

    public function __construct()
    {
        $this->page = 'Inicio';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->empresaModel = model('Empresa');
    }

    public function index()
    {
        $this->empresaModel = new ModelsEmpresa();

        $empresa = $this->empresaModel->select('RazonSocial, Ruc')->where('CodEmpresa', $this->empresa->getCodEmpresa())->findAll();

        $razon_social = '';
        $ruc = '';

        if(count($empresa) > 0){
            $razon_social = $empresa[0]['RazonSocial'];
            $ruc = $empresa[0]['Ruc'];
        }

        return view('app/panel/index', [
            'page' => $this->page,
            'sidebars' => $this->empresa->sidebars(),
            'sidebardetalles' => $this->empresa->sidebardetalles(),
            'razon_social' => $razon_social,
            'ruc' => $ruc,
            'fecha' => date('d/m/Y'),
            'tipo_cambio' => $this->empresa->consulta_tipo_cambio(),
        ]);
    }
}
