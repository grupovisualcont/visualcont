<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Documento as ModelsDocumento;

class Documento extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Documento';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if(isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas'){
                if (isset($post['search'])) {
                    $search = $post['search'];
    
                    $documento = (new ModelsDocumento())->getDocumento($this->CodEmpresa, '', 'VE', 'documento.CodDocumento AS id, CONCAT(documento.CodDocumento, " - ", documento.DescDocumento) AS text, tc.TipoDatoS', [ array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left') ], 'CONCAT(documento.CodDocumento, " - ", documento.DescDocumento) LIKE "%' . $search . '%"', 'documento.DescDocumento ASC');
                } else {
                    $documento = (new ModelsDocumento())->getDocumento($this->CodEmpresa, '', 'VE', 'documento.CodDocumento AS id, CONCAT(documento.CodDocumento, " - ", documento.DescDocumento) AS text, tc.TipoDatoS', [ array('tabla' => 'tipocomprobante tc', 'on' => 'tc.CodComprobante = documento.CodSunat', 'tipo' => 'left') ], '', 'documento.DescDocumento ASC');
                }
            }else{
                if (isset($post['search'])) {
                    $search = $post['search'];
    
                    $documento = (new ModelsDocumento())->getDocumento($this->CodEmpresa, '', '', 'CodDocumento AS id, CONCAT(CodDocumento, " - ", DescDocumento) AS text', [], 'CONCAT(CodDocumento, " - ", DescDocumento) LIKE "%' . $search . '%"', '');
                } else {
                    $documento = (new ModelsDocumento())->getDocumento($this->CodEmpresa, '', '', 'CodDocumento AS id, CONCAT(CodDocumento, " - ", DescDocumento) AS text', [], '', '');
                }
            }

            echo json_encode($documento);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
