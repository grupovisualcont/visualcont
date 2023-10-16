<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConceptoPres;
use App\Models\PlanContable;
use App\Models\VoucherPres;

class Presupuesto extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Presupuesto';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $concepto_pres = (new ConceptoPres())->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '
                        CASE
                            WHEN LENGTH(CodConceptoPres) = 2 THEN 1
                            WHEN LENGTH(CodConceptoPres) = 4 THEN 2
                            WHEN LENGTH(CodConceptoPres) = 7 THEN 3
                            WHEN LENGTH(CodConceptoPres) = 10 THEN 4
                        END
                        AS id,
                        CASE
                            WHEN LENGTH(CodConceptoPres) = 2 THEN "Nivel 1"
                            WHEN LENGTH(CodConceptoPres) = 4 THEN "Nivel 2"
                            WHEN LENGTH(CodConceptoPres) = 7 THEN "Nivel 3"
                            WHEN LENGTH(CodConceptoPres) = 10 THEN "Concepto Presupuestal"
                        END
                        AS nivel, CodConceptoPres AS codigo, descConceptoPres AS descripcion, CodCuenta As cuenta
                        ',
                    [],
                    '',
                    'CodConceptoPres ASC'
                );

                $voucher_pres = (new VoucherPres())->getVoucherPres($this->CodEmpresa, '', '5 AS id, "Proyectos" AS nivel, CodVoucherPre AS codigo, DescVoucherPre AS descripcion, "" AS cuenta', [], '', '');

                $datos = array_merge($concepto_pres, $voucher_pres);

                return viewApp($this->page, 'app/mantenience/budget/index', [
                    'datos' => $datos,
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
                $niveles = array('Nivel 1', 'Nivel 2', 'Nivel 3', 'Concepto Presupuestal', 'Proyectos');

                $options_niveles = '';

                foreach ($niveles as $indice => $valor) {
                    $selected = '';

                    if ($indice == 0) $selected = 'selected';

                    $options_niveles .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $nivel_1 = (new ConceptoPres())->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    'CAST(MAX(CodConceptoPres) AS UNSIGNED) AS CodConceptoPres',
                    [],
                    'LENGTH(CodConceptoPres) = 2',
                    ''
                )[0]['CodConceptoPres'];

                $codigo_nivel_1 = '01';

                if ($nivel_1 != NULL) {
                    switch (strlen($nivel_1)) {
                        case 1:
                            $codigo_nivel_1 = '0' . ($nivel_1 + 1);

                            break;
                        case 2:
                            $codigo_nivel_1 = ($nivel_1 + 1);

                            break;
                    }
                }

                $nivel_5 = (new VoucherPres())->getVoucherPres(
                    $this->CodEmpresa,
                    '',
                    'CAST(MAX(CodVoucherPre) AS UNSIGNED) AS CodVoucherPre',
                    [],
                    '',
                    ''
                )[0]['CodVoucherPre'];

                $codigo_nivel_5 = '01';

                if ($nivel_5 != NULL) {
                    switch (strlen($nivel_5)) {
                        case 1:
                            $codigo_nivel_5 = '0' . ($nivel_5 + 1);

                            break;
                        case 2:
                            $codigo_nivel_5 = ($nivel_5 + 1);

                            break;
                    }
                }

                $script = (new Empresa())->generar_script(['app/mantenience/budget/create.js']);

                return viewApp($this->page, 'app/mantenience/budget/create', [
                    'options_niveles' => $options_niveles,
                    'codigo_nivel_1' => $codigo_nivel_1,
                    'codigo_nivel_5' => $codigo_nivel_5,
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

    public function edit($Nivel, $Codigo)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                if ($Nivel <= 4) {
                    $presupuesto = (new ConceptoPres())->getConceptoPres($this->CodEmpresa, $Codigo, 'CodEmpresa, CodConceptoPres AS codigo, descConceptoPres AS descripcion, CodCuenta', [], '', '')[0];
                } else if ($Nivel == 5) {
                    $presupuesto = (new VoucherPres())->getVoucherPres($this->CodEmpresa, $Codigo, 'CodEmpresa, CodVoucherPre AS codigo, DescVoucherPre AS descripcion', [], '', '')[0];
                }

                $niveles = array('Nivel 1', 'Nivel 2', 'Nivel 3', 'Concepto Presupuestal', 'Proyectos');

                $options_niveles = '';

                foreach ($niveles as $indice => $valor) {
                    $selected = '';

                    if (($indice + 1) == $Nivel) $selected = 'selected';

                    $options_niveles .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $nivel_1 = (new ConceptoPres())->getConceptoPres($this->CodEmpresa, substr($presupuesto['codigo'], 0, 2), '', [], 'LENGTH(CodConceptoPres) = 2', '');

                $option_nivel_1 = count($nivel_1) > 0 ? '<option value="' . $nivel_1[0]['CodConceptoPres'] . '">' . $nivel_1[0]['descConceptoPres'] . '</option>' : '';

                $nivel_2 = (new ConceptoPres())->getConceptoPres($this->CodEmpresa, substr($presupuesto['codigo'], 0, 4), '', [], 'LENGTH(CodConceptoPres) = 4', '');

                $option_nivel_2 = count($nivel_2) > 0 ? '<option value="' . $nivel_2[0]['CodConceptoPres'] . '">' . $nivel_2[0]['descConceptoPres'] . '</option>' : '';

                $nivel_3 = (new ConceptoPres())->getConceptoPres($this->CodEmpresa, substr($presupuesto['codigo'], 0, 7), '', [], 'LENGTH(CodConceptoPres) = 7', '');

                $option_nivel_3 = count($nivel_3) > 0 ? '<option value="' . $nivel_3[0]['CodConceptoPres'] . '">' . $nivel_3[0]['descConceptoPres'] . '</option>' : '';

                $option_plan_contable = '';

                if (isset($presupuesto['CodCuenta'])) {
                    $plan_contable = (new PlanContable())->getPlanContable(
                        $this->CodEmpresa,
                        date('Y'),
                        $presupuesto['CodCuenta'],
                        'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled',
                        [],
                        '',
                        'CodCuenta ASC'
                    )[0];

                    $option_plan_contable = '<option value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';
                }

                $script = (new Empresa())->generar_script(['app/mantenience/budget/edit.js']);

                return viewApp($this->page, 'app/mantenience/budget/edit', [
                    'presupuesto' => $presupuesto,
                    'nivel' => $Nivel,
                    'options_niveles' => $options_niveles,
                    'option_nivel_1' => $option_nivel_1,
                    'option_nivel_2' => $option_nivel_2,
                    'option_nivel_3' => $option_nivel_3,
                    'option_plan_contable' => $option_plan_contable,
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

            $Tipo = $post['Tipo'] + 1;

            if ($Tipo <= 4) {
                $post['CodConceptoPres'] = $post['CodConceptoPres' . $Tipo];
                $post['descConceptoPres'] = strtoupper(trim($post['descConceptoPres' . $Tipo]));

                $existe_codigo = (new ConceptoPres())->getConceptoPres($post['CodEmpresa'], $post['CodConceptoPres'], '', [], '', '');

                if (count($existe_codigo) == 0) {
                    (new ConceptoPres())->agregar($post);
                }
            } else {
                $post['DescVoucherPre'] = strtoupper(trim($post['DescVoucherPre']));

                $existe_codigo = (new VoucherPres())->getVoucherPres($post['CodEmpresa'], $post['CodVoucherPre'], '', [], '', '');

                if (count($existe_codigo) == 0) {
                    (new VoucherPres())->agregar($post);
                }
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

            return redirect()->to(base_url('app/mantenience/budget/index'));
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

            if ($post['Nivel'] <= 4) {
                $post['CodConceptoPres'] = $post['CodConceptoPres' . $post['Nivel']];
                $post['descConceptoPres'] = strtoupper(trim($post['descConceptoPres' . $post['Nivel']]));

                (new ConceptoPres())->actualizar($post['CodEmpresa'], $post['CodConceptoPres'], $post);
            } else if ($post['Nivel'] == 5) {
                $post['DescVoucherPre'] = strtoupper(trim($post['DescVoucherPre']));

                (new VoucherPres())->actualizar($post['CodEmpresa'], $post['CodVoucherPre'], $post);
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

            return redirect()->to(base_url('app/mantenience/budget/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($Nivel, $Codigo)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            if ($Nivel <= 4) {
                (new ConceptoPres())->eliminar($this->CodEmpresa, $Codigo);
            } else if ($Nivel == 5) {
                (new VoucherPres())->where($this->CodEmpresa, $Codigo);
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

            return redirect()->to(base_url('app/mantenience/budget/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Presupuesto - Reporte');

            $columnas = array('Tipo', 'Código', 'Descripción', 'CodCuenta');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $concepto_pres = (new ConceptoPres())->getConceptoPres(
                $this->CodEmpresa,
                '',
                '
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN 1
                        WHEN LENGTH(CodConceptoPres) = 4 THEN 2
                        WHEN LENGTH(CodConceptoPres) = 7 THEN 3
                        WHEN LENGTH(CodConceptoPres) = 10 THEN 4
                    END
                    AS id,
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN "Nivel 1"
                        WHEN LENGTH(CodConceptoPres) = 4 THEN "Nivel 2"
                        WHEN LENGTH(CodConceptoPres) = 7 THEN "Nivel 3"
                        WHEN LENGTH(CodConceptoPres) = 10 THEN "Concepto Presupuestal"
                    END
                    AS nivel, CodConceptoPres AS codigo, descConceptoPres AS descripcion, CodCuenta As cuenta
                    ',
                [],
                '',
                'CodConceptoPres ASC'
            );

            $voucher_pres = (new VoucherPres())->getVoucherPres($this->CodEmpresa, '', '5 AS id, "Proyectos" AS nivel, CodVoucherPre AS codigo, DescVoucherPre AS descripcion, "" AS cuenta', [], '', '');

            $result = array_merge($concepto_pres, $voucher_pres);

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['nivel'],
                    $valor['codigo'],
                    $valor['descripcion'],
                    $valor['cuenta']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('presupuesto_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $concepto_pres = (new ConceptoPres())->getConceptoPres(
                $this->CodEmpresa,
                '',
                '
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN 1
                        WHEN LENGTH(CodConceptoPres) = 4 THEN 2
                        WHEN LENGTH(CodConceptoPres) = 7 THEN 3
                        WHEN LENGTH(CodConceptoPres) = 10 THEN 4
                    END
                    AS id,
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN "Nivel 1"
                        WHEN LENGTH(CodConceptoPres) = 4 THEN "Nivel 2"
                        WHEN LENGTH(CodConceptoPres) = 7 THEN "Nivel 3"
                        WHEN LENGTH(CodConceptoPres) = 10 THEN "Concepto Presupuestal"
                    END
                    AS nivel, CodConceptoPres AS codigo, descConceptoPres AS descripcion, CodCuenta As cuenta
                    ',
                [],
                '',
                'CodConceptoPres ASC'
            );

            $voucher_pres = (new VoucherPres())->getVoucherPres($this->CodEmpresa, '', '5 AS id, "Proyectos" AS nivel, CodVoucherPre AS codigo, DescVoucherPre AS descripcion, "" AS cuenta', [], '', '');

            $result = array_merge($concepto_pres, $voucher_pres);

            $columnas = array('Tipo', 'Código', 'Descripción', 'CodCuenta');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['nivel'] . '</td>
                    <td align="left">' . $valor['codigo'] . '</td>
                    <td align="left">' . $valor['descripcion'] . '</td>
                    <td align="left">' . $valor['cuenta'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('presupuesto_reporte');
            $pdf->creacion('Presupuesto - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_codigo()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $Nivel = $this->request->getPost('Nivel');
            $Niveles1 = $this->request->getPost('Niveles1');
            $Niveles2 = $this->request->getPost('Niveles2');
            $Niveles3 = $this->request->getPost('Niveles3');

            if ($tipo == 'nuevo') {
                switch ($Nivel) {
                    case 1:
                    case 5:
                        $length = 2;
                        $resta = 1;
                        break;
                    case 2:
                        $length = 4;
                        $resta = 1;
                        break;
                    case 3:
                        $length = 7;
                        $resta = 2;
                        break;
                    case 4:
                        $length = 10;
                        $resta = 2;
                        break;
                }

                if ($Nivel < 5) {
                    if (!empty($Niveles1)) {
                        $codigo = $Niveles1;

                        if ($Nivel == 2) {
                            $termina = '01';
                        } else {
                            $termina = '';
                        }
                    }

                    if (!empty($Niveles2)) {
                        $codigo = $Niveles2;

                        if ($Nivel == 3) {
                            $termina = '001';
                        } else {
                            $termina = '';
                        }
                    }

                    if (!empty($Niveles3)) {
                        $codigo = $Niveles3;

                        if ($Nivel == 4) {
                            $termina = '001';
                        } else {
                            $termina = '';
                        }
                    }

                    $datos = array('codigo' => $codigo . $termina);

                    $concepto_pres = (new ConceptoPres())->getConceptoPres(
                        $this->CodEmpresa,
                        '',
                        'CAST(MAX(SUBSTRING(CodConceptoPres, ' . ($length - $resta) . ')) AS UNSIGNED) AS CodConceptoPres',
                        [],
                        'CodConceptoPres LIKE "' . $codigo . '%" AND LENGTH(CodConceptoPres) = ' . $length,
                        ''
                    );

                    if ($concepto_pres[0]['CodConceptoPres'] != NULL) {
                        $concepto_pres[0]['CodConceptoPres'] = $concepto_pres[0]['CodConceptoPres'] + 1;

                        switch ($Nivel) {
                            case 2:
                                if (strlen($codigo) == 2) {
                                    if (strlen($concepto_pres[0]['CodConceptoPres']) == 1) {
                                        $codigo .= '0' . $concepto_pres[0]['CodConceptoPres'];
                                    } else {
                                        $codigo .= $concepto_pres[0]['CodConceptoPres'];
                                    }
                                }
                                break;
                            case 3:
                                if (strlen($codigo) == 4) {
                                    if (strlen($concepto_pres[0]['CodConceptoPres']) == 1) {
                                        $codigo .= '00' . $concepto_pres[0]['CodConceptoPres'];
                                    } else if (strlen($concepto_pres[0]['CodConceptoPres']) == 2) {
                                        $codigo .= '0' . $concepto_pres[0]['CodConceptoPres'];
                                    } else {
                                        $codigo .= $concepto_pres[0]['CodConceptoPres'];
                                    }
                                }
                                break;
                            case 4:
                                if (strlen($codigo) == 7) {
                                    if (strlen($concepto_pres[0]['CodConceptoPres']) == 1) {
                                        $codigo .= '00' . $concepto_pres[0]['CodConceptoPres'];
                                    } else if (strlen($concepto_pres[0]['CodConceptoPres']) == 2) {
                                        $codigo .= '0' . $concepto_pres[0]['CodConceptoPres'];
                                    } else {
                                        $codigo .= $concepto_pres[0]['CodConceptoPres'];
                                    }
                                }
                                break;
                        }

                        $datos = array('codigo' => $codigo);
                    }

                    echo json_encode($datos);
                }
            } else if ($tipo == 'options_nivel_2') {
                $nivel_2 = (new ConceptoPres())->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'CodConceptoPres LIKE "' . $Niveles1 . '%" AND LENGTH(CodConceptoPres) = 4',
                    ''
                );

                $options_nivel_2 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_2 as $indice => $valor) {
                    $options_nivel_2 .= '<option value="' . $valor['CodConceptoPres'] . '">' . $valor['descConceptoPres'] . '</option>';
                }

                echo $options_nivel_2;
            } else if ($tipo == 'options_nivel_3') {
                $nivel_3 = (new ConceptoPres())->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'CodConceptoPres LIKE "' . $Niveles2 . '%" AND LENGTH(CodConceptoPres) = 7',
                    ''
                );

                $options_nivel_3 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_3 as $indice => $valor) {
                    $options_nivel_3 .= '<option value="' . $valor['CodConceptoPres'] . '">' . $valor['descConceptoPres'] . '</option>';
                }

                echo $options_nivel_3;
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
