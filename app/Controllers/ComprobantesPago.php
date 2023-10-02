<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\ClaseDoc;
use App\Models\Documento;
use App\Models\TipoComprobante;

class ComprobantesPago extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $documentoModel;
    protected $claseDocModel;
    protected $tipoComprobanteModel;
    protected $anexoModel;

    public function __construct()
    {
        $this->page = 'Comprobantes de Pago';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->documentoModel = new Documento();
        $this->claseDocModel = new ClaseDoc();
        $this->tipoComprobanteModel = new TipoComprobante();
        $this->anexoModel = new Anexo();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->documentoModel = new Documento();

                $documentos = $this->documentoModel->getDocumento(
                    $this->CodEmpresa,
                    '',
                    'origen = "VE" OR origen = "CO"',
                    [
                        array('tabla' => 'clasedoc cl', 'on' => 'cl.CodClaseDoc = documento.CodClaseDoc', 'tipo' => 'left')
                    ],
                    'documento.*, cl.DescClaseDoc, IF(origen = "VE", "Venta", "Compra") AS Tipo',
                    '',
                    'origen, CodDocumento ASC'
                );

                return viewApp($this->page, 'app/mantenience/payment_vouchers/index', [
                    'documentos' => $documentos,
                    'typeOrder' => 'string'
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->claseDocModel = new ClaseDoc();

                $clases_documento = $this->claseDocModel->getClaseDoc('', [], '', '');

                $options_clases_documento = '';

                foreach ($clases_documento as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodClaseDoc'] == 'FACT') $selected = 'selected';

                    $options_clases_documento .= '<option value="' . $valor['CodClaseDoc'] . '" ' . $selected . '>' . $valor['DescClaseDoc'] . '</option>';
                }

                $this->tipoComprobanteModel = new TipoComprobante();

                $tipos_comprobantes = $this->tipoComprobanteModel->getTipoComprobante([], '', '', 'DescComprobante ASC');

                $options_tipos_comprobantes = '';

                foreach ($tipos_comprobantes as $indice => $valor) {
                    $options_tipos_comprobantes .= '<option value="' . $valor['CodComprobante'] . '">' . '(' . $valor['CodComprobante'] . ') ' . $valor['DescComprobante'] . '</option>';
                }

                $origen = array('VE' => 'Venta', 'CO' => 'Compra');

                $options_origen = '';

                foreach ($origen as $indice => $valor) {
                    $selected = '';

                    if ($indice == 'VE') $selected = 'selected';

                    $options_origen .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $van_al_registro_de = array('N' => 'Ninguno', 'V' => 'Venta', 'C' => 'Compra');

                $options_van_al_registro_de = '';

                foreach ($van_al_registro_de as $indice => $valor) {
                    $selected = '';

                    if ($indice == 'N') $selected = 'selected';

                    $options_van_al_registro_de .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, '', 1, '', '', '', '');

                $options_estados = '';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($valor['DescAnexo'] == 'Activo') $selected = 'selected';

                    $options_estados .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/payment_vouchers/create.js']);

                return viewApp($this->page, 'app/mantenience/payment_vouchers/create', [
                    'options_clases_documento' => $options_clases_documento,
                    'options_tipos_comprobantes' => $options_tipos_comprobantes,
                    'options_origen' => $options_origen,
                    'options_van_al_registro_de' => $options_van_al_registro_de,
                    'options_estados' => $options_estados,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($CodDocumento)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->documentoModel = new Documento();

                $documento = $this->documentoModel->getDocumento(
                    $this->CodEmpresa,
                    $CodDocumento,
                    '',
                    [
                        array('tabla' => 'clasedoc cl', 'on' => 'cl.CodClaseDoc = documento.CodClaseDoc', 'tipo' => 'left')
                    ],
                    'documento.*, cl.DescClaseDoc',
                    '',
                    ''
                )[0];

                $this->claseDocModel = new ClaseDoc();

                $clases_documento = $this->claseDocModel->getClaseDoc('', [], '', '');

                $options_clases_documento = '';

                foreach ($clases_documento as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodClaseDoc'] == $documento['CodClaseDoc']) $selected = 'selected';

                    $options_clases_documento .= '<option value="' . $valor['CodClaseDoc'] . '" ' . $selected . '>' . $valor['DescClaseDoc'] . '</option>';
                }

                $this->tipoComprobanteModel = new TipoComprobante();

                $tipos_comprobantes = $this->tipoComprobanteModel->getTipoComprobante([], '', '', 'DescComprobante ASC');

                $options_tipos_comprobantes = '';

                foreach ($tipos_comprobantes as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodComprobante'] == $documento['CodSunat']) $selected = 'selected';

                    $options_tipos_comprobantes .= '<option value="' . $valor['CodComprobante'] . '" ' . $selected . '>' . '(' . $valor['CodComprobante'] . ') ' . $valor['DescComprobante'] . '</option>';
                }

                $origen = array('VE' => 'Venta', 'CO' => 'Compra');

                $options_origen = '';

                foreach ($origen as $indice => $valor) {
                    $selected = '';

                    if ($indice == $documento['origen']) $selected = 'selected';

                    $options_origen .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $van_al_registro_de = array('N' => 'Ninguno', 'V' => 'Venta', 'C' => 'Compra');

                $options_van_al_registro_de = '';

                foreach ($van_al_registro_de as $indice => $valor) {
                    $selected = '';

                    if ($indice == $documento['vanalRegistrode']) $selected = 'selected';

                    $options_van_al_registro_de .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, '', 1, '', '', '', '');

                $options_estados = '';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodInterno'] == $documento['Estado']) $selected = 'selected';

                    $options_estados .= '<option value="' . $valor['CodInterno'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = "
                    var documento_CodDocumento = '" . $documento['CodDocumento'] . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/payment_vouchers/edit.js']);

                return viewApp($this->page, 'app/mantenience/payment_vouchers/edit', [
                    'documento' => $documento,
                    'options_clases_documento' => $options_clases_documento,
                    'options_tipos_comprobantes' => $options_tipos_comprobantes,
                    'options_origen' => $options_origen,
                    'options_van_al_registro_de' => $options_van_al_registro_de,
                    'options_estados' => $options_estados,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function save()
    {
        try {
            $post = $this->request->getPost();

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $post['CodDocumento'] = strtoupper(trim($post['CodDocumento']));
            $post['DescDocumento'] = strtoupper(trim($post['DescDocumento']));


            switch (strlen($post['Numero'])) {
                case 1:
                    $post['Numero'] = '000000' . $post['Numero'];

                    break;
                case 2:
                    $post['Numero'] = '00000' . $post['Numero'];

                    break;
                case 3:
                    $post['Numero'] = '0000' . $post['Numero'];

                    break;
                case 4:
                    $post['Numero'] = '000' . $post['Numero'];

                    break;
                case 5:
                    $post['Numero'] = '00' . $post['Numero'];

                    break;
                case 6:
                    $post['Numero'] = '0' . $post['Numero'];

                    break;
                case 7:
                    $post['Numero'] = $post['Numero'];
                    break;
            }

            $this->documentoModel = new Documento();

            $this->documentoModel->agregar($post);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/payment_vouchers/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update()
    {
        try {
            $post = $this->request->getPost();

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $post['CodDocumento'] = strtoupper(trim($post['CodDocumento']));
            $post['DescDocumento'] = strtoupper(trim($post['DescDocumento']));


            switch (strlen($post['Numero'])) {
                case 1:
                    $post['Numero'] = '000000' . $post['Numero'];

                    break;
                case 2:
                    $post['Numero'] = '00000' . $post['Numero'];

                    break;
                case 3:
                    $post['Numero'] = '0000' . $post['Numero'];

                    break;
                case 4:
                    $post['Numero'] = '000' . $post['Numero'];

                    break;
                case 5:
                    $post['Numero'] = '00' . $post['Numero'];

                    break;
                case 6:
                    $post['Numero'] = '0' . $post['Numero'];

                    break;
                case 7:
                    $post['Numero'] = $post['Numero'];
                    break;
            }

            $this->documentoModel = new Documento();

            $this->documentoModel->actualizar($post['CodEmpresa'], $post['CodDocumento'], $post);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/payment_vouchers/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($CodDocumento)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->documentoModel = new Documento();

            $this->documentoModel->eliminar($this->CodEmpresa, $CodDocumento);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();

                $result = false;
            } else {
                $this->db->transCommit();

                $result = true;
            }

            if ($result) {
                $_SESSION['code'] = 'success';
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/payment_vouchers/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Comprobantes de Pago - Reporte');

            $columnas = array('Tipo', 'Código', 'Documento', 'Clase', 'Serie', 'Numero');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->documentoModel = new Documento();

            $result = $this->documentoModel->getDocumento(
                $this->CodEmpresa,
                '',
                'origen = "VE" OR origen = "CO"',
                [
                    array('tabla' => 'clasedoc cl', 'on' => 'cl.CodClaseDoc = documento.CodClaseDoc', 'tipo' => 'left')
                ],
                'documento.*, cl.DescClaseDoc, IF(origen = "VE", "Venta", "Compra") AS Tipo',
                '',
                'origen, CodDocumento ASC'
            );

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['Tipo'],
                    $valor['CodDocumento'],
                    $valor['DescDocumento'],
                    $valor['DescClaseDoc'],
                    $valor['Serie'],
                    $valor['Numero']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('comprobantes_pago_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $this->documentoModel = new Documento();

            $result = $this->documentoModel->getDocumento(
                $this->CodEmpresa,
                '',
                'origen = "VE" OR origen = "CO"',
                [
                    array('tabla' => 'clasedoc cl', 'on' => 'cl.CodClaseDoc = documento.CodClaseDoc', 'tipo' => 'left')
                ],
                'documento.*, cl.DescClaseDoc, IF(origen = "VE", "Venta", "Compra") AS Tipo',
                '',
                'origen, CodDocumento ASC'
            );

            $columnas = array('Tipo', 'Código', 'Documento', 'Clase', 'Serie', 'Numero');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['Tipo'] . '</td>
                    <td align="left">' . $valor['CodDocumento'] . '</td>
                    <td align="left">' . $valor['DescDocumento'] . '</td>
                    <td align="left">' . $valor['DescClaseDoc'] . '</td>
                    <td align="left">' . $valor['Serie'] . '</td>
                    <td align="left">' . $valor['Numero'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('comprobantes_pago_reporte');
            $pdf->creacion('Comprobantes de Pago - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_codigo()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'nuevo') {
                $CodDocumento = strtoupper(trim(strval($this->request->getPost('CodDocumento'))));

                $this->documentoModel = new Documento();

                $documentos = $this->documentoModel->getDocumento($this->CodEmpresa, $CodDocumento, '', [], '', '', '');

                $existe = array('existe' => false);

                if (count($documentos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            } else if ($tipo == 'editar') {
                $CodDocumento = strtoupper(trim(strval($this->request->getPost('CodDocumento'))));
                $NotCodDocumento = strtoupper(trim(strval($this->request->getPost('NotCodDocumento'))));

                $this->documentoModel = new Documento();

                $documentos = $this->documentoModel->getDocumento($this->CodEmpresa, $CodDocumento, '', [], '', 'UPPER(CodDocumento) != "' . $NotCodDocumento . '"', '');

                $existe = array('existe' => false);

                if (count($documentos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
