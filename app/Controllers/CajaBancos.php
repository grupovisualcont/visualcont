<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Banco;
use App\Models\Cheque;
use App\Models\EntidadFinanciera;
use App\Models\Moneda;
use App\Models\PlanContable;

class CajaBancos extends BaseController
{
    protected $page;
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $cajaBancoModel;
    protected $entidadFinancieraModel;
    protected $planContableModel;
    protected $monedaModel;
    protected $chequeModel;

    public function __construct()
    {
        $this->page = 'Caja - Bancos';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->cajaBancoModel = new Banco();
        $this->entidadFinancieraModel = new EntidadFinanciera();
        $this->planContableModel = new PlanContable();
        $this->monedaModel = new Moneda();
        $this->chequeModel = new Cheque();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->cajaBancoModel = new Banco();

                $bancos = $this->cajaBancoModel->getBanco(
                    $this->CodEmpresa,
                    '',
                    date('Y'),
                    'banco.Codbanco, banco.CodEntidad, e.DescFinanciera, banco.abreviatura, banco.ctacte, banco.CodMoneda, m.DescMoneda, banco.codcuenta, p.DescCuenta, banco.Periodo, banco.Propio',
                    [
                        array('tabla' => 'entidadfinanciera e', 'on' => 'e.CodEntidad = banco.CodEntidad', 'tipo' => 'left'),
                        array('tabla' => 'moneda m', 'on' => 'm.CodMoneda = banco.CodMoneda', 'tipo' => 'left'),
                        array('tabla' => 'plan_contable p', 'on' => 'p.CodCuenta = banco.codcuenta AND p.CodEmpresa = banco.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    'banco.Codbanco ASC'
                );

                return viewApp($this->page, 'app/mantenience/box_banks/index', [
                    'bancos' => $bancos,
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
                $this->cajaBancoModel = new Banco();

                $caja_banco = $this->cajaBancoModel->getBanco($this->CodEmpresa, '', '', 'MAX(SUBSTRING(Codbanco, 3)) AS codigo', [], '', '');

                $codigo_maximo = 'BA001';

                if ($caja_banco[0]['codigo']) {
                    $caja_banco[0]['codigo'] = $caja_banco[0]['codigo'] + 1;

                    if (strlen($caja_banco[0]['codigo']) == 1) {
                        $codigo_maximo = 'BA00' . $caja_banco[0]['codigo'];
                    } else if (strlen($caja_banco[0]['codigo']) == 2) {
                        $codigo_maximo = 'BA0' . $caja_banco[0]['codigo'];
                    } else {
                        $codigo_maximo = 'BA' . $caja_banco[0]['codigo'];
                    }
                }

                $this->entidadFinancieraModel = new EntidadFinanciera();

                $entidades_financiera = $this->entidadFinancieraModel->getEntidadFinanciera('', [], '', '');

                $options_entidad_financiera = '<option value="" disabled selected>Seleccione</option>';

                foreach ($entidades_financiera as $indice => $valor) {
                    $options_entidad_financiera .= '<option value="' . $valor['CodEntidad'] . '">' . $valor['DescFinanciera'] . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $planes_contable = $this->planContableModel->getPlanContable(
                    $this->CodEmpresa,
                    '',
                    '',
                    'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled',
                    '',
                    ''
                );

                $options_plan_contable = '<option value="" disabled selected>Seleccione</option>';

                foreach ($planes_contable as $indice => $valor) {
                    $options_plan_contable .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->monedaModel = new Moneda();

                $monedas = $this->monedaModel->getMoneda('', '');

                $options_moneda = '';

                foreach ($monedas as $indice => $valor) {
                    $options_moneda .= '<option value="' . $valor['CodMoneda'] . '">' . $valor['DescMoneda'] . '</option>';
                }

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/box_banks/create.js']);

                return viewApp($this->page, 'app/mantenience/box_banks/create', [
                    'codigo_maximo' => $codigo_maximo,
                    'options_entidad_financiera' => $options_entidad_financiera,
                    'options_plan_contable' => $options_plan_contable,
                    'options_moneda' => $options_moneda,
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

    public function edit($CodBanco, $Periodo)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->cajaBancoModel = new Banco();

                $banco = $this->cajaBancoModel->getBanco($this->CodEmpresa, $CodBanco, $Periodo, '', [], '', '')[0];

                $this->entidadFinancieraModel = new EntidadFinanciera();

                $entidades_financiera = $this->entidadFinancieraModel->getEntidadFinanciera('', [], '', '');

                $options_entidad_financiera = '<option value="" disabled selected>Seleccione</option>';

                foreach ($entidades_financiera as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodEntidad'] == $banco['CodEntidad']) $selected = 'selected';

                    $options_entidad_financiera .= '<option value="' . $valor['CodEntidad'] . '" ' . $selected . '>' . $valor['DescFinanciera'] . '</option>';
                }

                $this->planContableModel = new PlanContable();

                $planes_contable = $this->planContableModel->getPlanContable(
                    $this->CodEmpresa,
                    '',
                    '',
                    'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled',
                    '',
                    ''
                );

                $options_plan_contable = '<option value="" disabled selected>Seleccione</option>';

                foreach ($planes_contable as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodCuenta'] == $banco['codcuenta']) $selected = 'selected';

                    $options_plan_contable .= '<option value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . ' ' . $selected . '>' . $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'] . '</option>';
                }

                $this->monedaModel = new Moneda();

                $monedas = $this->monedaModel->getMoneda('', '');

                $options_moneda = '';

                foreach ($monedas as $indice => $valor) {
                    $selected = '';

                    if ($valor['CodMoneda'] == $banco['CodMoneda']) $selected = 'selected';

                    $options_moneda .= '<option value="' . $valor['CodMoneda'] . '" ' . $selected . '>' . $valor['DescMoneda'] . '</option>';
                }

                $this->chequeModel = new Cheque();

                $cheques = $this->chequeModel->getCheque($this->CodEmpresa, $CodBanco, '', [], '', '');

                $this->empresa = new Empresa();

                $script = "
                    var id_cheque = " . (count($cheques) + 1) . ";
                    $('#CodEntidad').val('" . $banco['CodEntidad'] . "');
                    $('#codcuenta').val('" . $banco['codcuenta'] . "');
                    $('#CodMoneda').val('" . $banco['CodMoneda'] . "');
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/box_banks/edit.js']);

                return viewApp($this->page, 'app/mantenience/box_banks/edit', [
                    'banco' => $banco,
                    'options_entidad_financiera' => $options_entidad_financiera,
                    'options_plan_contable' => $options_plan_contable,
                    'options_moneda' => $options_moneda,
                    'cheques' => $cheques,
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

            $post['codcuenta'] = !empty($post['codcuenta']) ? $post['codcuenta'] : NULL;
            $post['Propio'] = isset($post['Propio']) ? $post['Propio'] : 0;
            $post['CodMoneda'] = isset($post['CodMoneda']) ? $post['CodMoneda'] : 'MO001';
            $post['PagoDetraccion'] = isset($post['PagoDetraccion']) ? $post['PagoDetraccion'] : 0;

            $this->cajaBancoModel = new Banco();

            $existe_codigo = $this->cajaBancoModel->getBanco($post['CodEmpresa'], $post['Codbanco'], '', '', [], '', '');

            if (count($existe_codigo) == 0) {
                $this->cajaBancoModel = new Banco();

                $this->cajaBancoModel->agregar($post);

                if (isset($post['DescCheque'])) {
                    $codigo_cheque = '';

                    foreach ($post['DescCheque'] as $indice => $valor) {
                        if (strlen($codigo_cheque) == 0 || strlen($codigo_cheque) == 5) {
                            $codigo_cheque = 'CH00' . ($indice + 1);
                        } else if (strlen($codigo_cheque) == 4) {
                            $codigo_cheque = 'CH0' . ($indice + 1);
                        } else {
                            $codigo_cheque = 'CH' . ($indice + 1);
                        }

                        $data = [
                            'CodBanco' => $post['Codbanco'],
                            'CodEmpresa' => $post['CodEmpresa'],
                            'CodCheque' => $codigo_cheque,
                            'DescCheque' => strtoupper(trim($valor)),
                            'nroinicial' => $post['nroinicial'][$indice],
                            'nrOfinal' => $post['nrOfinal'][$indice],
                            'numerador' => $post['numerador'][$indice],
                            'Estado' => 1
                        ];

                        $this->chequeModel = new Cheque();

                        $this->chequeModel->agregar($data);
                    }
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

            return redirect()->to(base_url('app/mantenience/box_banks/index'));
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

            $post['codcuenta'] = isset($post['codcuenta']) ? $post['codcuenta'] : NULL;
            $post['CodMoneda'] = isset($post['CodMoneda']) ? $post['CodMoneda'] : 'MO001';
            $post['ctacte'] = isset($post['ctacte']) ? $post['ctacte'] : '';
            $post['Propio'] = isset($post['Propio']) ? $post['Propio'] : 0;
            $post['PagoDetraccion'] = isset($post['PagoDetraccion']) ? $post['PagoDetraccion'] : 0;

            $this->cajaBancoModel = new Banco();

            $this->cajaBancoModel->actualizar($post['CodEmpresa'], $post['Codbanco'], $post);

            if (isset($post['DescCheque'])) {
                if (isset($post['idCheque'])) {
                    $ids = implode(',', $post['idCheque']);

                    $this->chequeModel = new Cheque();

                    $this->chequeModel->eliminar($post['CodEmpresa'], $post['Codbanco'], 'idCheque NOT IN (' . $ids . ')');
                } else {
                    $this->chequeModel = new Cheque();

                    $this->chequeModel->eliminar($post['CodEmpresa'], $post['Codbanco'], '');
                }

                $codigo_cheque = '';

                foreach ($post['DescCheque'] as $indice => $valor) {
                    if (strlen($codigo_cheque) == 0 || strlen($codigo_cheque) == 5) {
                        $codigo_cheque = 'CH00' . ($indice + 1);
                    } else if (strlen($codigo_cheque) == 4) {
                        $codigo_cheque = 'CH0' . ($indice + 1);
                    } else {
                        $codigo_cheque = 'CH' . ($indice + 1);
                    }

                    $data = [
                        'CodBanco' => $post['Codbanco'],
                        'CodEmpresa' => $post['CodEmpresa'],
                        'CodCheque' => $codigo_cheque,
                        'DescCheque' => strtoupper(trim($valor)),
                        'nroinicial' => $post['nroinicial'][$indice],
                        'nrOfinal' => $post['nrOfinal'][$indice],
                        'numerador' => $post['numerador'][$indice],
                        'Estado' => 1
                    ];

                    if (isset($post['idCheque'][$indice]) && !empty($post['idCheque'][$indice])) {
                        $this->chequeModel = new Cheque();

                        $this->chequeModel->actualizar($post['CodEmpresa'], $post['idCheque'][$indice], $data);

                        $codigo_cheque = $post['CodCheque'][$indice];
                    } else {
                        $this->chequeModel = new Cheque();

                        $this->chequeModel->agregar($data);
                    }
                }
            } else {
                $this->chequeModel = new Cheque();

                $this->chequeModel->eliminar($post['CodEmpresa'], $post['Codbanco'], '');
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

            return redirect()->to(base_url('app/mantenience/box_banks/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($CodBanco, $Periodo)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->cajaBancoModel = new Banco();

            $this->cajaBancoModel->eliminar($this->CodEmpresa, $CodBanco, $Periodo);

            $this->chequeModel = new Cheque();

            $this->chequeModel->eliminar($this->CodEmpresa, $CodBanco, '');

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

            return redirect()->to(base_url('app/mantenience/box_banks/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Caja - Bancos - Reporte');

            $columnas = array('Código', 'Banco', 'Abreviado', 'Cta. Cte', 'Moneda', 'CodMoneda', 'Cuenta');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $this->cajaBancoModel = new Banco();

            $result = $this->cajaBancoModel->getBanco(
                $this->CodEmpresa,
                '',
                '',
                'banco.Codbanco, banco.CodEntidad, e.DescFinanciera, banco.abreviatura, banco.ctacte, banco.CodMoneda, m.DescMoneda, banco.codcuenta, p.DescCuenta, banco.Propio',
                [
                    array('tabla' => 'entidadfinanciera e', 'on' => 'e.CodEntidad = banco.CodEntidad', 'tipo' => 'left'),
                    array('tabla' => 'moneda m', 'on' => 'm.CodMoneda = banco.CodMoneda', 'tipo' => 'left'),
                    array('tabla' => 'plan_contable p', 'on' => 'p.CodCuenta = banco.codcuenta AND p.CodEmpresa = banco.CodEmpresa', 'tipo' => 'left')
                ],
                '',
                'banco.Codbanco ASC'
            );

            foreach ($result as  $indice => $valor) {
                $values = array(
                    $valor['Codbanco'],
                    $valor['DescFinanciera'],
                    $valor['abreviatura'],
                    $valor['ctacte'],
                    $valor['DescMoneda'],
                    $valor['CodMoneda'],
                    $valor['codcuenta']
                );

                $excel->setValues($values);

                $excel->body($indice + 2, 'valor');
            }

            $excel->footer('caja_bancos_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $this->cajaBancoModel = new Banco();

            $result = $this->cajaBancoModel->getBanco(
                $this->CodEmpresa,
                '',
                '',
                'banco.Codbanco, banco.CodEntidad, e.DescFinanciera, banco.abreviatura, banco.ctacte, banco.CodMoneda, m.DescMoneda, banco.codcuenta, p.DescCuenta, banco.Propio',
                [
                    array('tabla' => 'entidadfinanciera e', 'on' => 'e.CodEntidad = banco.CodEntidad', 'tipo' => 'left'),
                    array('tabla' => 'moneda m', 'on' => 'm.CodMoneda = banco.CodMoneda', 'tipo' => 'left'),
                    array('tabla' => 'plan_contable p', 'on' => 'p.CodCuenta = banco.codcuenta AND p.CodEmpresa = banco.CodEmpresa', 'tipo' => 'left')
                ],
                '',
                'banco.Codbanco ASC'
            );

            $columnas = array('Código', 'Banco', 'Abreviado', 'Cta. Cte', 'Moneda', 'CodMoneda', 'Cuenta');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['Codbanco'] . '</td>
                    <td align="left">' . $valor['DescFinanciera'] . '</td>
                    <td align="left">' . $valor['abreviatura'] . '</td>
                    <td align="left">' . $valor['ctacte'] . '</td>
                    <td align="left">' . $valor['DescMoneda'] . '</td>
                    <td align="left">' . $valor['CodMoneda'] . '</td>
                    <td align="left">' . $valor['codcuenta'] . '</td>
                <tr>
            ';
            }

            $pdf = new PDF();

            $pdf->setFilename('caja_bancos_reporte');
            $pdf->creacion('Caja - Bancos - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
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

                $this->cajaBancoModel = new Banco();

                $caja_bancos = $this->cajaBancoModel->getBanco($this->CodEmpresa, '', '', 'Codbanco AS value, CONCAT(Codbanco, " - ", abreviatura) AS name', [], 'abreviatura LIKE "%' . $search . '%"', '');
            } else {
                $this->cajaBancoModel = new Banco();

                $caja_bancos = $this->cajaBancoModel->getBanco($this->CodEmpresa, '', '', 'Codbanco AS value, CONCAT(Codbanco, " - ", abreviatura) AS name', [], '', '');
            }

            echo json_encode($caja_bancos);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
