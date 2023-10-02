<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoActivo;

class TiposActivos extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $tipoActivoModel;

    public function __construct()
    {
        $this->page = 'Tipos de Activos';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->tipoActivoModel = new TipoActivo();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->tipoActivoModel = new TipoActivo();

                $tipos_activos_fijos = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', '');

                return viewApp($this->page, 'app/mantenience/asset_types/index', [
                    'tipos_activos_fijos' => $tipos_activos_fijos,
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
                $this->tipoActivoModel = new TipoActivo();

                $tipo_activo = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, 'MAX(SUBSTRING(codTipoActivo, 3)) AS codigo', '');

                $codigo_maximo = 'TA001';

                if ($tipo_activo[0]['codigo']) {
                    $tipo_activo[0]['codigo'] = $tipo_activo[0]['codigo'] + 1;

                    if (strlen($tipo_activo[0]['codigo']) == 1) {
                        $codigo_maximo = 'TA00' . $tipo_activo[0]['codigo'];
                    } else if (strlen($tipo_activo[0]['codigo']) == 2) {
                        $codigo_maximo = 'TA0' . $tipo_activo[0]['codigo'];
                    } else {
                        $codigo_maximo = 'TA' . $tipo_activo[0]['codigo'];
                    }
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/asset_types/create.js']);

                return viewApp($this->page, 'app/mantenience/asset_types/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'typeOrder' => 'num',
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

            $post['descTipoActivo'] = strtoupper(trim($post['descTipoActivo']));

            $this->tipoActivoModel = new TipoActivo();

            $existe_codigo = $this->tipoActivoModel->getTipoActivo($post['CodEmpresa'], '', 'codTipoActivo = "' . $post['codTipoActivo'] . '"');

            if (count($existe_codigo) == 0) {
                $this->tipoActivoModel = new TipoActivo();

                $this->tipoActivoModel->insert($post);

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
            } else {
                $_SESSION['code'] = 'error';
            }

            return redirect()->to(base_url('app/mantenience/asset_types/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($codTipoActivo)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->tipoActivoModel = new TipoActivo();

                $tipo_activo_fijo = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', 'codTipoActivo = "' . $codTipoActivo . '"')[0];

                $this->empresa = new Empresa();

                $script = "
                    var tipo_activo_fijo_descTipoActivo = '" . $tipo_activo_fijo['descTipoActivo'] . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/asset_types/edit.js']);

                return viewApp($this->page, 'app/mantenience/asset_types/edit', [
                    'tipo_activo_fijo' => $tipo_activo_fijo,
                    'typeOrder' => 'num',
                    'script' => $script
                ]);
            } else {
                return $this->empresa->logout();
            }
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

            $post['descTipoActivo'] = strtoupper(trim($post['descTipoActivo']));

            $this->tipoActivoModel = new TipoActivo();

            $this->tipoActivoModel->actualizar($post['CodEmpresa'], $post['codTipoActivo'], $post);

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

            return redirect()->to(base_url('app/mantenience/asset_types/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($codTipoActivo)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->tipoActivoModel = new TipoActivo();

            $this->tipoActivoModel->eliminar($this->CodEmpresa, $codTipoActivo);

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

            return redirect()->to(base_url('app/mantenience/asset_types/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Tipos Activos Fijos - Reporte');

            $columnas = array('Código', 'Descripción');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->tipoActivoModel = new TipoActivo();

            $result = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', '');

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['codTipoActivo'],
                    $valor['descTipoActivo']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('tipos_activos_fijos_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $this->tipoActivoModel = new TipoActivo();

            $result = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', '');

            $columnas = array('Código', 'Descripción');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['codTipoActivo'] . '</td>
                    <td align="left">' . $valor['descTipoActivo'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('tipos_activos_fijos_reporte');
            $pdf->creacion('Tipos Activos Fijos - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_nombre()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'nuevo') {
                $descTipoActivo = strtoupper(trim(strval($this->request->getPost('descTipoActivo'))));

                $this->tipoActivoModel = new TipoActivo();

                $tipos_activos_fijos = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', 'UPPER(descTipoActivo) = "' . $descTipoActivo . '"');

                $existe = array('existe' => false);

                if (count($tipos_activos_fijos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            } else if ($tipo == 'editar') {
                $descTipoActivo = strtoupper(trim(strval($this->request->getPost('descTipoActivo'))));
                $NotdescTipoActivo = strtoupper(trim(strval($this->request->getPost('NotdescTipoActivo'))));

                $this->tipoActivoModel = new TipoActivo();

                $tipos_activos_fijos = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', 'UPPER(descTipoActivo) != "' . $NotdescTipoActivo . '" AND UPPER(descTipoActivo) = "' . $descTipoActivo . '"');

                $existe = array('existe' => false);

                if (count($tipos_activos_fijos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
