<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Moneda;

class Monedas extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Activos Fijos';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autoCompletado()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new Moneda())->autoCompletado($busqueda);
        return $this->response->setJSON($items);
    }

    public function autocompletado_()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $moneda = (new Moneda())->getMoneda('', 'CodMoneda AS id, DescMoneda AS text', [], 'DescMoneda LIKE "%' . $search . '%"', '');
            } else {
                $moneda = (new Moneda())->getMoneda('', 'CodMoneda AS id, DescMoneda AS text', [], '', '');
            }

            echo json_encode($moneda);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
