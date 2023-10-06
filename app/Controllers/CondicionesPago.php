<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\CondicionPago;

class CondicionesPago extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Condicion Pago';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', '', [], '', '');

                return viewApp($this->page, 'app/mantenience/payment_condition/index', [
                    'condicion_pago' => $condicion_pago,
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
                $condicion_pago = (new CondicionPago())->getCondicionPago(
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

                $tipo_condicion = (new Anexo())->getAnexo($this->CodEmpresa, 0, 106, '', '', [], 'CodInterno = 1', '')[0];

                $option_tipo_condicion = '<option value="' . $tipo_condicion['IdAnexo'] . '">' . $tipo_condicion['DescAnexo'] . '</option>';

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 1, '', '', [], 'CodInterno = 1', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script('', ['app/mantenience/payment_condition/create.js']);

                return viewApp($this->page, 'app/mantenience/payment_condition/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'option_tipo_condicion' => $option_tipo_condicion,
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

    public function edit($codcondpago)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $condicion_pago = (new CondicionPago())->getCondicionPago($this->CodEmpresa, $codcondpago, '', [], '', '')[0];

                $tipo_condicion = (new Anexo())->getAnexo($this->CodEmpresa, $condicion_pago['Tipo'], 106, '', '', [], '', '')[0];

                $option_tipo_condicion = '<option value="' . $tipo_condicion['IdAnexo'] . '">' . $tipo_condicion['DescAnexo'] . '</option>';

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, $condicion_pago['Estado'], 1, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script('', ['app/mantenience/payment_condition/edit.js']);

                return viewApp($this->page, 'app/mantenience/payment_condition/edit', [
                    'condicion_pago' => $condicion_pago,
                    'option_tipo_condicion' => $option_tipo_condicion,
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

            $post['desccondpago'] = strtoupper(trim($post['desccondpago']));
            $post['Ndias'] = !empty($post['Ndias']) ? $post['Ndias'] : 0;
            $post['carga_inicial'] = 0;

            if ($post['Tipo'] == 168) {
                $post['con_cre'] = 'CP002';
            } else {
                $post['con_cre'] = 'CP001';
            }

            $existe_codigo = (new CondicionPago())->getCondicionPago($post['CodEmpresa'], $post['codcondpago'], '', [], '', '');

            if (count($existe_codigo) == 0) {
                (new CondicionPago())->agregar($post);
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

            (new CondicionPago())->actualizar($post['CodEmpresa'], $post['codcondpago'], $post);

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

            (new CondicionPago())->eliminar($this->CodEmpresa, $codcondpago);

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

            $result = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', '', [], '', '');

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
            $result = (new CondicionPago())->getCondicionPago($this->CodEmpresa, '', '', [], '', '');

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
