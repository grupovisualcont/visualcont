<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;

class Anexos extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Anexos';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas') {
                $Value = $post['Value'] ?? 'IdAnexo';
                $whereCodInterno = isset($post['CodInterno']) && !empty($post['CodInterno']) ? 'CodInterno = ' . $post['CodInterno'] : '';

                if (isset($post['search'])) {
                    $search = $post['search'];

                    $anexo = (new Anexo())->getAnexo($this->CodEmpresa, $post['IdAnexo'], $post['TipoAnexo'], $post['OtroDato'], $Value . ' AS id, DescAnexo AS text, CodInterno', [], 'DescAnexo LIKE "%' . $search . '%"', '');
                } else {
                    $anexo = (new Anexo())->getAnexo($this->CodEmpresa, $post['IdAnexo'], $post['TipoAnexo'], $post['OtroDato'], $Value . ' AS id, DescAnexo AS text, CodInterno', [], $whereCodInterno, '');
                }
            } else if (isset($post['CodEFE']) && !empty($post['CodEFE'])) {
                $option_operacion = array();
                $option_financiamento = array();
                $option_inversion = array();

                $search = isset($post['search']) ? ' AND DescAnexo LIKE "%' . $post['search'] . '%"' : '';

                $operaciones = (new Anexo())->getAnexo($this->CodEmpresa, 0, 0, '', 'IdAnexo AS id, DescAnexo AS text', [], 'CodInterno >= 101 AND CodInterno <= 109' . $search, '');

                $option_operacion[] = array('id' => "0", 'text' => 'Operación', 'disabled' => true, 'class' => 'background-readonly h5 text-black');

                $anexos = array_merge($option_operacion, $operaciones);

                $financiamientos = (new Anexo())->getAnexo($this->CodEmpresa, 0, 0, '', 'IdAnexo AS id, DescAnexo AS text', [], 'CodInterno >= 201 AND CodInterno <= 206' . $search, '');

                $option_financiamento[] = array('id' => "0", 'text' => 'Financiamiento', 'disabled' => true, 'class' => 'background-readonly h5 text-black');

                $inversiones = (new Anexo())->getAnexo($this->CodEmpresa, 0, 0, '', 'IdAnexo AS id, DescAnexo AS text', [], 'CodInterno >= 301 AND CodInterno <= 308' . $search, '');

                $option_inversion[] = array('id' => "0", 'text' => 'Inversión', 'disabled' => true, 'class' => 'background-readonly h5 text-black');

                $anexo = array_merge($option_operacion, $operaciones, $option_financiamento, $financiamientos, $option_inversion, $inversiones);
            } else if (isset($post['DescAnexo']) && !empty($post['DescAnexo'])) {
                $anexo = (new Anexo())->getAnexo($this->CodEmpresa, $post['IdAnexo'], $post['TipoAnexo'], $post['OtroDato'], 'IdAnexo AS id, DescAnexo AS text', [], 'DescAnexo = "' . $post['DescAnexo'] . '"', '');
            } else {
                $Value = $post['Value'] ?? 'IdAnexo';

                if (isset($post['search'])) {
                    $search = $post['search'];

                    $anexo = (new Anexo())->getAnexo($this->CodEmpresa, $post['IdAnexo'], $post['TipoAnexo'], $post['OtroDato'], $Value . ' AS id, DescAnexo AS text', [], 'DescAnexo LIKE "%' . $search . '%"', '');
                } else {
                    $anexo = (new Anexo())->getAnexo($this->CodEmpresa, $post['IdAnexo'], $post['TipoAnexo'], $post['OtroDato'], $Value . ' AS id, DescAnexo AS text', [], '', '');
                }
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
