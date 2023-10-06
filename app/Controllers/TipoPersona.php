<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoPersona as ModelsTipoPersona;

class TipoPersona extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Tipo de Persona';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['CodTipPer']) && !empty($post['CodTipPer'])) {
                $tipo_persona = (new ModelsTipoPersona())->getTipoPersona($post['CodTipPer'], 'CodTipPer AS id, DescPer AS text', [], '', '')[0];
            } else {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $tipo_persona = (new ModelsTipoPersona())->getTipoPersona('', 'CodTipPer AS id, DescPer AS text', [], 'DescPer LIKE "%' . $search . '%"', '');
                } else {
                    $tipo_persona = (new ModelsTipoPersona())->getTipoPersona('', 'CodTipPer AS id, DescPer AS text', [], '', '');
                }
            }

            echo json_encode($tipo_persona);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
