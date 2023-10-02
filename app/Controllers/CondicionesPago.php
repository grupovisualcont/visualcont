<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\CondicionPago;

class CondicionesPago extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $condicionPagoModel;
    protected $anexoModel;

    public function __construct()
    {
        $this->page = 'Condicion Pago';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->condicionPagoModel = new CondicionPago();
        $this->anexoModel = new Anexo();  
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->condicionPagoModel = new CondicionPago();

                $condicion_pago = $this->condicionPagoModel->getCondicionPago($this->CodEmpresa, '', '', [], '', '');

                return viewApp($this->page, 'app/mantenience/payment_condition/index', [
                    'condicion_pago' => $condicion_pago,
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
                $this->condicionPagoModel = new CondicionPago();

                $condicion_pago = $this->condicionPagoModel->getCondicionPago(
                    $this->CodEmpresa,
                    '',
                    'MAX(SUBSTRING(codcondpago, 3)) AS codigo',
                    [],
                    '',
                    ''
                );

                $codigo_maximo = 'CP000';

                if ($condicion_pago[0]['codigo']) {
                    if (strlen($condicion_pago[0]['codigo']) == 3) {
                        $codigo_maximo = 'CP00' . ($condicion_pago[0]['codigo'] + 1);
                    } else if (strlen($condicion_pago[0]['codigo']) == 2) {
                        $codigo_maximo = 'CP0' . ($condicion_pago[0]['codigo'] + 1);
                    } else {
                        $codigo_maximo = 'CP' . ($condicion_pago[0]['codigo'] + 1);
                    }
                }

                $this->anexoModel = new Anexo();

                $tipo_condicion = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 106, '', '', '', '');

                $options_tipo_condicion = '';

                foreach ($tipo_condicion as $indice => $valor) {
                    $selected = '';

                    if ($valor['DescAnexo'] == 'Contado') $selected = 'selected';

                    $options_tipo_condicion .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estado = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 1, '', '', '', '');

                $options_estado = '';

                foreach ($estado as $indice => $valor) {
                    $selected = '';

                    if ($valor['DescAnexo'] == 'Activo') $selected = 'selected';

                    $options_estado .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/payment_condition/create.js']);

                return viewApp($this->page, 'app/mantenience/payment_condition/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'options_tipo_condicion' => $options_tipo_condicion,
                    'options_estado' => $options_estado,
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

    public function edit($codcondpago)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->condicionPagoModel = new CondicionPago();

                $condicion_pago = $this->condicionPagoModel->getCondicionPago($this->CodEmpresa, $codcondpago, '', [], '', '')[0];

                $this->anexoModel = new Anexo();

                $tipo_condicion = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 106, '', '', '', '');

                $options_tipo_condicion = '';

                foreach ($tipo_condicion as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $condicion_pago['Tipo']) $selected = 'selected';

                    $options_tipo_condicion .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estado = $this->anexoModel->getAnexo($this->CodEmpresa, 0, 1, '', '', '', '');

                $options_estado = '';

                foreach ($estado as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $condicion_pago['Estado']) $selected = 'selected';

                    $options_estado .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/payment_condition/edit.js']);

                return viewApp($this->page, 'app/mantenience/payment_condition/edit', [
                    'condicion_pago' => $condicion_pago,
                    'options_tipo_condicion' => $options_tipo_condicion,
                    'options_estado' => $options_estado,
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

            $post['desccondpago'] = strtoupper(trim($post['desccondpago']));
            $post['Ndias'] = !empty($post['Ndias']) ? $post['Ndias'] : 0;
            $post['carga_inicial'] = 0;

            if ($post['Tipo'] == 168) {
                $post['con_cre'] = 'CP002';
            } else {
                $post['con_cre'] = 'CP001';
            }

            $this->condicionPagoModel = new CondicionPago();

            $existe_codigo = $this->condicionPagoModel->getCondicionPago($post['CodEmpresa'], $post['codcondpago'], '', [], '', '');

            if (count($existe_codigo) == 0) {
                $this->condicionPagoModel = new CondicionPago();

                $this->condicionPagoModel->agregar($post);
            }

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

            return redirect()->to(base_url('app/mantenience/payment_condition/index'));
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

            $post['desccondpago'] = strtoupper(trim($post['desccondpago']));
            $post['Ndias'] = !empty($post['Ndias']) ? $post['Ndias'] : 0;
            $post['carga_inicial'] = 0;

            if ($post['Tipo'] == 168) {
                $post['con_cre'] = 'CP002';
            } else {
                $post['con_cre'] = 'CP001';
            }

            $this->condicionPagoModel = new CondicionPago();

            $this->condicionPagoModel->actualizar($post['CodEmpresa'], $post['codcondpago'], $post);

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

            return redirect()->to(base_url('app/mantenience/payment_condition/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($codcondpago)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->condicionPagoModel = new CondicionPago();

            $this->condicionPagoModel->eliminar($this->CodEmpresa, $codcondpago);

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

            return redirect()->to(base_url('app/mantenience/payment_condition/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Condición de Pago - Reporte');

            $columnas = array('Código', 'Descripción', 'Comentario');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->condicionPagoModel = new CondicionPago();

            $result = $this->condicionPagoModel->getCondicionPago($this->CodEmpresa, '', '', [], '', '');

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['codcondpago'],
                    $valor['desccondpago'],
                    $valor['comentario']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('condicion_de_pago_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $this->condicionPagoModel = new CondicionPago();

            $result = $this->condicionPagoModel->getCondicionPago($this->CodEmpresa, '', '', [], '', '');

            $columnas = array('Código', 'Descripción', 'Comentario');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['codcondpago'] . '</td>
                    <td align="left">' . $valor['desccondpago'] . '</td>
                    <td align="left">' . $valor['comentario'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('condicion_de_pago_reporte');
            $pdf->creacion('Condición de Pago - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado()
    {
        $busqueda = $this->request->getGet('search');
        $items = (new CondicionPago())->autoCompletado($busqueda, $this->request->getCookie('empresa'));
        return $this->response->setJSON($items);
    }
}
