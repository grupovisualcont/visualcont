<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConceptoPres as ModelsConceptoPres;

class ConceptoPres extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'ConceptoPres';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            $whereLength = isset($post['length']) ? 'LENGTH(CodConceptoPres) = ' . $post['length'] : '';

            if (isset($post['search'])) {
                $search = $post['search'];

                $concepto_pres = (new ModelsConceptoPres())->getConceptoPres($this->CodEmpresa, '', 'CodConceptoPres AS id, descConceptoPres AS text', [], 'descConceptoPres LIKE "%' . $search . '%"' . $whereLength ? ' AND ' . $whereLength : '', '');
            } else {
                $concepto_pres = (new ModelsConceptoPres())->getConceptoPres($this->CodEmpresa, '', 'CodConceptoPres AS id, descConceptoPres AS text', [], $whereLength, '');
            }

            echo json_encode($concepto_pres);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
