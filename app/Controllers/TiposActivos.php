<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoActivo;

class TiposActivos extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Tipos de Activos';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $tipos_activos_fijos = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', '', [], '', '');

                return viewApp($this->page, 'app/mantenience/asset_types/index', [
                    'tipos_activos_fijos' => $tipos_activos_fijos,
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
                $tipo_activo = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', 'MAX(SUBSTRING(codTipoActivo, 3)) AS codigo', [], '', '');

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

                $script = (new Empresa())->generar_script('', ['app/mantenience/asset_types/create.js']);

                return viewApp($this->page, 'app/mantenience/asset_types/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'typeOrder' => 'num',
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

            $post['descTipoActivo'] = strtoupper(trim($post['descTipoActivo']));

            $existe_codigo = (new TipoActivo())->getTipoActivo($post['CodEmpresa'], '', '', [], 'codTipoActivo = "' . $post['codTipoActivo'] . '"', '');

            if (count($existe_codigo) == 0) {
                (new TipoActivo())->insert($post);

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
            if ((new Empresa())->verificar_inicio_sesion()) {
                $tipo_activo_fijo = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', '', [], 'codTipoActivo = "' . $codTipoActivo . '"', '')[0];

                $script = "
                    var tipo_activo_fijo_descTipoActivo = '" . $tipo_activo_fijo['descTipoActivo'] . "';
                ";

                $script = (new Empresa())->generar_script($script, ['app/mantenience/asset_types/edit.js']);

                return viewApp($this->page, 'app/mantenience/asset_types/edit', [
                    'tipo_activo_fijo' => $tipo_activo_fijo,
                    'typeOrder' => 'num',
                    'script' => $script
                ]);
            } else {
                return (new Empresa())->logout();
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

            (new TipoActivo())->actualizar($post['CodEmpresa'], $post['codTipoActivo'], $post);

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

            (new TipoActivo())->eliminar($this->CodEmpresa, $codTipoActivo);

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

            $result = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', '', [], '', '');

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
            $result = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', '', [], '', '');

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

                $tipos_activos_fijos = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', '', [], 'UPPER(descTipoActivo) = "' . $descTipoActivo . '"', '');

                $existe = array('existe' => false);

                if (count($tipos_activos_fijos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            } else if ($tipo == 'editar') {
                $descTipoActivo = strtoupper(trim(strval($this->request->getPost('descTipoActivo'))));
                $NotdescTipoActivo = strtoupper(trim(strval($this->request->getPost('NotdescTipoActivo'))));

                $tipos_activos_fijos = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', '', [], 'UPPER(descTipoActivo) != "' . $NotdescTipoActivo . '" AND UPPER(descTipoActivo) = "' . $descTipoActivo . '"', '');

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

    public function autocompletado()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $tipo_activo = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', 'codTipoActivo AS id, descTipoActivo AS text', [], 'descTipoActivo LIKE "%' . $search . '%"', '');
            } else {
                $tipo_activo = (new TipoActivo())->getTipoActivo($this->CodEmpresa, '', 'codTipoActivo AS id, descTipoActivo AS text', [], '', '');
            }

            echo json_encode($tipo_activo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
