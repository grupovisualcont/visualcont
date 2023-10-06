<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoComprobante as ModelsTipoComprobante;

class TipoComprobante extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Tipo Comprobante';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $tipo_comprobante = (new ModelsTipoComprobante())->getTipoComprobante('', 'CodComprobante AS id, CONCAT("(", CodComprobante, ") ", DescComprobante) AS text', [], 'DescComprobante LIKE "' . $search . '%"', 'DescComprobante ASC');
            } else {
                $tipo_comprobante = (new ModelsTipoComprobante())->getTipoComprobante('', 'CodComprobante AS id, CONCAT("(", CodComprobante, ") ", DescComprobante) AS text', [], '', 'DescComprobante ASC');
            }

            echo json_encode($tipo_comprobante);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
