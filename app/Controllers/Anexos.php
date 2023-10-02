<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;

class Anexos extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $anexoModel;

    public function __construct()
    {
        $this->page = 'Anexos';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->anexoModel = new Anexo();
    }

    public function autocompletado($IdAnexo, $TipoAnexo, $OtroDato)
    {
        try {
            $post = $this->request->getPost();

            if($OtroDato == 'null') $OtroDato = '';

            if (isset($post['search'])) {
                $search = $post['search'];

                $this->anexoModel = new Anexo();

                $anexo = $this->anexoModel->getAnexo($this->CodEmpresa, $IdAnexo, $TipoAnexo, $OtroDato, 'IdAnexo AS value, DescAnexo AS name', [], 'DescAnexo LIKE "%' . $search . '%"', '');
            } else {
                $this->anexoModel = new Anexo();

                $anexo = $this->anexoModel->getAnexo($this->CodEmpresa, $IdAnexo, $TipoAnexo, $OtroDato, 'IdAnexo AS value, DescAnexo AS name', [], '', '');
            }

            echo json_encode($anexo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletadoTipoOperacion()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new Anexo())->autoCompletado($busqueda, $this->request->getCookie('empresa'), '4');
        return $this->response->setJSON($items);
    }
}
