<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivoFijo;
use App\Models\Anexo;
use App\Models\CentroCosto;
use App\Models\I_AnexoSunat;
use App\Models\PlanContable;
use App\Models\TipoActivo;

class ActivosFijos extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $activoFijoModel;
    protected $tipoActivoModel;
    protected $anexoModel;
    protected $planContableModel;
    protected $centroCostoModel;
    protected $i_AnexoSunatModel;

    public function __construct()
    {
        $this->page = 'Activos Fijos';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->activoFijoModel = new ActivoFijo();
        $this->tipoActivoModel = new TipoActivo();
        $this->anexoModel = new Anexo();
        $this->planContableModel = new PlanContable();
        $this->centroCostoModel = new CentroCosto();
        $this->i_AnexoSunatModel = new I_AnexoSunat();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->activoFijoModel = new ActivoFijo();

                $activos_fijos = $this->activoFijoModel->getActivoFijo($this->CodEmpresa, 'IdActivo, codActivo, descripcion, marca, modelo, serie', '');

                return viewApp($this->page, 'app/mantenience/fixed_assets/index', [
                    'activos_fijos' => $activos_fijos,
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
                $this->activoFijoModel = new ActivoFijo();

                $activo_fijo = $this->activoFijoModel->getActivoFijo($this->CodEmpresa, 'MAX(SUBSTRING(codActivo, 3)) AS codigo', '');

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

                $this->tipoActivoModel = new TipoActivo();

                $tipos_activo = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', '');

                $options_tipos_activos = '';

                foreach ($tipos_activo as $indice => $valor) {
                    $selected = '';

                    if ($valor['codTipoActivo'] == 'TA002') $selected = 'selected';

                    $options_tipos_activos .= '<option value="' . $valor['codTipoActivo'] . '" ' . $selected . '>' . $valor['descTipoActivo'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $depreciaciones = $this->anexoModel->getAnexo($this->CodEmpresa, '', 15, '', '', '', '');

                $options_depreciacion = '<option value="" disabled selected>Seleccione</option>';

                foreach ($depreciaciones as $indice => $valor) {
                    $options_depreciacion .= '<option value="' . $valor['OtroDato'] . '">' . $valor['DescAnexo'] . ' (' . $valor['OtroDato'] . ')' . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $cuentas_gasto = $this->planContableModel->getCuentasGastos($this->CodEmpresa);

                $options_cuentas_gasto = '';

                foreach ($cuentas_gasto as $indice => $valor) {
                    $options_cuentas_gasto .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $cuentas_depreciacion = $this->planContableModel->getCuentasDepreciacion($this->CodEmpresa);

                $options_cuentas_depreciacion = '';

                foreach ($cuentas_depreciacion as $indice => $valor) {
                    $options_cuentas_depreciacion .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, '', 1, '', '', '', '');

                $options_estado = '<option value="" disabled selected>Seleccione</option>';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($indice == 1) $selected = 'selected';

                    $options_estado .= '<option value="' . $indice . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->centroCostoModel = new CentroCosto();

                $centro_costo = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', '', '', '');

                $options_centro_costo = '';

                foreach ($centro_costo as $indice => $valor) {
                    $options_centro_costo .= '<option value="' . $valor['CodcCosto'] . '">' . $valor['CodcCosto'] . ' - ' . $valor['DesccCosto'] . '</option>';
                }

                $ubigeos = $this->db
                    ->query('SELECT dist.codubigeo, (
                                SELECT (
                                    SELECT CONCAT(dept.descubigeo, " \\\ ", prov.descubigeo, " \\\ ", dist.descubigeo) 
                                    FROM ubigeo dept WHERE dept.codubigeo = SUBSTRING(prov.codubigeo, 1, 4)
                                )
                                FROM ubigeo prov
                                WHERE prov.codubigeo = SUBSTRING(dist.codubigeo, 1, 6)
                            )
                            AS descubigeo
                            FROM ubigeo dist
                            WHERE LENGTH(dist.codubigeo) = 9 AND LENGTH(dist.codubigeo) != 2 AND dist.codubigeo NOT LIKE "9%"
                        ')->getResult();

                $options_ubigeos = '';

                foreach ($ubigeos as $indice => $valor) {
                    $options_ubigeos .= '<option value="' . $valor->codubigeo . '">' . htmlspecialchars($valor->descubigeo, ENT_QUOTES) . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $catalogo_existente = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(13);

                $options_catalogo_existente = '';

                foreach ($catalogo_existente as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == 2) $selected = 'selected';

                    $options_catalogo_existente .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $tipo_activo = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(18);

                $options_tipo_activo = '';

                foreach ($tipo_activo as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == 4) $selected = 'selected';

                    $options_tipo_activo .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $estado_activo = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(19);

                $options_estado_activo = '';

                foreach ($estado_activo as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == 8) $selected = 'selected';

                    $options_estado_activo .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $metodo_depreciacion = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(20);

                $options_metodo_depreciacion = '';

                foreach ($metodo_depreciacion as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == 9) $selected = 'selected';

                    $options_metodo_depreciacion .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/fixed_assets/create.js']);

                return viewApp($this->page, 'app/mantenience/fixed_assets/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'options_tipos_activos' => $options_tipos_activos,
                    'options_depreciacion' => $options_depreciacion,
                    'options_cuentas_gasto' => $options_cuentas_gasto,
                    'options_cuentas_depreciacion' => $options_cuentas_depreciacion,
                    'options_estado' => $options_estado,
                    'options_centro_costo' => $options_centro_costo,
                    'options_ubigeos' => $options_ubigeos,
                    'options_catalogo_existente' => $options_catalogo_existente,
                    'options_tipo_activo' => $options_tipo_activo,
                    'options_estado_activo' => $options_estado_activo,
                    'options_metodo_depreciacion' => $options_metodo_depreciacion,
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

    public function edit($IdActivo)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->activoFijoModel = new ActivoFijo();

                $activo_fijo = $this->activoFijoModel->getActivoFijo($this->CodEmpresa, '', 'IdActivo = ' . $IdActivo)[0];

                $this->tipoActivoModel = new TipoActivo();

                $tipos_activo = $this->tipoActivoModel->getTipoActivo($this->CodEmpresa, '', '');

                $options_tipos_activos = '';

                foreach ($tipos_activo as $indice => $valor) {
                    $selected = '';

                    if ($valor['codTipoActivo'] == $activo_fijo['codTipoActivo']) $selected = 'selected';

                    $options_tipos_activos .= '<option value="' . $valor['codTipoActivo'] . '" ' . $selected . '>' . $valor['descTipoActivo'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $depreciaciones = $this->anexoModel->getAnexo($this->CodEmpresa, '', 15, '', '', '', '');

                $options_depreciacion = '<option value="" disabled selected>Seleccione</option>';

                foreach ($depreciaciones as $indice => $valor) {
                    $selected = '';

                    if ($valor['OtroDato'] == $activo_fijo['depresiacion']) $selected = 'selected';

                    $options_depreciacion .= '<option value="' . $valor['OtroDato'] . '" ' . $selected . '>' . $valor['DescAnexo'] . ' (' . $valor['OtroDato'] . ')' . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $cuentas_gasto = $this->planContableModel->getCuentasGastos($this->CodEmpresa);

                $options_cuentas_gasto = '';

                foreach ($cuentas_gasto as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodCuenta'] == $activo_fijo['CtaCtableGasto']) $selected = 'selected';

                    $options_cuentas_gasto .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . ' ' . $selected . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $cuentas_depreciacion = $this->planContableModel->getCuentasDepreciacion($this->CodEmpresa);

                $options_cuentas_depreciacion = '';

                foreach ($cuentas_depreciacion as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodCuenta'] == $activo_fijo['CtaCtableDepreciacion']) $selected = 'selected';

                    $options_cuentas_depreciacion .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . ' ' . $selected . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->anexoModel = new Anexo();

                $estados = $this->anexoModel->getAnexo($this->CodEmpresa, '', 1, '', '', '', '');

                $options_estado = '<option value="" disabled selected>Seleccione</option>';

                foreach ($estados as $indice => $valor) {
                    $selected = '';

                    if ($indice == $activo_fijo['estado']) $selected = 'selected';

                    $options_estado .= '<option value="' . $indice . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $this->centroCostoModel = new CentroCosto();

                $centro_costo = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', '', '', '');

                $options_centro_costo = '';

                foreach ($centro_costo as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodcCosto'] == $activo_fijo['CodCcosto']) $selected = 'selected';

                    $options_centro_costo .= '<option value="' . $valor['CodcCosto'] . '" ' . $selected . '>' . $valor['CodcCosto'] . ' - ' . $valor['DesccCosto'] . '</option>';
                }

                $ubigeos = $this->db
                    ->query('SELECT dist.codubigeo, (
                                SELECT (
                                    SELECT CONCAT(dept.descubigeo, " \\\ ", prov.descubigeo, " \\\ ", dist.descubigeo) 
                                    FROM ubigeo dept WHERE dept.codubigeo = SUBSTRING(prov.codubigeo, 1, 4)
                                )
                                FROM ubigeo prov
                                WHERE prov.codubigeo = SUBSTRING(dist.codubigeo, 1, 6)
                            )
                            AS descubigeo
                            FROM ubigeo dist
                            WHERE LENGTH(dist.codubigeo) = 9 AND LENGTH(dist.codubigeo) != 2 AND dist.codubigeo NOT LIKE "9%"
                        ')->getResult();

                $options_ubigeos = '';

                foreach ($ubigeos as $indice => $valor) {
                    $selected = '';

                    if ($valor->codubigeo == $activo_fijo['codubigeo']) $selected = 'selected';

                    $options_ubigeos .= '<option value="' . $valor->codubigeo . '" ' . $selected . '>' . htmlspecialchars($valor->descubigeo, ENT_QUOTES) . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $catalogo_existente = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(13);

                $options_catalogo_existente = '';

                foreach ($catalogo_existente as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == $activo_fijo['IdCatalogo']) $selected = 'selected';

                    $options_catalogo_existente .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $tipo_activo = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(18);

                $options_tipo_activo = '';

                foreach ($tipo_activo as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == $activo_fijo['IdTipoActivo']) $selected = 'selected';

                    $options_tipo_activo .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $estado_activo = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(19);

                $options_estado_activo = '';

                foreach ($estado_activo as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == $activo_fijo['IdEstadoActivo']) $selected = 'selected';

                    $options_estado_activo .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->i_AnexoSunatModel = new I_AnexoSunat();

                $metodo_depreciacion = $this->i_AnexoSunatModel->getI_AnexoSunatByTipoAnexoS(20);

                $options_metodo_depreciacion = '';

                foreach ($metodo_depreciacion as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexoS'] == $activo_fijo['IdMetodo']) $selected = 'selected';

                    $options_metodo_depreciacion .= '<option value="' . $valor['IdAnexoS'] . '" ' . $selected . '>' . $valor['DescAnexoS'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = "
                    var activo_fijo_descripcion = '" . $activo_fijo['descripcion'] . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/fixed_assets/edit.js']);

                return viewApp($this->page, 'app/mantenience/fixed_assets/edit', [
                    'activo_fijo' => $activo_fijo,
                    'options_tipos_activos' => $options_tipos_activos,
                    'options_depreciacion' => $options_depreciacion,
                    'options_cuentas_gasto' => $options_cuentas_gasto,
                    'options_cuentas_depreciacion' => $options_cuentas_depreciacion,
                    'options_estado' => $options_estado,
                    'options_centro_costo' => $options_centro_costo,
                    'options_ubigeos' => $options_ubigeos,
                    'options_catalogo_existente' => $options_catalogo_existente,
                    'options_tipo_activo' => $options_tipo_activo,
                    'options_estado_activo' => $options_estado_activo,
                    'options_metodo_depreciacion' => $options_metodo_depreciacion,
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

            $post['descripcion'] = strtoupper(trim($post['descripcion']));
            $post['fechaAdqui'] = !empty($post['fechaAdqui']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaAdqui']))) : NULL;
            $post['fechaInicio'] = !empty($post['fechaInicio']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaInicio']))) : NULL;
            $post['fechaRetiro'] = !empty($post['fechaRetiro']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['fechaRetiro']))) : NULL;
            $post['ArrFecha'] = !empty($post['ArrFecha']) ? date('Y-m-d', strtotime(str_replace('/', '-', $post['ArrFecha']))) : NULL;

            $this->activoFijoModel = new ActivoFijo();

            $existe_codigo = $this->activoFijoModel->getActivoFijo($post['CodEmpresa'], '', 'codActivo = "' . $post['codActivo'] . '"');

            if (count($existe_codigo) == 0) {
                $this->activoFijoModel = new ActivoFijo();

                $this->activoFijoModel->agregar($post);

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

            $this->activoFijoModel = new ActivoFijo();

            $this->activoFijoModel->actualizar($post['CodEmpresa'], $post['IdActivo'], $post);

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

            $this->activoFijoModel = new ActivoFijo();

            $this->activoFijoModel->eliminar($this->CodEmpresa, $IdActivo);

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

            $this->activoFijoModel = new ActivoFijo();

            $result = $this->activoFijoModel->getActivoFijoExcel($this->CodEmpresa);

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
            $this->activoFijoModel = new ActivoFijo();

            $result = $this->activoFijoModel->getActivoFijoPDF($this->CodEmpresa);

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

                $this->activoFijoModel = new ActivoFijo();

                $activos_fijos = $this->activoFijoModel->getActivoFijo($this->CodEmpresa, '', 'UPPER(descripcion) = "' . $descripcion . '"');

                $existe = array('existe' => false);

                if (count($activos_fijos) > 0) {
                    $existe = array('existe' => true);
                }

                echo json_encode($existe);
            } else if ($tipo == 'editar') {
                $descripcion = strtoupper(trim(strval($this->request->getPost('descripcion'))));
                $Notdescripcion = strtoupper(trim(strval($this->request->getPost('Notdescripcion'))));

                $this->activoFijoModel = new ActivoFijo();

                $activos_fijos = $this->activoFijoModel->getActivoFijo($this->CodEmpresa, '', 'UPPER(descripcion) != "' . $Notdescripcion . '" AND UPPER(descripcion) = "' . $descripcion . '"');

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
}
