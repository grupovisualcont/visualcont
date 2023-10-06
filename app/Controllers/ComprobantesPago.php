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
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Comprobantes de Pago';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $documentos = (new Documento())->getDocumento(
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
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $clase_documento = (new ClaseDoc())->getClaseDoc('FACT', '', [], '', '')[0];

                $option_clase_documento = '<option value="' . $clase_documento['CodClaseDoc'] . '">' . $clase_documento['DescClaseDoc'] . '</option>';

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

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 11, 0, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script('', ['app/mantenience/payment_vouchers/create.js']);

                return viewApp($this->page, 'app/mantenience/payment_vouchers/create', [
                    'option_clase_documento' => $option_clase_documento,
                    'options_origen' => $options_origen,
                    'options_van_al_registro_de' => $options_van_al_registro_de,
                    'option_estado' => $option_estado,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($CodDocumento)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $documento = (new Documento())->getDocumento(
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

                $clase_documento = (new ClaseDoc())->getClaseDoc($documento['CodClaseDoc'], '', [], '', '')[0];

                $option_clase_documento = '<option value="' . $clase_documento['CodClaseDoc'] . '">' . $clase_documento['DescClaseDoc'] . '</option>';

                $tipo_comprobante = (new TipoComprobante())->getTipoComprobante($documento['CodSunat'], '', [], '', 'DescComprobante ASC')[0];

                $option_tipo_comprobante = '<option value="' . $tipo_comprobante['CodComprobante'] . '">' . '(' . $tipo_comprobante['CodComprobante'] . ') ' . $tipo_comprobante['DescComprobante'] . '</option>';

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

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 1, '', '', [], 'CodInterno = ' . $documento['Estado'], '')[0];

                $option_estado = '<option value="' . $estado['CodInterno'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = "
                    var documento_CodDocumento = '" . $documento['CodDocumento'] . "';
                ";

                $script = (new Empresa())->generar_script($script, ['app/mantenience/payment_vouchers/edit.js']);

                return viewApp($this->page, 'app/mantenience/payment_vouchers/edit', [
                    'documento' => $documento,
                    'option_clase_documento' => $option_clase_documento,
                    'option_tipo_comprobante' => $option_tipo_comprobante,
                    'options_origen' => $options_origen,
                    'options_van_al_registro_de' => $options_van_al_registro_de,
                    'option_estado' => $option_estado,
                    'typeOrder' => 'string',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
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

            (new Documento())->agregar($post);

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

            (new Documento())->actualizar($post['CodEmpresa'], $post['CodDocumento'], $post);

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

            (new Documento())->eliminar($this->CodEmpresa, $CodDocumento);

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

            $result = (new Documento())->getDocumento(
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
            $result = (new Documento())->getDocumento(
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

                $documentos = (new Documento())->getDocumento($this->CodEmpresa, $CodDocumento, '', [], '', '', '');

                $existe = array('existe' => false);

                if (count($documentos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            } else if ($tipo == 'editar') {
                $CodDocumento = strtoupper(trim(strval($this->request->getPost('CodDocumento'))));
                $NotCodDocumento = strtoupper(trim(strval($this->request->getPost('NotCodDocumento'))));

                $documentos = (new Documento())->getDocumento($this->CodEmpresa, $CodDocumento, '', [], '', 'UPPER(CodDocumento) != "' . $NotCodDocumento . '"', '');

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
