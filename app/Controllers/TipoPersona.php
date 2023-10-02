<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoPersona as ModelsTipoPersona;

class TipoPersona extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $tipoPersonaModel;

    public function __construct()
    {
        $this->page = 'Tipo de Persona';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->tipoPersonaModel = new ModelsTipoPersona();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $this->tipoPersonaModel = new ModelsTipoPersona();

                $tipo_persona = $this->tipoPersonaModel->getTipoPersona('', 'CodTipPer AS value, DescPer AS name', [], 'DescPer LIKE "%' . $search . '%"', '');
            } else {
                $this->tipoPersonaModel = new ModelsTipoPersona();

                $tipo_persona = $this->tipoPersonaModel->getTipoPersona('', 'CodTipPer AS value, DescPer AS name', [], '', '');
            }

            echo json_encode($tipo_persona);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
