<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Detraccion;

class Detracciones extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Detraccion';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if(isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas'){
                $text = $post['text'] ?? 'DescMoneda';

                if (isset($post['search'])) {
                    $search = $post['search'];
    
                    $detraccion = (new Detraccion())->getDetraccion($this->CodEmpresa, 0, 'IdDetraccion AS id, CONCAT(Tasa, "% - ", DescDetra) AS text', [], 'CONCAT(Tasa, "% - ", DescDetra) LIKE "%' . $search . '%"', '');
                } else {
                    $detraccion = (new Detraccion())->getDetraccion($this->CodEmpresa, 0, 'IdDetraccion AS id, CONCAT(Tasa, "% - ", DescDetra) AS text', [], '', '');
                }
            }
            
            echo json_encode($detraccion);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
