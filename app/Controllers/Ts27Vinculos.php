<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Ts27Vinculo;

class Ts27Vinculos extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $ts27VinculoModel;

    public function __construct()
    {
        $this->page = 'Ts27Vinculo';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->ts27VinculoModel = new Ts27Vinculo();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $this->ts27VinculoModel = new Ts27Vinculo();

                $ts27Vinculo = $this->ts27VinculoModel->getTs27Vinculo('CodVinculo AS value, DescVinculo AS name', [], 'DescVinculo LIKE "%' . $search . '%"', '');
            } else {
                $this->ts27VinculoModel = new Ts27Vinculo();

                $ts27Vinculo = $this->ts27VinculoModel->getTs27Vinculo('CodVinculo AS value, DescVinculo AS name', [], '', '');
            }

            echo json_encode($ts27Vinculo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
