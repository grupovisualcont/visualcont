<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Ts27Vinculo;

class Ts27Vinculos extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Ts27Vinculo';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $ts27Vinculo = (new Ts27Vinculo())->getTs27Vinculo('', 'CodVinculo AS id, DescVinculo AS text', [], 'DescVinculo LIKE "%' . $search . '%"', '');
            } else {
                $ts27Vinculo = (new Ts27Vinculo())->getTs27Vinculo('', 'CodVinculo AS id, DescVinculo AS text', [], '', '');
            }

            echo json_encode($ts27Vinculo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
