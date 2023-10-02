<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Anexo;
use App\Models\CentroCosto as ModelsCentroCosto;

class CentroCosto extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $centroCostoModel;
    protected $anexoModel;

    public function __construct()
    {
        $this->page = 'Centro de Costo';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->centroCostoModel = new ModelsCentroCosto();
        $this->anexoModel = new Anexo();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->centroCostoModel = new ModelsCentroCosto();

                $centro_costo = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', '', '', '');

                return viewApp($this->page, 'app/mantenience/cost_center/index', [
                    'centro_costo' => $centro_costo,
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
                $this->centroCostoModel = new ModelsCentroCosto();

                $centro_costo_superior = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', 'Estado = 11', '', 'CodcCosto ASC');

                $options_centro_costo_superior = '<option value="" disabled selected>Seleccione</option>';

                $descripcion = '';

                foreach ($centro_costo_superior as $indice => $valor) {
                    if (strlen($valor['CodcCosto']) > 6) {
                        $descripcion .= ' \\ ' . $valor['DesccCosto'];
                    } else {
                        $descripcion = $valor['DesccCosto'];
                    }

                    $options_centro_costo_superior .= '<option value="' . $valor['CodcCosto'] . '">' . $descripcion . '</option>';
                }

                $this->centroCostoModel = new ModelsCentroCosto();

                $codigo_maximo = $this->centroCostoModel->getCentroCosto(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'CAST(MAX(SUBSTRING(CodcCosto, 3)) AS UNSIGNED) AS CodcCosto',
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

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, '', 1, '', '', '', '');

                $options_estados = '';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($valor['DescAnexo'] == 'Activo') $selected = 'selected';

                    $options_estados .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/cost_center/create.js']);

                return viewApp($this->page, 'app/mantenience/cost_center/create', [
                    'options_centro_costo_superior' => $options_centro_costo_superior,
                    'codigo_maximo' => $codigo_maximo,
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

    public function edit($CodcCosto)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->centroCostoModel = new ModelsCentroCosto();

                $centro_costo = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, $CodcCosto, '', [], '', '', '', '')[0];

                $this->centroCostoModel = new ModelsCentroCosto();

                $centro_costo_superior = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', 11, [], '', '', '', 'CodcCosto ASC');

                $codigo_inferior = substr($centro_costo['CodcCosto'], 0, strlen($centro_costo['CodcCosto']) - 3);

                $options_centro_costo_superior = '';

                $descripcion = '';

                foreach ($centro_costo_superior as $indice => $valor) {
                    $selected = '';

                    if (strlen($valor['CodcCosto']) > 6) {
                        $descripcion .= ' \\ ' . $valor['DesccCosto'];
                    } else {
                        $descripcion = $valor['DesccCosto'];
                    }

                    if ($valor['CodcCosto'] == $codigo_inferior) $selected = 'selected';

                    $options_centro_costo_superior .= '<option value="' . $valor['CodcCosto'] . '" ' . $selected . '>' . $descripcion . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, '', 1, '', '', '', '');

                $options_estados = '';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $centro_costo['Estado']) $selected = 'selected';

                    $options_estados .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/cost_center/edit.js']);

                return viewApp($this->page, 'app/mantenience/cost_center/edit', [
                    'centro_costo' => $centro_costo,
                    'options_centro_costo_superior' => $options_centro_costo_superior,
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

            $post['DesccCosto'] = strtoupper(trim($post['DesccCosto']));

            $this->centroCostoModel = new ModelsCentroCosto();

            $existe_codigo = $this->centroCostoModel->getCentroCosto($post['CodEmpresa'], $post['CodcCosto'], '', [], '', '', '', '');

            if (count($existe_codigo) == 0) {
                $this->centroCostoModel = new ModelsCentroCosto();

                $this->centroCostoModel->agregar($post);
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

            $this->centroCostoModel = new ModelsCentroCosto();

            $this->centroCostoModel->actualizar($post['CodEmpresa'], $post['CodcCosto'], $post);

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

            $this->centroCostoModel = new ModelsCentroCosto();

            $this->centroCostoModel->eliminar($this->CodEmpresa, $CodcCosto);

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

            $this->centroCostoModel = new ModelsCentroCosto();

            $result = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', '', '', '');

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
            $this->centroCostoModel = new ModelsCentroCosto();

            $result = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', '', '', '');

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

                $this->centroCostoModel = new ModelsCentroCosto();

                $centro_costo = $this->centroCostoModel->getCentroCosto(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'CAST(MAX(SUBSTRING(CodcCosto, IF(LENGTH(CodcCosto) = 6, 3, LENGTH(CodcCosto) - 2))) AS UNSIGNED) AS CodcCosto',
                    'LENGTH(CodcCosto) = ' . (strlen($CodcCosto) + 3),
                    $CodcCosto,
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
}
