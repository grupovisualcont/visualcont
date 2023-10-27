<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\Web\Ubigeo;

class UbigeoController extends BaseController
{
    private $db;

    public function __construct()
    {

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        //
    }

    public function autoCompletado()
    {
        try {
            $this->db = \Config\Database::connect();

            $post = $this->request->getPost();

            if ($post['tipo'] == 'ubigeo') {
                if (isset($post['search'])) {
                    $search = $post['search'];
                    $ubigeo = (new Ubigeo())->getUbigeoQuery($this->db, '', $search);
                } else {
                    $ubigeo = (new Ubigeo())->getUbigeoQuery($this->db, '', '');
                }

                echo json_encode($ubigeo);
            } else if ($post['tipo'] == 'pais') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $ubigeo = (new Ubigeo())->getUbigeo('', 'codubigeo AS id, descubigeo AS text', [], 'descubigeo LIKE "%' . $search . '%" AND (LENGTH(codubigeo) = 2 OR codubigeo LIKE "9%")', '');
                } else {
                    $ubigeo = (new Ubigeo())->getUbigeo('', 'codubigeo AS id, descubigeo AS text', [], 'LENGTH(codubigeo) = 2 OR codubigeo LIKE "9%"', '');
                }

                echo json_encode($ubigeo);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
