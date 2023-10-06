<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\I_AnexoSunat;

class IAnexoSunat extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'IAnexoSunat';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $I_AnexoSunat = (new I_AnexoSunat())->getI_AnexoSunat($post['IdAnexoS'], $post['TipoAnexoS'], 'IdAnexoS AS id, DescAnexoS AS text', [], 'DescAnexoS LIKE "%' . $search . '%"', '');
            } else {
                $I_AnexoSunat = (new I_AnexoSunat())->getI_AnexoSunat($post['IdAnexoS'], $post['TipoAnexoS'], 'IdAnexoS AS id, DescAnexoS AS text', [], '', '');
            }

            echo json_encode($I_AnexoSunat);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
