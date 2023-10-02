<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoDocumentoIdentidad as ModelsTipoDocumentoIdentidad;

class TipoDocumentoIdentidad extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $tipoDocumentoIdentidadModel;

    public function __construct()
    {
        $this->page = 'Tipo Documento de Identidad';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->tipoDocumentoIdentidadModel = new ModelsTipoDocumentoIdentidad();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $this->tipoDocumentoIdentidadModel = new ModelsTipoDocumentoIdentidad();

                $tipo_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad('', 'CodTipoDoc AS value, DesDocumento AS name, TipoDato', [], 'DesDocumento LIKE "%' . $search . '%"', '');
            } else {
                $this->tipoDocumentoIdentidadModel = new ModelsTipoDocumentoIdentidad();

                $tipo_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad('', 'CodTipoDoc AS value, DesDocumento AS name, TipoDato', [], '', '');
            }

            echo json_encode($tipo_documento_identidad);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autocompletado_banco()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $this->tipoDocumentoIdentidadModel = new ModelsTipoDocumentoIdentidad();

                $tipo_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad('', 'CodTipoDoc AS value, DesDocumento AS name, TipoDato', [], '(bcp IS NOT NULL OR bbva IS NOT NULL) AND DesDocumento LIKE "%' . $search . '%"', '');
            } else {
                $this->tipoDocumentoIdentidadModel = new ModelsTipoDocumentoIdentidad();

                $tipo_documento_identidad = $this->tipoDocumentoIdentidadModel->getTipoDocumentoIdentidad('', 'CodTipoDoc AS value, DesDocumento AS name, TipoDato', [], 'bcp IS NOT NULL OR bbva IS NOT NULL', '');
            }

            echo json_encode($tipo_documento_identidad);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
