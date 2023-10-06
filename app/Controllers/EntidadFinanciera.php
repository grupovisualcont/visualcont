<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntidadFinanciera as ModelsEntidadFinanciera;

class EntidadFinanciera extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Entidad Financiera';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $entidad_financiera = (new ModelsEntidadFinanciera())->getEntidadFinanciera('', 'CodEntidad AS id, DescFinanciera AS text', [], 'DescFinanciera LIKE "%' . $search . '%"', '');
            } else {
                $entidad_financiera = (new ModelsEntidadFinanciera())->getEntidadFinanciera('', 'CodEntidad AS id, DescFinanciera AS text', [], '', '');
            }

            echo json_encode($entidad_financiera);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
