<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivoFijo;
use App\Models\Anexo;
use App\Models\CentroCosto;
use App\Models\I_AnexoSunat;
use App\Models\PlanContable;
use App\Models\TipoActivo;
use App\Models\Ubigeo;

class ActivosFijos extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Activos Fijos';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $activos_fijos = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', 'IdActivo, codActivo, descripcion, marca, modelo, serie', [], '', '');

                return viewApp($this->page, 'app/mantenience/fixed_assets/index', [
                    'activos_fijos' => $activos_fijos,
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
                $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', 'MAX(SUBSTRING(codActivo, 3)) AS codigo', [], '', '');

                $codigo_maximo = 'AC0001';

                if ($activo_fijo[0]['codigo']) {
                    $activo_fijo[0]['codigo'] = $activo_fijo[0]['codigo'] + 1;

                    if (strlen($activo_fijo[0]['codigo']) == 1) {
                        $codigo_maximo = 'AC000' . $activo_fijo[0]['codigo'];
                    } else if (strlen($activo_fijo[0]['codigo']) == 2) {
                        $codigo_maximo = 'AC00' . $activo_fijo[0]['codigo'];
                    } else if (strlen($activo_fijo[0]['codigo']) == 3) {
                        $codigo_maximo = 'AC0' . $activo_fijo[0]['codigo'];
                    } else {
                        $codigo_maximo = 'AC' . $activo_fijo[0]['codigo'];
                    }
                }

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, 0, 1, '', '', [], 'CodInterno = 1', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $catalogo_existente = (new I_AnexoSunat())->getI_AnexoSunat(2, 13, '', [], '', '')[0];

                $option_catalogo_existente = '<option value="' . $catalogo_existente['IdAnexoS'] . '">' . $catalogo_existente['DescAnexoS'] . '</option>';

                $tipo_activo = (new I_AnexoSunat())->getI_AnexoSunat(4, 18, '', [], '', '')[0];

                $option_tipo_activo = '<option value="' . $tipo_activo['IdAnexoS'] . '">' . $tipo_activo['DescAnexoS'] . '</option>';

                $estado_activo = (new I_AnexoSunat())->getI_AnexoSunat(8, 19, '', [], '', '')[0];

                $option_estado_activo = '<option value="' . $estado_activo['IdAnexoS'] . '">' . $estado_activo['DescAnexoS'] . '</option>';

                $metodo_depreciacion = (new I_AnexoSunat())->getI_AnexoSunat(9, 20, '', [], '', '')[0];

                $option_metodo_depreciacion = '<option value="' . $metodo_depreciacion['IdAnexoS'] . '">' . $metodo_depreciacion['DescAnexoS'] . '</option>';

                $script = (new Empresa())->generar_script(['app/mantenience/fixed_assets/create.js']);

                return viewApp($this->page, 'app/mantenience/fixed_assets/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'option_estado' => $option_estado,
                    'option_catalogo_existente' => $option_catalogo_existente,
                    'option_tipo_activo' => $option_tipo_activo,
                    'option_estado_activo' => $option_estado_activo,
                    'option_metodo_depreciacion' => $option_metodo_depreciacion,
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

    public function edit($IdActivo)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, $IdActivo, '', '', [], '', '')[0];

                $tipo_activo = (new TipoActivo())->getTipoActivo($this->CodEmpresa, $activo_fijo['codTipoActivo'] ?? '', '', [], '', '')[0];

                $option_tipo = $activo_fijo['codTipoActivo'] ? '<option value="' . $tipo_activo['codTipoActivo'] . '">' . $tipo_activo['descTipoActivo'] . '</option>' : '';

                $depreciacion = (new Anexo())->getAnexo($this->CodEmpresa, $activo_fijo['depresiacion'] ?? 0, 15, '', '', [], '', '')[0];

                $option_depreciacion = $activo_fijo['depresiacion'] ? '<option value="' . $depreciacion['IdAnexo'] . '">' . $depreciacion['DescAnexo'] . '</option>' : '';

                $cuenta_gasto = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $activo_fijo['CtaCtableGasto'] ?? '', '', [], '', '')[0];

                $option_cuenta_gasto = $activo_fijo['CtaCtableGasto'] ? '<option value="' . $cuenta_gasto['CodCuenta'] . '">' . $cuenta_gasto['CodCuenta'] . ' - ' . $cuenta_gasto['DescCuenta'] . '</option>' : '';

                $cuenta_depreciacion = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $activo_fijo['CtaCtableDepreciacion'] ?? '', '', [], '', '')[0];

                $option_cuenta_depreciacion = $activo_fijo['CtaCtableDepreciacion'] ? '<option value="' . $cuenta_depreciacion['CodCuenta'] . '">' . $cuenta_depreciacion['CodCuenta'] . ' - ' . $cuenta_depreciacion['DescCuenta'] . '</option>' : '';

                $estado = (new Anexo())->getAnexo($this->CodEmpresa, $activo_fijo['estado'], 1, '', '', [], '', '')[0];

                $option_estado = '<option value="' . $estado['IdAnexo'] . '">' . $estado['DescAnexo'] . '</option>';

                $centro_costo = (new CentroCosto())->getCentroCosto($this->CodEmpresa, $activo_fijo['CodCcosto'] ?? '', 0, '', [], '', '', '')[0];

                $option_centro_costo = $activo_fijo['CodCcosto'] ? '<option value="' . $centro_costo['CodcCosto'] . '">' . $centro_costo['CodcCosto'] . ' - ' . $centro_costo['DesccCosto'] . '</option>' : '';

                $ubigeo = (new Ubigeo())->getUbigeoQuery($this->db, $activo_fijo['codubigeo'] ?? '', '')[0];

                $option_ubigeo = $activo_fijo['codubigeo'] ? '<option value="' . $ubigeo->id . '">' . htmlspecialchars($ubigeo->text, ENT_QUOTES) . '</option>' : '';

                $catalogo_existente = (new I_AnexoSunat())->getI_AnexoSunat($activo_fijo['IdCatalogo'], 13, '', [], '', '')[0];

                $option_catalogo_existente = '<option value="' . $catalogo_existente['IdAnexoS'] . '">' . $catalogo_existente['DescAnexoS'] . '</option>';

                $tipo_activo = (new I_AnexoSunat())->getI_AnexoSunat($activo_fijo['IdTipoActivo'] ?? '', 18, '', [], '', '')[0];

                $option_tipo_activo = $activo_fijo['IdTipoActivo'] ? '<option value="' . $tipo_activo['IdAnexoS'] . '">' . $tipo_activo['DescAnexoS'] . '</option>' : '';

                $estado_activo = (new I_AnexoSunat())->getI_AnexoSunat($activo_fijo['IdEstadoActivo'], 19, '', [], '', '')[0];

                $option_estado_activo = '<option value="' . $estado_activo['IdAnexoS'] . '">' . $estado_activo['DescAnexoS'] . '</option>';

                $metodo_depreciacion = (new I_AnexoSunat())->getI_AnexoSunat($activo_fijo['IdMetodo'], 20, '', [], '', '')[0];

                $option_metodo_depreciacion = '<option value="' . $metodo_depreciacion['IdAnexoS'] . '">' . $metodo_depreciacion['DescAnexoS'] . '</option>';

                $script = (new Empresa())->generar_script(['app/mantenience/fixed_assets/edit.js']);

                return viewApp($this->page, 'app/mantenience/fixed_assets/edit', [
                    'activo_fijo' => $activo_fijo,
                    'option_tipo' => $option_tipo,
                    'option_tipo_activo' => $option_tipo_activo,
                    'option_depreciacion' => $option_depreciacion,
                    'option_cuenta_gasto' => $option_cuenta_gasto,
                    'option_cuenta_depreciacion' => $option_cuenta_depreciacion,
                    'option_estado' => $option_estado,
                    'option_centro_costo' => $option_centro_costo,
                    'option_ubigeo' => $option_ubigeo,
                    'option_catalogo_existente' => $option_catalogo_existente,
                    'option_tipo_activo' => $option_tipo_activo,
                    'option_estado_activo' => $option_estado_activo,
                    'option_metodo_depreciacion' => $option_metodo_depreciacion,
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

            $post['descripcion'] = strtoupper(trim($post['descripcion']));
            $post['fechaAdqui'] = !empty($post['fechaAdqui']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaAdqui']))) : NULL;
            $post['fechaInicio'] = !empty($post['fechaInicio']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaInicio']))) : NULL;
            $post['fechaRetiro'] = !empty($post['fechaRetiro']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaRetiro']))) : NULL;
            $post['ArrFecha'] = !empty($post['ArrFecha']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['ArrFecha']))) : NULL;

            $existe_codigo = (new ActivoFijo())->getActivoFijo($post['CodEmpresa'], 0, $post['codActivo'], '', [], '', '');

            if (count($existe_codigo) == 0) {
                (new ActivoFijo())->agregar($post);

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

            return redirect()->to(base_url('app/mantenience/fixed_assets/index'));
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

            $post['descripcion'] = strtoupper(trim($post['descripcion']));
            $post['fechaAdqui'] = !empty($post['fechaAdqui']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaAdqui']))) : NULL;
            $post['fechaInicio'] = !empty($post['fechaInicio']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaInicio']))) : NULL;
            $post['fechaRetiro'] = !empty($post['fechaRetiro']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaRetiro']))) : NULL;
            $post['ArrFecha'] = !empty($post['ArrFecha']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['ArrFecha']))) : NULL;

            (new ActivoFijo())->actualizar($post['CodEmpresa'], $post['IdActivo'], $post);

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

            return redirect()->to(base_url('app/mantenience/fixed_assets/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($IdActivo)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            (new ActivoFijo())->eliminar($this->CodEmpresa, $IdActivo);

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

            return redirect()->to(base_url('app/mantenience/fixed_assets/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Activos Fijos - Reporte');

            $columnas = array('CodActivo', 'Descripción', 'Tipo de Activo', 'Marca', 'Modelo', 'Serie');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $result = (new ActivoFijo())->getActivoFijo(
                $this->CodEmpresa,
                0,
                '',
                'activosfijos.codActivo, activosfijos.descripcion, t.descTipoActivo, activosfijos.marca, activosfijos.modelo, activosfijos.serie',
                [
                    array('tabla' => 'tipoactivo t', 'on' => 'activosfijos.codTipoActivo = t.codTipoActivo AND activosfijos.CodEmpresa = t.CodEmpresa', 'tipo' => 'inner')
                ],
                '',
                'activosfijos.IdActivo ASC'
            );

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['codActivo'],
                    $valor['descripcion'],
                    $valor['descTipoActivo'],
                    $valor['marca'],
                    $valor['modelo'],
                    $valor['serie']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('activos_fijos_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $result = (new ActivoFijo())->getActivoFijo(
                $this->CodEmpresa,
                0,
                '',
                'activosfijos.codActivo, activosfijos.descripcion, t.descTipoActivo, activosfijos.marca, activosfijos.modelo, activosfijos.serie',
                [
                    array('tabla' => 'tipoactivo t', 'on' => 'activosfijos.codTipoActivo = t.codTipoActivo AND activosfijos.CodEmpresa = t.CodEmpresa', 'tipo' => 'inner')
                ],
                '',
                'activosfijos.IdActivo ASC'
            );

            $columnas = array('CodActivo', 'Descripción', 'Tipo de Activo', 'Marca', 'Modelo', 'Serie');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['codActivo'] . '</td>
                    <td align="left">' . $valor['descripcion'] . '</td>
                    <td align="left">' . $valor['descTipoActivo'] . '</td>
                    <td align="left">' . $valor['marca'] . '</td>
                    <td align="left">' . $valor['modelo'] . '</td>
                    <td align="left">' . $valor['serie'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('activos_fijos_reporte');
            $pdf->creacion('Activos Fijos - Reporte', $tr, '', 'A3', true);
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
                $descripcion = strtoupper(trim(strval($this->request->getPost('descripcion'))));

                $activos_fijos = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', '', [], 'UPPER(descripcion) = "' . $descripcion . '"', '');

                $existe = array('existe' => false);

                if (count($activos_fijos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            } else if ($tipo == 'editar') {
                $descripcion = strtoupper(trim(strval($this->request->getPost('descripcion'))));
                $Notdescripcion = strtoupper(trim(strval($this->request->getPost('Notdescripcion'))));

                $activos_fijos = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', '', [], 'UPPER(descripcion) != "' . $Notdescripcion . '" AND UPPER(descripcion) = "' . $descripcion . '"', '');

                $existe = array('existe' => false);

                if (count($activos_fijos) > 0) {
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

            if (isset($post['App']) && !empty($post['App']) && $post['App'] == 'Ventas') {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', 'IdActivo AS id, CONCAT(codActivo, " - ", descripcion) AS text', [], 'CONCAT(codActivo, " - ", descripcion) LIKE "%' . $search . '%"', '');
                } else {
                    $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', 'IdActivo AS id, CONCAT(codActivo, " - ", descripcion) AS text', [], '', '');
                }
            } else {
                if (isset($post['search'])) {
                    $search = $post['search'];

                    $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', 'IdActivo AS id, CONCAT(codActivo, " - ", descripcion) AS text', [], 'CONCAT(codActivo, " - ", descripcion) LIKE "%' . $search . '%"', '');
                } else {
                    $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, 0, '', 'IdActivo AS id, CONCAT(codActivo, " - ", descripcion) AS text', [], '', '');
                }
            }

            echo json_encode($activo_fijo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
