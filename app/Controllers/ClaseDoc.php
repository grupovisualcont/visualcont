<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ClaseDoc as ModelsClaseDoc;

class ClaseDoc extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'ClaseDoc';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $clase_doc = (new ModelsClaseDoc())->getClaseDoc('', 'CodClaseDoc AS id, DescClaseDoc AS text', [], 'DescClaseDoc LIKE "' . $search . '%"', '');
            } else {
                $clase_doc = (new ModelsClaseDoc())->getClaseDoc('', 'CodClaseDoc AS id, DescClaseDoc AS text', [], '', '');
            }

            echo json_encode($clase_doc);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
