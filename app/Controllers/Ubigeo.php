<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Ubigeo as ModelsUbigeo;

class Ubigeo extends BaseController
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
            $this->db = \Config\Database::connect();

            $post = $this->request->getPost();

            if ($post['tipo'] == 'ubigeo') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $ubigeo = (new ModelsUbigeo())->getUbigeoQuery($this->db, '', $search);
                } else {
                    $ubigeo = (new ModelsUbigeo())->getUbigeoQuery($this->db, '', '');
                }

                echo json_encode($ubigeo);
            } else if ($post['tipo'] == 'pais') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $ubigeo = (new ModelsUbigeo())->getUbigeo('', 'codubigeo AS id, descubigeo AS text', [], 'descubigeo LIKE "%' . $search . '%" AND (LENGTH(codubigeo) = 2 OR codubigeo LIKE "9%")', '');
                } else {
                    $ubigeo = (new ModelsUbigeo())->getUbigeo('', 'codubigeo AS id, descubigeo AS text', [], 'LENGTH(codubigeo) = 2 OR codubigeo LIKE "9%"', '');
                }

                echo json_encode($ubigeo);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
