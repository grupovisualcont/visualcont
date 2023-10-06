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
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Caja - Bancos';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $bancos = (new Banco())->getBanco(
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
                $caja_banco = (new Banco())->getBanco($this->CodEmpresa, '', '', 'MAX(SUBSTRING(Codbanco, 3)) AS codigo', [], '', '');

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

                $script = (new Empresa())->generar_script('', ['app/mantenience/box_banks/create.js']);

                return viewApp($this->page, 'app/mantenience/box_banks/create', [
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

    public function edit($CodBanco, $Periodo)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $banco = (new Banco())->getBanco($this->CodEmpresa, $CodBanco, $Periodo, '', [], '', '')[0];

                $entidad_financiera = (new EntidadFinanciera())->getEntidadFinanciera($banco['CodEntidad'], '', [], '', '')[0];

                $option_entidad_financiera = '<option value="' . $entidad_financiera['CodEntidad'] . '">' . $entidad_financiera['DescFinanciera'] . '</option>';

                $option_plan_contable = '';

                if (isset($banco['codcuenta'])) {
                    $plan_contable = (new PlanContable())->getPlanContable(
                        $this->CodEmpresa,
                        '',
                        $banco['codcuenta'],
                        'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled',
                        [],
                        '',
                        ''
                    )[0];

                    $option_plan_contable = '<option value="' . $plan_contable['CodCuenta'] . '" ' . $plan_contable['Disabled'] . '>' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';
                }

                $moneda = (new Moneda())->getMoneda($banco['CodMoneda'], '', [], '', '')[0];

                $option_moneda = '<option value="' . $moneda['CodMoneda'] . '">' . $moneda['DescMoneda'] . '</option>';

                $cheques = (new Cheque())->getCheque($this->CodEmpresa, $CodBanco, '', [], '', '');

                $script = "
                    var id_cheque = " . (count($cheques) + 1) . ";
                ";

                $script = (new Empresa())->generar_script($script, ['app/mantenience/box_banks/edit.js']);

                return viewApp($this->page, 'app/mantenience/box_banks/edit', [
                    'banco' => $banco,
                    'option_entidad_financiera' => $option_entidad_financiera,
                    'option_plan_contable' => $option_plan_contable,
                    'option_moneda' => $option_moneda,
                    'cheques' => $cheques,
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

            $post['codcuenta'] = !empty($post['codcuenta']) ? $post['codcuenta'] : NULL;
            $post['Propio'] = isset($post['Propio']) ? $post['Propio'] : 0;
            $post['CodMoneda'] = isset($post['CodMoneda']) ? $post['CodMoneda'] : 'MO001';
            $post['PagoDetraccion'] = isset($post['PagoDetraccion']) ? $post['PagoDetraccion'] : 0;

            $existe_codigo = (new Banco())->getBanco($post['CodEmpresa'], $post['Codbanco'], '', '', [], '', '');

            if (count($existe_codigo) == 0) {
                (new Banco())->agregar($post);

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

                        (new Cheque())->agregar($data);
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

            (new Banco())->actualizar($post['CodEmpresa'], $post['Codbanco'], $post);

            if (isset($post['DescCheque'])) {
                if (isset($post['idCheque'])) {
                    $ids = implode(',', $post['idCheque']);

                    (new Cheque())->eliminar($post['CodEmpresa'], $post['Codbanco'], 'idCheque NOT IN (' . $ids . ')');
                } else {
                    (new Cheque())->eliminar($post['CodEmpresa'], $post['Codbanco'], '');
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
                        (new Cheque())->actualizar($post['CodEmpresa'], $post['idCheque'][$indice], $data);

                        $codigo_cheque = $post['CodCheque'][$indice];
                    } else {
                        (new Cheque())->agregar($data);
                    }
                }
            } else {
                (new Cheque())->eliminar($post['CodEmpresa'], $post['Codbanco'], '');
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

            (new Banco())->eliminar($this->CodEmpresa, $CodBanco, $Periodo);

            (new Cheque())->eliminar($this->CodEmpresa, $CodBanco, '');

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

            $result = (new Banco())->getBanco(
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
            $result = (new Banco())->getBanco(
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

                $caja_banco = (new Banco())->getBanco($this->CodEmpresa, '', '', 'Codbanco AS id, CONCAT(Codbanco, " - ", abreviatura) AS text', [], 'abreviatura LIKE "%' . $search . '%"', '');
            } else {
                $caja_banco = (new Banco())->getBanco($this->CodEmpresa, '', '', 'Codbanco AS id, CONCAT(Codbanco, " - ", abreviatura) AS text', [], '', '');
            }

            echo json_encode($caja_banco);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
