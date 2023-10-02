<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Amarre;
use App\Models\PlanContable as ModelsPlanContable;
use App\Models\PlanContableEquiv;
use App\Models\TipoVoucherDet;

class PlanContable extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $planContableModel;
    protected $amarreModel;
    protected $tipoVoucherDetModel;
    protected $planContableEquivModel;

    public function __construct()
    {
        $this->page = 'Plan Contable';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->planContableModel = new ModelsPlanContable();
        $this->amarreModel = new Amarre();
        $this->tipoVoucherDetModel = new TipoVoucherDet();
        $this->planContableEquivModel = new PlanContableEquiv();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/accounting_plan/index.js']);

                return viewApp($this->page, 'app/mantenience/accounting_plan/index', [
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

    public function create()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $relacion_cuentas = array('Ninguno', 'Cuenta Corriente', 'Centro de Costo', 'Ambos', 'Activo Fijo');

                $options_relacion_cuentas = '';

                foreach ($relacion_cuentas as $indice => $valor) {
                    $options_relacion_cuentas .= '<option value="' . $indice . '">' . $valor . '</option>';
                }

                $tipo_cuentas = array('Ninguno', 'Ctas. del Balance General', 'EGP por Naturaleza', 'EGP por Función', 'EGP por Naturaleza y Función');

                $options_tipo_cuentas = '';

                foreach ($tipo_cuentas as $indice => $valor) {
                    $options_tipo_cuentas .= '<option value="' . $indice . '">' . $valor . '</option>';
                }

                $tipo_activo_fijos = array('Ninguno', 'Mejoras', 'Otros Ajustes');

                $options_tipo_activo_fijos = '';

                foreach ($tipo_activo_fijos as $indice => $valor) {
                    $options_tipo_activo_fijos .= '<option value="' . $indice . '">' . $valor . '</option>';
                }

                $diferencia_cambios = array('Compra', 'Venta');

                $options_diferencia_cambios = '';

                foreach ($diferencia_cambios as $indice => $valor) {
                    $options_diferencia_cambios .= '<option value="' . $indice . '">' . $valor . '</option>';
                }

                $tipos_debe_haber = array('Ninguno', 'Activo', 'Pasivo', 'Gastos', 'Ingresos', 'Activo\Pasivo');

                $radios_tipos_debe_haber = '';

                for ($i = 0; $i <= count($tipos_debe_haber) - 4; $i++) {
                    $checked = '';
                    $disabled = 'disabled';

                    if ($i == 0) {
                        $checked = 'checked';
                        $disabled = '';
                    }

                    $radios_tipos_debe_haber .= '
                        <div class="mx-3">
                            <input type="radio" name="TipoDebeHaber" id="TipoDebeHaber' . $i . '" class="TipoDebeHaber" value="' . $i . '" ' . $checked . ' ' . $disabled . '> <label id="labelTipoDebeHaber' . $i . '" class="' . $disabled . '" for="TipoDebeHaber' . $i . '">' . $tipos_debe_haber[$i] . '</label>
                        </div>';
                }

                $radios_tipos_debe_haber .= '
                    <div class="mx-3">
                        <input type="radio" name="TipoDebeHaber" id="TipoDebeHaber' . (count($tipos_debe_haber) - 1) . '" class="TipoDebeHaber" value="' . (count($tipos_debe_haber) - 1) . '" disabled> <label id="labelTipoDebeHaber' . (count($tipos_debe_haber) - 1) . '" class="disabled" for="TipoDebeHaber' . (count($tipos_debe_haber) - 1) . '">' . $tipos_debe_haber[count($tipos_debe_haber) - 1] . '</label>
                    </div>';

                for ($i = count($tipos_debe_haber) - 3; $i <= 4; $i++) {
                    $disabled = 'disabled';

                    $radios_tipos_debe_haber .= '
                        <div class="mx-3">
                            <input type="radio" name="TipoDebeHaber" id="TipoDebeHaber' . $i . '" class="TipoDebeHaber" value="' . $i . '" ' . $disabled . '> <label id="labelTipoDebeHaber' . $i . '" class="' . $disabled . '" for="TipoDebeHaber' . $i . '">' . $tipos_debe_haber[$i] . '</label>
                        </div>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/accounting_plan/create.js']);

                return viewApp($this->page, 'app/mantenience/accounting_plan/create', [
                    'options_relacion_cuentas' => $options_relacion_cuentas,
                    'options_tipo_cuentas' => $options_tipo_cuentas,
                    'options_tipo_activo_fijos' => $options_tipo_activo_fijos,
                    'options_diferencia_cambios' => $options_diferencia_cambios,
                    'radios_tipos_debe_haber' => $radios_tipos_debe_haber,
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

    public function edit($CodCuenta, $Periodo)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->planContableModel = new ModelsPlanContable();

                $plan_contable = $this->planContableModel->getPlanContable($this->CodEmpresa, $Periodo, $CodCuenta, '', '', '')[0];

                $this->amarreModel = new Amarre();

                $amarres = $this->amarreModel->getAmarre($this->CodEmpresa, $Periodo, $CodCuenta, '', '');

                $this->planContableEquivModel = new PlanContableEquiv();

                $plan_contable_equiv = $this->planContableEquivModel->getPlanContableEquiv($this->CodEmpresa, $Periodo, $CodCuenta, '', '');

                $equivalente = array();

                if (count($plan_contable_equiv) > 0) {
                    $equivalente = $equivalente[0];
                }

                $this->planContableModel = new ModelsPlanContable();

                $planes_contable = $this->planContableModel->getPlanContable($this->CodEmpresa, $Periodo, '', 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled', '', 'CodCuenta ASC');

                $options_planes_contable = '<option value="" disabled selected>Seleccione</option>';

                foreach ($planes_contable as $indice => $valor) {
                    $options_planes_contable .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $relacion_cuentas = array('Ninguno', 'Cuenta Corriente', 'Centro de Costo', 'Ambos', 'Activo Fijo');

                $options_relacion_cuentas = '';

                foreach ($relacion_cuentas as $indice => $valor) {
                    $selected = '';

                    if ($indice == $plan_contable['RelacionCuenta']) $selected = 'selected';

                    $options_relacion_cuentas .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $tipo_cuentas = array('Ninguno', 'Ctas. del Balance General', 'EGP por Naturaleza', 'EGP por Función', 'EGP por Naturaleza y Función');

                $options_tipo_cuentas = '';

                foreach ($tipo_cuentas as $indice => $valor) {
                    $selected = '';

                    if ($indice == $plan_contable['TipoResultado']) $selected = 'selected';

                    $options_tipo_cuentas .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $tipo_activo_fijos = array('Ninguno', 'Mejoras', 'Otros Ajustes');

                $options_tipo_activo_fijos = '';

                foreach ($tipo_activo_fijos as $indice => $valor) {
                    $selected = '';

                    if ($indice == $plan_contable['TipoCuenta']) $selected = 'selected';

                    $options_tipo_activo_fijos .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $diferencia_cambios = array('Compra', 'Venta');

                $options_diferencia_cambios = '';

                foreach ($diferencia_cambios as $indice => $valor) {
                    $selected = '';

                    if ($indice == $plan_contable['Tcambio_CV']) $selected = 'selected';

                    $options_diferencia_cambios .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $tipos_debe_haber = array('Ninguno', 'Activo', 'Pasivo', 'Gastos', 'Ingresos', 'Activo\Pasivo');

                $radios_tipos_debe_haber = '';

                for ($i = 0; $i <= count($tipos_debe_haber) - 4; $i++) {
                    $disabled = 'disabled';
                    $checked = '';

                    if ($i == 0) $disabled = '';

                    if ($i == $plan_contable['TipoDebeHaber']) $checked = 'checked';

                    $radios_tipos_debe_haber .= '
                        <div class="mx-3">
                            <input type="radio" name="TipoDebeHaber" id="TipoDebeHaber' . $i . '" class="TipoDebeHaber" value="' . $i . '" ' . $checked . ' ' . $disabled . '> <label id="labelTipoDebeHaber' . $i . '" class="' . $disabled . '" for="TipoDebeHaber' . $i . '">' . $tipos_debe_haber[$i] . '</label>
                        </div>';
                }

                $checked = '';

                if ((count($tipos_debe_haber) - 1) == $plan_contable['TipoDebeHaber']) $checked = 'checked';

                $radios_tipos_debe_haber .= '
                    <div class="mx-3">
                        <input type="radio" name="TipoDebeHaber" id="TipoDebeHaber' . (count($tipos_debe_haber) - 1) . '" class="TipoDebeHaber" value="' . (count($tipos_debe_haber) - 1) . '" ' . $checked . ' disabled> <label id="labelTipoDebeHaber' . (count($tipos_debe_haber) - 1) . '" class="disabled" for="TipoDebeHaber' . (count($tipos_debe_haber) - 1) . '">' . $tipos_debe_haber[count($tipos_debe_haber) - 1] . '</label>
                    </div>';

                for ($i = count($tipos_debe_haber) - 3; $i <= 4; $i++) {
                    $disabled = 'disabled';
                    $checked = '';

                    if ($i == $plan_contable['TipoDebeHaber']) $checked = 'checked';

                    $radios_tipos_debe_haber .= '
                        <div class="mx-3">
                            <input type="radio" name="TipoDebeHaber" id="TipoDebeHaber' . $i . '" class="TipoDebeHaber" value="' . $i . '" ' . $checked . ' ' . $disabled . '> <label id="labelTipoDebeHaber' . $i . '" class="' . $disabled . '" for="TipoDebeHaber' . $i . '">' . $tipos_debe_haber[$i] . '</label>
                        </div>';
                }

                $this->empresa = new Empresa();

                $script = "
                    var id_amarre = " . (count($amarres) + 1) . ";
                    var plan_contable_TipoDebeHaber = '" . $plan_contable['TipoDebeHaber'] . "';
                    var plan_contable_CodCuenta = '" . $plan_contable['CodCuenta'] . "';
                    var plan_contable_DescCuenta = '" . $plan_contable['DescCuenta'] . "';
                ";

                foreach ($amarres as $indice => $valor) {
                    $script .= "$('#CuentaDebe" . ($indice + 1) . "').val('" . $valor['CuentaDebe'] . "');";
                    $script .= "$('#CuentaHaber" . ($indice + 1) . "').val('" . $valor['CuentaHaber'] . "');";
                }

                $script = $this->empresa->generar_script($script, ['app/mantenience/accounting_plan/edit.js']);

                return viewApp($this->page, 'app/mantenience/accounting_plan/edit', [
                    'plan_contable' => $plan_contable,
                    'amarres' => $amarres,
                    'equivalente' => $equivalente,
                    'options_planes_contable' => $options_planes_contable,
                    'options_relacion_cuentas' => $options_relacion_cuentas,
                    'options_tipo_cuentas' => $options_tipo_cuentas,
                    'options_tipo_activo_fijos' => $options_tipo_activo_fijos,
                    'options_diferencia_cambios' => $options_diferencia_cambios,
                    'radios_tipos_debe_haber' => $radios_tipos_debe_haber,
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

            $items = isset($post['Items']) ? $post['Items'] : [];

            $post['DescCuenta'] = strtoupper(trim($post['DescCuenta']));
            $post['Child'] = 1;
            $post['AjusteDC'] = isset($post['AjusteDC']) ? $post['AjusteDC'] : 0;

            $this->planContableModel = new ModelsPlanContable();

            $this->planContableModel->agregar($post);

            $this->planContableModel = new ModelsPlanContable();

            $this->planContableModel->actualizar($post['CodEmpresa'], $post['Periodo'], $post['CuentaPadre'], '', ['Child' => 0]);

            if (count($items) > 0) {
                $this->amarreModel = new Amarre();

                $this->amarreModel->eliminar($post['CodEmpresa'], $post['Periodo'], $post['CodCuenta']);

                foreach ($items as $indice => $valor) {
                    $data = [
                        'CodCuenta' => $post['CodCuenta'],
                        'Periodo' => $post['Periodo'],
                        'CodEmpresa' => $post['CodEmpresa'],
                        'CuentaDebe' => $post['CuentaDebe'][$indice],
                        'CuentaHaber' => $post['CuentaHaber'][$indice],
                        'Porcentaje' => $post['Porcentaje'][$indice]
                    ];

                    $this->amarreModel = new Amarre();

                    $this->amarreModel->agregar($data);
                }
            } else {
                $this->amarreModel = new Amarre();

                $this->amarreModel->eliminar($post['CodEmpresa'], $post['Periodo'], $post['CodCuenta']);
            }

            if (!empty($post['CodCuentaEquiv']) || !empty($post['Descripcion'])) {
                $post['DescCuenta'] = $post['Descripcion'];

                $this->planContableEquivModel = new PlanContableEquiv();

                $this->planContableEquivModel->agregar($post);
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

            return redirect()->to(base_url('app/mantenience/accounting_plan/index'));
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

            $items = isset($post['Items']) ? $post['Items'] : [];

            $post['DescCuenta'] = strtoupper(trim($post['DescCuenta']));

            $post['AjusteDC'] = isset($post['AjusteDC']) ? $post['AjusteDC'] : 0;

            $this->planContableModel = new ModelsPlanContable();

            $existe_codcuentas = $this->planContableModel->getPlanContable($this->CodEmpresa, $post['Periodo'], '', '', 'CuentaPadre = "' . $post['CodCuenta'] . '"', '');

            if (count($existe_codcuentas) == 0) {
                $post['Child'] = 1;
            } else {
                $post['Child'] = 0;
            }

            $this->planContableModel = new ModelsPlanContable();

            $this->planContableModel->actualizar($this->CodEmpresa, $post['Periodo'], $post['CuentaPadre'], '', ['Child' => 0]);

            $this->planContableModel = new ModelsPlanContable();

            $this->planContableModel->actualizar($this->CodEmpresa, $post['Periodo'], $post['CodCuentaPrincipal'], '', $post);

            if (count($items) > 0) {
                $this->amarreModel = new Amarre();

                $this->amarreModel->eliminar($this->CodEmpresa, $post['Periodo'], $post['CodCuentaPrincipal']);

                foreach ($items as $indice => $valor) {
                    $data = [
                        'CodCuenta' => $post['CodCuenta'],
                        'Periodo' => $post['Periodo'],
                        'CodEmpresa' => $post['CodEmpresa'],
                        'CuentaDebe' => $post['CuentaDebe'][$indice],
                        'CuentaHaber' => $post['CuentaHaber'][$indice],
                        'Porcentaje' => $post['Porcentaje'][$indice]
                    ];

                    $this->amarreModel = new Amarre();

                    $this->amarreModel->agregar($data);
                }
            } else {
                $this->amarreModel = new Amarre();

                $this->amarreModel->eliminar($this->CodEmpresa, $post['Periodo'], $post['CodCuentaPrincipal']);
            }

            if (!isset($post['Amarres'])) {
                $this->amarreModel = new Amarre();

                $this->amarreModel->eliminar($this->CodEmpresa, $post['Periodo'], $post['CodCuentaPrincipal']);
            }

            if (!empty($post['CodCuentaEquiv']) || !empty($post['Descripcion'])) {
                $this->planContableEquivModel = new PlanContableEquiv();

                $this->planContableEquivModel->eliminar($this->CodEmpresa, $post['Periodo'], $post['CodCuentaPrincipal']);

                $post['DescCuenta'] = $post['Descripcion'];

                $this->planContableEquivModel = new PlanContableEquiv();

                $this->planContableEquivModel->agregar($post);
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

            return redirect()->to(base_url('app/mantenience/accounting_plan/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($CodCuenta, $Periodo)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->planContableModel = new ModelsPlanContable();

            $CuentaPadre = $this->planContableModel->getPlanContable($this->CodEmpresa, $Periodo, $CodCuenta, 'CuentaPadre', '', '');

            $CuentaPadre = $CuentaPadre[0]['CuentaPadre'];

            $this->planContableModel = new ModelsPlanContable();

            $existe_codcuentas = $this->planContableModel->getPlanContable($this->CodEmpresa, $Periodo, '', '', 'CuentaPadre = "' . $CodCuenta . '" AND CodCuenta != "' . $CodCuenta . '"', '');

            $this->tipoVoucherDetModel = new TipoVoucherDet();

            $existe_referencias = $this->tipoVoucherDetModel->getTipoVoucherDet($this->CodEmpresa, $Periodo, $CodCuenta, '', '', [], '', '');

            if (count($existe_codcuentas) == 0) {
                if (count($existe_referencias) == 0) {
                    $this->planContableModel = new ModelsPlanContable();

                    $this->planContableModel->actualizar($this->CodEmpresa, $Periodo, $CuentaPadre, '', ['Child' => 1]);

                    $this->planContableModel = new ModelsPlanContable();

                    $this->planContableModel->eliminar($this->CodEmpresa, $Periodo, $CodCuenta, '');

                    $this->amarreModel = new Amarre();

                    $this->amarreModel->eliminar($this->CodEmpresa, $Periodo, $CodCuenta);

                    $this->planContableEquivModel = new PlanContableEquiv();

                    $this->planContableEquivModel->eliminar($this->CodEmpresa, $Periodo, $CodCuenta);

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
                }
            } else {
                if (count($existe_referencias) > 0) {
                    $_SESSION['mensaje'] = 'No se puede Eliminar la Cuenta por estar Referenciado en Tipo Voucher Detalle';
                    $_SESSION['code'] = 'error';
                } else if (count($existe_codcuentas) > 0) {
                    $_SESSION['mensaje'] = 'No se Puede Eliminar porque Existen Cuentas Hijas de esta Cuenta';
                    $_SESSION['code'] = 'error';
                }
            }

            return redirect()->to(base_url('app/mantenience/accounting_plan/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Plan Contable - Reporte');

            $columnas = array('Cuenta', 'Descripción', 'Cta. Padre', 'Cta. Ajuste', 'Relación Cta.', 'Tipo Resultado', 'Amarre Debe', 'Amarre Haber');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->planContableModel = new ModelsPlanContable();

            $result = $this->planContableModel->excel($this->CodEmpresa);

            $index = 0;

            foreach ($result as $indice => $valor) {
                $values = array(
                    $valor['CodCuenta'],
                    $valor['DescCuenta'],
                    $valor['CuentaPadre'],
                    $valor['CuentaAjuste'],
                    $valor['RelacionCuenta'],
                    $valor['TipoResultado']
                );

                $excel->setValues($values);

                $excel->body($index + 2, 'valor');

                $this->amarreModel = new Amarre();

                $result_2 = $this->amarreModel->getAmarre($this->CodEmpresa, $valor['Periodo'], $valor['CodCuenta'], 'CuentaDebe, CuentaHaber', '');

                foreach ($result_2 as $indice_2 => $valor_2) {
                    $index++;

                    $values_2 = array('', '', '', '', '', '', $valor_2['CuentaDebe'], $valor_2['CuentaHaber']);

                    $excel->setValues($values_2);

                    $excel->body($index + 2, 'valor');
                }

                $index++;
            }

            $excel->footer('plan_contable_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $this->planContableModel = new ModelsPlanContable();

            $result = $this->planContableModel->pdf($this->CodEmpresa);

            $columnas = array('Cuenta', 'Descripción', 'Cta. Padre', 'Cta. Ajuste', 'Relación Cta.', 'Tipo Resultado', 'Amarre Debe', 'Amarre Haber');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['CodCuenta'] . '</td>
                    <td align="left">' . $valor['DescCuenta'] . '</td>
                    <td align="left">' . $valor['CuentaPadre'] . '</td>
                    <td align="left">' . $valor['CuentaAjuste'] . '</td>
                    <td align="left">' . $valor['RelacionCuenta'] . '</td>
                    <td align="left">' . $valor['TipoResultado'] . '</td>
                <tr>
            ';

                $this->amarreModel = new Amarre();

                $result_2 = $this->amarreModel->getAmarre($this->CodEmpresa, $valor['Periodo'], $valor['CodCuenta'], 'CuentaDebe, CuentaHaber', '');

                foreach ($result_2 as $indice_2 => $valor_2) {
                    $tr .= '
                    <tr>
                        <td colspan="6"></td>
                        <td align="left">' . $valor_2['CuentaDebe'] . '</td>
                        <td align="left">' . $valor_2['CuentaHaber'] . '</td>
                    <tr>
                ';
                }
            }

            $pdf = new PDF();

            $pdf->setFilename('plan_contable_reporte');
            $pdf->creacion('Plan Contable - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function datos()
    {
        try {
            $dbDetails = array(
                'host' => $this->db->hostname,
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db'   => $this->db->database
            );

            $table = 'plan_contable';

            $primaryKey = 'CodCuenta';

            $columns = array(
                array('db' => 'CodCuenta', 'dt' => 0),
                array('db' => 'DescCuenta', 'dt' => 1),
                array('db' => 'CuentaPadre', 'dt' => 2),
                array(
                    'db' => 'RelacionCuenta',
                    'dt' => 3,
                    'formatter' => function ($d, $row) {
                        switch ($d) {
                            case 0:
                                return 'Ninguno';
                            case 1:
                                return 'Cuenta Corriente';
                            case 2:
                                return 'Centro de Costo';
                            case 3:
                                return 'Ambos';
                            case 4:
                                return 'Activo Fijo';
                        }
                    }
                ),
                array(
                    'db' => 'TipoResultado',
                    'dt' => 4,
                    'formatter' => function ($d, $row) {
                        switch ($d) {
                            case 0:
                                return 'Ninguno';
                            case 1:
                                return 'Inventario';
                            case 2:
                                return 'Resultado x Naturaleza';
                            case 3:
                                return 'Resultado x Funcion';
                            case 4:
                                return 'Resultado x Naturaleza y Funcion';
                        }
                    }
                ),
                array('db' => 'Periodo', 'dt' => 5)
            );

            $where = "CodEmpresa = '" . $this->CodEmpresa . "'";

            $_POST['likeStart'] = 'no';

            echo json_encode(
                SSP::simple($_POST, $dbDetails, $table, $primaryKey, $columns, $where)
            );
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_cuenta()
    {
        try {
            $tipo = $this->request->getPost('tipo');

            if ($tipo == 'consulta_cuenta_padre') {
                $CodCuenta = substr(trim(strval($this->request->getPost('CodCuenta'))), 0, strlen(trim(strval($this->request->getPost('CodCuenta')))) - 1);

                $existe = array('existe' => false, 'codigo' => '');

                $this->planContableModel = new ModelsPlanContable();

                $CuentaPadre = $this->planContableModel->getPlanContable($this->CodEmpresa, '', $CodCuenta, 'CodCuenta', '', '');

                if (count($CuentaPadre) > 0) {
                    $existe = array('existe' => true, 'codigo' => $CuentaPadre[0]['CodCuenta']);
                }

                echo json_encode($existe);
            } else if ($tipo == 'verificar_cuenta_hijo') {
                $CodCuenta = trim(strval($this->request->getPost('CodCuenta')));
                $DescCuenta = strtoupper(trim(strval($this->request->getPost('DescCuenta'))));

                $existe = array('existe' => false);

                $this->planContableModel = new ModelsPlanContable();

                $CuentaPadre = $this->planContableModel->getPlanContable($this->CodEmpresa, '', $CodCuenta, 'CodCuenta', '', '');

                if (count($CuentaPadre) > 0) {
                    $existe = array('existe' => true, 'tipo' => 'codigo', 'codigo' => $CuentaPadre[0]['CodCuenta']);
                } else {
                    $this->planContableModel = new ModelsPlanContable();

                    $DescripcionHijo = $this->planContableModel->getPlanContable($this->CodEmpresa, '', '', 'DescCuenta', 'DescCuenta = "' . $DescCuenta . '"', '');

                    if (count($DescripcionHijo) > 0) {
                        $existe = array('existe' => true, 'tipo' => 'descripcion', 'codigo' => $DescripcionHijo[0]['DescCuenta']);
                    }
                }

                echo json_encode($existe);
            } else if ($tipo == 'verificar_cuenta_hijo_editar') {
                $CodCuenta = trim(strval($this->request->getPost('CodCuenta')));
                $NotCodCuenta = trim(strval($this->request->getPost('NotCodCuenta')));
                $DescCuenta = strtoupper(trim(strval($this->request->getPost('DescCuenta'))));
                $NotDescCuenta = strtoupper(trim(strval($this->request->getPost('NotDescCuenta'))));

                $existe = array('existe' => false);

                $this->planContableModel = new ModelsPlanContable();

                $CuentaHijo = $this->planContableModel->getPlanContable($this->CodEmpresa, '', $CodCuenta, 'CodCuenta', 'CodCuenta != ' . $NotCodCuenta . '"', '');

                if (count($CuentaHijo) > 0) {
                    $existe = array('existe' => true, 'tipo' => 'codigo', 'codigo' => $CuentaHijo[0]['CodCuenta']);
                } else {
                    $this->planContableModel = new ModelsPlanContable();

                    $DescripcionHijo = $this->planContableModel->getPlanContable($this->CodEmpresa, '', '', 'DescCuenta', 'DescCuenta = "' . $DescCuenta . '" AND DescCuenta != "' . $NotDescCuenta . '"', '');

                    if (count($DescripcionHijo) > 0) {
                        $existe = array('existe' => true, 'tipo' => 'descripcion', 'codigo' => $DescripcionHijo[0]['DescCuenta']);
                    }
                }

                echo json_encode($existe);
            } else if ($tipo == 'verificar_ultimo_hijo') {
                $CodCuenta = trim(strval($this->request->getPost('CodCuenta')));
                $CuentaPadre = trim(strval($this->request->getPost('CuentaPadre')));

                $permite_amarre = array('permite' => false);

                if (strlen($CodCuenta) >= 3) {
                    $this->planContableModel = new ModelsPlanContable();

                    $es_cuenta_hija = $this->planContableModel->getPlanContable($this->CodEmpresa, '', $CodCuenta, 'CodCuenta', 'Child = 1', '');

                    if (count($es_cuenta_hija) > 0) {
                        $permite_amarre = array('permite' => true);
                    } else {
                        $this->planContableModel = new ModelsPlanContable();

                        $existe_cuentas_hijas = $this->planContableModel->getPlanContable($this->CodEmpresa, '', '', 'CodCuenta', 'CuentaPadre = "' . $CodCuenta . '"', '');

                        if (count($existe_cuentas_hijas) == 0 && !empty($CuentaPadre)) {
                            $permite_amarre = array('permite' => true);
                        }
                    }
                }

                echo json_encode($permite_amarre);
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

                $this->planContableModel = new ModelsPlanContable();

                $plan_contable = $this->planContableModel->getPlanContable($this->CodEmpresa, date('Y'), '', 'CodCuenta AS value, CONCAT(CodCuenta, " - ", DescCuenta) AS name, IF(Child = 0, CodCuenta, "") AS disabled', 'CodCuenta LIKE "' . $search . '%"', '');
            } else {
                $this->planContableModel = new ModelsPlanContable();

                $plan_contable = $this->planContableModel->getPlanContable($this->CodEmpresa, date('Y'), '', 'CodCuenta AS value, CONCAT(CodCuenta, " - ", DescCuenta) AS name, IF(Child = 0, CodCuenta, "") AS disabled', '', '');
            }

            echo json_encode($plan_contable);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}