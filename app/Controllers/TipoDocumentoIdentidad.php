<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoDocumentoIdentidad as ModelsTipoDocumentoIdentidad;

class TipoDocumentoIdentidad extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Tipo Documento de Identidad';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if ($post['tipo'] == 'documento') {
                if (isset($post['CodTipoDoc']) && !empty($post['CodTipoDoc'])) {
                    $tipo_documento_identidad = (new ModelsTipoDocumentoIdentidad())->getTipoDocumentoIdentidad($post['CodTipoDoc'], 'CodTipoDoc AS id, DesDocumento AS text, TipoDato', [], '', '')[0];
                } else {
                    if (isset($post['search'])) {
                        $search = $post['search'];

                        $tipo_documento_identidad = (new ModelsTipoDocumentoIdentidad())->getTipoDocumentoIdentidad('', 'CodTipoDoc AS id, DesDocumento AS text, TipoDato', [], 'DesDocumento LIKE "%' . $search . '%"', '');
                    } else {
                        $tipo_documento_identidad = (new ModelsTipoDocumentoIdentidad())->getTipoDocumentoIdentidad('', 'CodTipoDoc AS id, DesDocumento AS text, TipoDato', [], '', '');
                    }
                }

                echo json_encode($tipo_documento_identidad);
            } else if ($post['tipo'] == 'banco') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $tipo_documento_identidad = (new ModelsTipoDocumentoIdentidad())->getTipoDocumentoIdentidad('', 'CodTipoDoc AS id, DesDocumento AS text, TipoDato', [], '(bcp IS NOT NULL OR bbva IS NOT NULL) AND DesDocumento LIKE "%' . $search . '%"', '');
                } else {
                    $tipo_documento_identidad = (new ModelsTipoDocumentoIdentidad())->getTipoDocumentoIdentidad('', 'CodTipoDoc AS id, DesDocumento AS text, TipoDato', [], 'bcp IS NOT NULL OR bbva IS NOT NULL', '');
                }

                echo json_encode($tipo_documento_identidad);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
