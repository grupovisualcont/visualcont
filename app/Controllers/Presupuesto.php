<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ConceptoPres;
use App\Models\PlanContable;
use App\Models\VoucherPres;

class Presupuesto extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $conceptoPresModel;
    protected $voucherPresModel;
    protected $planContableModel;

    public function __construct()
    {
        $this->page = 'Presupuesto';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->conceptoPresModel = new ConceptoPres();
        $this->voucherPresModel = new VoucherPres();
        $this->planContableModel = new PlanContable();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->conceptoPresModel = new ConceptoPres();

                $concepto_pres = $this->conceptoPresModel->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '
                        CASE
                            WHEN LENGTH(CodConceptoPres) = 2 THEN 1
                            WHEN LENGTH(CodConceptoPres) = 4 THEN 2
                            WHEN LENGTH(CodConceptoPres) = 7 THEN 3
                            WHEN LENGTH(CodConceptoPres) = 10 THEN 4
                        END
                        AS Id,
                        CASE
                            WHEN LENGTH(CodConceptoPres) = 2 THEN "Nivel 1"
                            WHEN LENGTH(CodConceptoPres) = 4 THEN "Nivel 2"
                            WHEN LENGTH(CodConceptoPres) = 7 THEN "Nivel 3"
                            WHEN LENGTH(CodConceptoPres) = 10 THEN "Concepto Presupuestal"
                        END
                        AS Nivel, CodConceptoPres, descConceptoPres, CodCuenta
                        ',
                    [],
                    '',
                    'CodConceptoPres ASC'
                );

                $this->voucherPresModel = new VoucherPres();

                $voucher_pres = $this->voucherPresModel->getVoucherPres($this->CodEmpresa, '', '', [], '', '');

                $datos = array();

                foreach ($concepto_pres as $indice => $valor) {
                    $datos[] = array('Id' => $valor['Id'], 'Nivel' => $valor['Nivel'], 'Codigo' => $valor['CodConceptoPres'], 'Descripcion' => $valor['descConceptoPres'], 'Cuenta' => $valor['CodCuenta']);
                }

                foreach ($voucher_pres as $indice => $valor) {
                    $datos[] = array('Id' => 5, 'Nivel' => 'Proyectos', 'Codigo' => $valor['CodVoucherPre'], 'Descripcion' => $valor['DescVoucherPre'], 'Cuenta' => '');
                }

                return viewApp($this->page, 'app/mantenience/budget/index', [
                    'datos' => $datos,
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
                $niveles = array('Nivel 1', 'Nivel 2', 'Nivel 3', 'Concepto Presupuestal', 'Proyectos');

                $options_niveles = '';

                foreach ($niveles as $indice => $valor) {
                    $selected = '';

                    if ($indice == 0) $selected = 'selected';

                    $options_niveles .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_1 = $this->conceptoPresModel->getConceptoPres(
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

                $this->voucherPresModel = new VoucherPres();

                $nivel_5 = $this->voucherPresModel->getVoucherPres(
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

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_1 = $this->conceptoPresModel->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'LENGTH(CodConceptoPres) = 2',
                    ''
                );

                $options_nivel_1 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_1 as $indice => $valor) {
                    $options_nivel_1 .= '<option value="' . $valor['CodConceptoPres'] . '">' . $valor['descConceptoPres'] . '</option>';
                }

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_2 = $this->conceptoPresModel->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'LENGTH(CodConceptoPres) = 4',
                    ''
                );

                $options_nivel_2 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_2 as $indice => $valor) {
                    $options_nivel_2 .= '<option value="' . $valor['CodConceptoPres'] . '">' . $valor['descConceptoPres'] . '</option>';
                }

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_3 = $this->conceptoPresModel->getConceptoPres(
                    $this->CodEmpresa,
                    '',
                    '',
                    [],
                    'LENGTH(CodConceptoPres) = 7',
                    ''
                );

                $options_nivel_3 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_3 as $indice => $valor) {
                    $options_nivel_3 .= '<option value="' . $valor['CodConceptoPres'] . '">' . $valor['descConceptoPres'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/budget/create.js']);

                return viewApp($this->page, 'app/mantenience/budget/create', [
                    'options_niveles' => $options_niveles,
                    'codigo_nivel_1' => $codigo_nivel_1,
                    'codigo_nivel_5' => $codigo_nivel_5,
                    'options_nivel_1' => $options_nivel_1,
                    'options_nivel_2' => $options_nivel_2,
                    'options_nivel_3' => $options_nivel_3,
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

    public function edit($Nivel, $Codigo)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                if ($Nivel <= 4) {
                    $this->conceptoPresModel = new ConceptoPres();

                    $presupuesto = $this->conceptoPresModel->getConceptoPres($this->CodEmpresa, $Codigo, '', [], '', '')[0];
                } else if ($Nivel == 5) {
                    $this->voucherPresModel = new VoucherPres();

                    $presupuesto = $this->voucherPresModel->getVoucherPres($this->CodEmpresa, $Codigo, '', [], '', '')[0];
                }

                $niveles = array('Nivel 1', 'Nivel 2', 'Nivel 3', 'Concepto Presupuestal', 'Proyectos');

                $options_niveles = '';

                foreach ($niveles as $indice => $valor) {
                    $selected = '';

                    if (($indice + 1) == $Nivel) $selected = 'selected';

                    $options_niveles .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_1 = $this->conceptoPresModel->getConceptoPres($this->CodEmpresa, '', '', [], 'LENGTH(CodConceptoPres) = 2', '');

                $options_nivel_1 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_1 as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodConceptoPres'] == substr($presupuesto['CodConceptoPres'], 0, 2)) $selected = 'selected';

                    $options_nivel_1 .= '<option value="' . $valor['CodConceptoPres'] . '" ' . $selected . '>' . $valor['descConceptoPres'] . '</option>';
                }

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_2 = $this->conceptoPresModel->getConceptoPres($this->CodEmpresa, '', '', [], 'LENGTH(CodConceptoPres) = 4', '');

                $options_nivel_2 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_2 as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodConceptoPres'] == substr($presupuesto['CodConceptoPres'], 0, 4)) $selected = 'selected';

                    $options_nivel_2 .= '<option value="' . $valor['CodConceptoPres'] . '" ' . $selected . '>' . $valor['descConceptoPres'] . '</option>';
                }

                $this->conceptoPresModel = new ConceptoPres();

                $nivel_3 = $this->conceptoPresModel->getConceptoPres($this->CodEmpresa, '', '', [], 'LENGTH(CodConceptoPres) = 7', '');

                $options_nivel_3 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($nivel_3 as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodConceptoPres'] == substr($presupuesto['CodConceptoPres'], 0, 7)) $selected = 'selected';

                    $options_nivel_3 .= '<option value="' . $valor['CodConceptoPres'] . '" ' . $selected . '>' . $valor['descConceptoPres'] . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $planes_contable = $this->planContableModel->getPlanContable(
                    $this->CodEmpresa,
                    date('Y'),
                    $presupuesto['CodCuenta'],
                    'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled',
                    '',
                    'CodCuenta ASC'
                );

                $options_planes_contable = '<option value="" disabled selected>Seleccione</option>';

                foreach ($planes_contable as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodCuenta'] == $presupuesto['CodCuenta']) $selected = 'selected';

                    $options_planes_contable .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . ' ' . $selected . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/budget/edit.js']);

                return viewApp($this->page, 'app/mantenience/budget/edit', [
                    'presupuesto' => $presupuesto,
                    'nivel' => $Nivel,
                    'options_niveles' => $options_niveles,
                    'options_nivel_1' => $options_nivel_1,
                    'options_nivel_2' => $options_nivel_2,
                    'options_nivel_3' => $options_nivel_3,
                    'options_planes_contable' => $options_planes_contable,
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

            $Tipo = $post['Tipo'] + 1;

            if ($Tipo <= 4) {
                $post['CodConceptoPres'] = $post['CodConceptoPres' . $Tipo];
                $post['descConceptoPres'] = strtoupper(trim($post['descConceptoPres' . $Tipo]));

                $this->conceptoPresModel = new ConceptoPres();

                $existe_codigo = $this->conceptoPresModel->getConceptoPres($post['CodEmpresa'], $post['CodConceptoPres'], '', [], '', '');

                if (count($existe_codigo) == 0) {
                    $this->conceptoPresModel = new ConceptoPres();

                    $this->conceptoPresModel->agregar($post);
                }
            } else {
                $post['DescVoucherPre'] = strtoupper(trim($post['DescVoucherPre']));

                $this->voucherPresModel = new VoucherPres();

                $existe_codigo = $this->voucherPresModel->getVoucherPres($post['CodEmpresa'], $post['CodVoucherPre'], '', [], '', '');

                if (count($existe_codigo) == 0) {
                    $this->voucherPresModel = new VoucherPres();

                    $this->voucherPresModel->agregar($post);
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

                $this->conceptoPresModel = new ConceptoPres();

                $this->conceptoPresModel->actualizar($post['CodEmpresa'], $post['CodConceptoPres'], $post);
            } else if ($post['Nivel'] == 5) {
                $post['DescVoucherPre'] = strtoupper(trim($post['DescVoucherPre']));

                $this->voucherPresModel = new VoucherPres();

                $this->voucherPresModel->actualizar($post['CodEmpresa'], $post['CodVoucherPre'], $post);
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
                $this->conceptoPresModel = new ConceptoPres();

                $this->conceptoPresModel->eliminar($this->CodEmpresa, $Codigo);
            } else if ($Nivel == 5) {
                $this->voucherPresModel = new VoucherPres();

                $this->voucherPresModel->where($this->CodEmpresa, $Codigo);
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

            $this->conceptoPresModel = new ConceptoPres();

            $concepto_pres = $this->conceptoPresModel->getConceptoPres(
                $this->CodEmpresa,
                '',
                '
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN 1
                        WHEN LENGTH(CodConceptoPres) = 4 THEN 2
                        WHEN LENGTH(CodConceptoPres) = 7 THEN 3
                        WHEN LENGTH(CodConceptoPres) = 10 THEN 4
                    END
                    AS Id,
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN "Nivel 1"
                        WHEN LENGTH(CodConceptoPres) = 4 THEN "Nivel 2"
                        WHEN LENGTH(CodConceptoPres) = 7 THEN "Nivel 3"
                        WHEN LENGTH(CodConceptoPres) = 10 THEN "Concepto Presupuestal"
                    END
                    AS Nivel, CodConceptoPres, descConceptoPres, CodCuenta
                ',
                [],
                '',
                'CodConceptoPres ASC'
            );

            $this->voucherPresModel = new VoucherPres();

            $voucher_pres = $this->voucherPresModel->getVoucherPres($this->CodEmpresa, '', '', [], '', '');

            $result = array();

            foreach ($concepto_pres as $indice => $valor) {
                $result[] = array('Id' => $valor['Id'], 'Nivel' => $valor['Nivel'], 'Codigo' => $valor['CodConceptoPres'], 'Descripcion' => $valor['descConceptoPres'], 'Cuenta' => $valor['CodCuenta']);
            }

            foreach ($voucher_pres as $indice => $valor) {
                $result[] = array('Id' => 5, 'Nivel' => 'Proyectos', 'Codigo' => $valor['CodVoucherPre'], 'Descripcion' => $valor['DescVoucherPre'], 'Cuenta' => '');
            }

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['Nivel'],
                    $valor['Codigo'],
                    $valor['Descripcion'],
                    $valor['Cuenta']
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
            $this->conceptoPresModel = new ConceptoPres();

            $concepto_pres = $this->conceptoPresModel->getConceptoPres(
                $this->CodEmpresa,
                '',
                '
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN 1
                        WHEN LENGTH(CodConceptoPres) = 4 THEN 2
                        WHEN LENGTH(CodConceptoPres) = 7 THEN 3
                        WHEN LENGTH(CodConceptoPres) = 10 THEN 4
                    END
                    AS Id,
                    CASE
                        WHEN LENGTH(CodConceptoPres) = 2 THEN "Nivel 1"
                        WHEN LENGTH(CodConceptoPres) = 4 THEN "Nivel 2"
                        WHEN LENGTH(CodConceptoPres) = 7 THEN "Nivel 3"
                        WHEN LENGTH(CodConceptoPres) = 10 THEN "Concepto Presupuestal"
                    END
                    AS Nivel, CodConceptoPres, descConceptoPres, CodCuenta
                ',
                [],
                '',
                'CodConceptoPres ASC'
            );

            $this->voucherPresModel = new VoucherPres();

            $voucher_pres = $this->voucherPresModel->getVoucherPres($this->CodEmpresa, '', '', [], '', '');

            $result = array();

            foreach ($concepto_pres as $indice => $valor) {
                $result[] = array('Id' => $valor['Id'], 'Nivel' => $valor['Nivel'], 'Codigo' => $valor['CodConceptoPres'], 'Descripcion' => $valor['descConceptoPres'], 'Cuenta' => $valor['CodCuenta']);
            }

            foreach ($voucher_pres as $indice => $valor) {
                $result[] = array('Id' => 5, 'Nivel' => 'Proyectos', 'Codigo' => $valor['CodVoucherPre'], 'Descripcion' => $valor['DescVoucherPre'], 'Cuenta' => '');
            }

            $columnas = array('Tipo', 'Código', 'Descripción', 'CodCuenta');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['Nivel'] . '</td>
                    <td align="left">' . $valor['Codigo'] . '</td>
                    <td align="left">' . $valor['Descripcion'] . '</td>
                    <td align="left">' . $valor['Cuenta'] . '</td>
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

                    $this->conceptoPresModel = new ConceptoPres();

                    $concepto_pres = $this->conceptoPresModel->getConceptoPres(
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
                $this->conceptoPresModel = new ConceptoPres();

                $nivel_2 = $this->conceptoPresModel->getConceptoPres(
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
                $this->conceptoPresModel = new ConceptoPres();

                $nivel_3 = $this->conceptoPresModel->getConceptoPres(
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
