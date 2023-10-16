<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoPago;

class TipoPagos extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Tipo Pago';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $documento = (new TipoPago())->getTipoPago('', 'CodTipoPago AS id, CONCAT(CodTipoPago, " - ", DescTipoPago) AS text', [], 'CONCAT(CodTipoPago, " - ", DescTipoPago) LIKE "%' . $search . '%"', 'CodTipoPago ASC');
                } else {
                    $documento = (new TipoPago())->getTipoPago('', 'CodTipoPago AS id, CONCAT(CodTipoPago, " - ", DescTipoPago) AS text', [], '', 'CodTipoPago ASC');
                }
            }

            echo json_encode($documento);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
