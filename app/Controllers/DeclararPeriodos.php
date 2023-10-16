<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DeclararPeriodos extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Declarar Periodo';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if(isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas'){
                $periodo = array();
    
                $time = strtotime(date('Y-m-d'));
    
                if (isset($post['search'])) {
                    $search = $post['search'];
    
                    for ($i = 0; $i < 34; $i++) {
                        $final = date('Y-m', strtotime('+' . ($i + 1) . ' month', $time));
    
                        if (mb_strpos($final, $search) !== false) $periodo[] = array('id' => $final, 'text' => $final);
                    }
                } else {
                    for ($i = 0; $i < 34; $i++) {
                        $final = date('Y-m', strtotime('+' . ($i + 1) . ' month', $time));
    
                        $periodo[] = array('id' => $final, 'text' => $final);
                    }
                }
            }
            
            echo json_encode($periodo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
