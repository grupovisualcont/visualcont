<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\CentroCosto as ModelsCentroCosto;

class CentroCosto extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Centro de Costo';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $centro_costo = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, '', 0, '', [], '', '', '');

                return viewApp($this->page, 'app/mantenience/cost_center/index', [
                    'centro_costo' => $centro_costo,
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
                $codigo_maximo = (new ModelsCentroCosto())->getCentroCosto(
                    $this->CodEmpresa,
                    '',
                    0,
                    'CAST(MAX(SUBSTRING(CodcCosto, 3)) AS UNSIGNED) AS CodcCosto',
                    [],
                    'LENGTH(CodcCosto) = 6',
                    '',
                    ''
                );

                switch (strlen($codigo_maximo[0]['CodcCosto'])) {
                    case 1:
                        $codigo_maximo = 'CC000' . ($codigo_maximo[0]['CodcCosto'] + 1);

                        break;
                    case 2:
                        $codigo_maximo = 'CC00' . ($codigo_maximo[0]['CodcCosto'] + 1);

                        break;
                    case 3:
                        $codigo_maximo = 'CC0' . ($codigo_maximo[0]['CodcCosto'] + 1);

                        break;
                    case 4:
                        $codigo_maximo = 'CC' . ($codigo_maximo[0]['CodcCosto'] + 1);

                        break;
                }

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 11, 1, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script('', ['app/mantenience/cost_center/create.js']);

                return viewApp($this->page, 'app/mantenience/cost_center/create', [
                    'codigo_maximo' => $codigo_maximo,
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

    public function edit($CodcCosto)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $centro_costo = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, $CodcCosto, 0, '', [], '', '', '')[0];

                $option_centro_costo_superior = '';

                if (strlen($centro_costo['CodcCosto']) > 6) {
                    $codigo_inferior = substr($centro_costo['CodcCosto'], 0, strlen($centro_costo['CodcCosto']) - 3);

                    $centro_costo_superior = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, $codigo_inferior, 0, '', [], '', '', '')[0];

                    $option_centro_costo_superior = '<option value="' . $centro_costo_superior['CodcCosto'] . '">' . $centro_costo_superior['CodcCosto'] . ' - ' . $centro_costo_superior['DesccCosto'] . '</option>';
                }

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, $centro_costo['Estado'], 1, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $script = (new Empresa())->generar_script('', ['app/mantenience/cost_center/edit.js']);

                return viewApp($this->page, 'app/mantenience/cost_center/edit', [
                    'centro_costo' => $centro_costo,
                    'option_centro_costo_superior' => $option_centro_costo_superior,
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

            $post['DesccCosto'] = strtoupper(trim($post['DesccCosto']));

            $existe_codigo = (new ModelsCentroCosto())->getCentroCosto($post['CodEmpresa'], $post['CodcCosto'], 0, '', [], '', '', '');

            if (count($existe_codigo) == 0) {
                (new ModelsCentroCosto())->agregar($post);
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

            return redirect()->to(base_url('app/mantenience/cost_center/index'));
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

            $post['DesccCosto'] = strtoupper(trim($post['DesccCosto']));

            (new ModelsCentroCosto())->actualizar($post['CodEmpresa'], $post['CodcCosto'], $post);

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

            return redirect()->to(base_url('app/mantenience/cost_center/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($CodcCosto)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            (new ModelsCentroCosto())->eliminar($this->CodEmpresa, $CodcCosto);

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

            return redirect()->to(base_url('app/mantenience/cost_center/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Centro de Costo - Reporte');

            $columnas = array('Código', 'Descripción', 'Porcentaje');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $result = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, '', 0, '', [], '', '', '');

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['CodcCosto'],
                    $valor['DesccCosto'],
                    $valor['Porcentaje']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('centro_costo_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $result = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, '', 0, '', [], '', '', '');

            $columnas = array('Código', 'Descripción', 'Porcentaje');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['CodcCosto'] . '</td>
                    <td align="left">' . $valor['DesccCosto'] . '</td>
                    <td align="left">' . $valor['Porcentaje'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('centro_costo_reporte');
            $pdf->creacion('Centro de Costo - Reporte', $tr, '', 'A3', true);
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
                $CodcCosto = strtoupper(trim(strval($this->request->getPost('CodcCosto'))));

                $centro_costo = (new ModelsCentroCosto())->getCentroCosto(
                    $this->CodEmpresa,
                    '',
                    0,
                    'CAST(MAX(SUBSTRING(CodcCosto, IF(LENGTH(CodcCosto) = 6, 3, LENGTH(CodcCosto) - 2))) AS UNSIGNED) AS CodcCosto',
                    [],
                    'LENGTH(CodcCosto) = ' . (strlen($CodcCosto) + 3),
                    '',
                    ''
                );

                $datos = array('codigo' => $CodcCosto . '001');

                if ($centro_costo[0]['CodcCosto'] != NULL) {
                    $centro_costo[0]['CodcCosto'] = $centro_costo[0]['CodcCosto'] + 1;

                    switch (strlen($centro_costo[0]['CodcCosto'])) {
                        case 1:
                            $CodcCosto .= '00' . $centro_costo[0]['CodcCosto'];

                            break;
                        case 2:
                            $CodcCosto .= '0' . $centro_costo[0]['CodcCosto'];

                            break;
                        case 3:
                            $CodcCosto .= $centro_costo[0]['CodcCosto'];

                            break;
                    }

                    $datos = array('codigo' => $CodcCosto);
                }

                echo json_encode($datos);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $centro_costo = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, '', $post['Estado'] ?? 0, 'CodcCosto AS id, CONCAT(CodcCosto, " - ", DesccCosto) AS text', [], 'DesccCosto LIKE "%' . $search . '%"', '', '');
            } else {
                $centro_costo = (new ModelsCentroCosto())->getCentroCosto($this->CodEmpresa, '', $post['Estado'] ?? 0, 'CodcCosto AS id, CONCAT(CodcCosto, " - ", DesccCosto) AS text', [], '', '', '');
            }

            echo json_encode($centro_costo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
