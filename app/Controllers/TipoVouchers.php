<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ActivoFijo;
use App\Models\Anexo;
use App\Models\CentroCosto;
use App\Models\Moneda;
use App\Models\PlanContable;
use App\Models\SocioNegocio;
use App\Models\TipoVoucherCab;
use App\Models\TipoVoucherDet;

class TipoVouchers extends BaseController
{
    protected $page;
    protected $CodEmpresa;

    protected $db;

    public function __construct()
    {
        $this->page = 'Tipos de Vouchers';
        $this->CodEmpresa = (new Empresa())->getCodEmpresa();

        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $tipoVoucherCab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', 0, '', [], '', '');

                $script = (new Empresa())->generar_script('', ['app/mantenience/types_of_vouchers/index.js']);

                return viewApp($this->page, 'app/mantenience/types_of_vouchers/index', [
                    'tipoVoucherCab' => $tipoVoucherCab,
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

    public function create()
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $tipos = array('Diario', 'Ventas Contado', 'Ventas Crédito', 'Compras Contado', 'Compras Crédito', 'Cobro Cliente', 'Pago Proveedor', 'Honorario Contado', 'Honorario Crédito', 'Sistema');

                $options_tipos = '';

                foreach ($tipos as $indice => $valor) {
                    $options_tipos .= '<option value="' . $indice . '">' . $valor . '</option>';
                }

                $script = (new Empresa())->generar_script('', ['app/mantenience/types_of_vouchers/create.js']);

                return viewApp($this->page, 'app/mantenience/types_of_vouchers/create', [
                    'options_tipos' => $options_tipos,
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

    public function edit($CodTV)
    {
        try {
            if ((new Empresa())->verificar_inicio_sesion()) {
                $tipoVoucherCab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, $CodTV, 0, '', [], '', '')[0];

                $tipoVoucherDet = (new TipoVoucherDet())->getTipoVoucherDet(
                    $this->CodEmpresa,
                    '',
                    '',
                    $CodTV,
                    'tipovoucherdet.*, pc.RelacionCuenta',
                    [
                        array('tabla' => 'plan_contable pc', 'on' => 'pc.CodCuenta = tipovoucherdet.CodCuenta AND pc.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    ''
                );

                $th = '';

                switch ($tipoVoucherCab['Tipo']) {
                    case 0:
                        $th .= '
                            <tr>
                                <th>N°</th>
                                <th>Cuenta</th>
                                <th>D / H</th>
                                <th>Monto</th>
                                <th>C. Costo</th>
                                <th>Act. Fijo</th>
                                <th>Razón Social</th>
                                <th>Eliminar</th>
                            </tr>
                        ';

                        $tdTipoBackground1 = '';

                        $display_tdTipo0 = '';
                        $display_tdTipo1 = 'display-none';
                        $display_MontoD = '';
                        $display_CodCcosto = '';
                        $display_IdActivo = '';
                        $display_IdSocioN = '';

                        $display_tdTipo9 = '';

                        break;
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                        $th_Moneda = $tipoVoucherCab['Tipo'] != 9 ? '<th>Moneda</th>' : '';

                        $th .= '
                            <tr>
                                <th>N°</th>
                                <th>Cuenta</th>
                                <th>D / H</th>
                                <th>Parametro</th>
                                ' . $th_Moneda . '
                                <th>Monto</th>
                                <th>C. Costo</th>
                                <th>Act. Fijo</th>
                                <th>Razón Social</th>
                                <th>Eliminar</th>
                            </tr>
                        ';

                        $tdTipoBackground1 = 'background-readonly';
                        $tdTipoBackground9 = 'background-readonly';

                        $display_tdTipo0 = '';
                        $display_tdTipo1 = '';
                        $display_CodMoneda = 'display-none';
                        $display_MontoD = 'display-none';
                        $display_CodCcosto = 'display-none';
                        $display_IdActivo = 'display-none';
                        $display_IdSocioN = 'display-none';

                        if (
                            $tipoVoucherCab['Tipo'] == 1 || $tipoVoucherCab['Tipo'] == 2 ||
                            $tipoVoucherCab['Tipo'] == 5 || $tipoVoucherCab['Tipo'] == 6
                        ) {
                            $tdTipoBackground9 = '';

                            $display_CodMoneda = '';
                        }

                        $display_tdTipo9 = '';

                        if ($tipoVoucherCab['Tipo'] == 9) $display_tdTipo9 = 'display-none';

                        break;
                }

                $tr = '';

                foreach ($tipoVoucherDet as $indice => $valor) {
                    if ($valor['RelacionCuenta'] == 1 || $valor['RelacionCuenta'] == 3) {
                        $tdTipoBackground9 = '';
                        $display_CodMoneda = '';
                    } else {
                        $tdTipoBackground9 = 'background-readonly';
                        $display_CodMoneda = 'display-none';
                    }

                    $plan_contable = (new PlanContable())->getPlanContable($this->CodEmpresa, '', $valor['CodCuenta'], '', [], '', '')[0];

                    $option_plan_contable = '<option value="' . $plan_contable['CodCuenta'] . '" data-relacion-cuenta="' . $plan_contable['RelacionCuenta'] . '">' . $plan_contable['CodCuenta'] . ' - ' . $plan_contable['DescCuenta'] . '</option>';

                    $debe_haber = (new DebeHaber())->getDebeHaber($valor['Debe_Haber'])[0];

                    $option_debe_haber = '<option value="' . $debe_haber['id'] . '">' . $debe_haber['text'] . '</option>';

                    $parametro = (new Parametro())->getParametro($valor['Parametro'] ?? '')[0];

                    $option_parametro = $valor['Parametro'] ? '<option value="' . $parametro['id'] . '">' . $parametro['text'] . '</option>' : '';

                    $moneda = (new Moneda())->getMoneda($valor['CodMoneda'] ?? '', '', [], '', '')[0];

                    $option_moneda = $valor['CodMoneda'] ? '<option value="' . $moneda['CodMoneda'] . '">' . $moneda['DescMoneda'] . '</option>' : '';

                    $centro_costo = (new CentroCosto())->getCentroCosto($this->CodEmpresa, $valor['CodCcosto'] ?? '', 0, '', [], '', '', '')[0];

                    $option_centro_costo = $valor['CodCcosto'] ? '<option value="' . $centro_costo['CodcCosto'] . '">' . $centro_costo['DesccCosto'] . '</option>' : '';

                    $activo_fijo = (new ActivoFijo())->getActivoFijo($this->CodEmpresa, $valor['IdActivo'] ?? 0, '', '', [], '', '')[0];

                    $option_activo_fijo = $valor['IdActivo'] ? '<option value="' . $activo_fijo['IdActivo'] . '">' . $activo_fijo['descripcion'] . '</option>' : '';

                    $socio_negocio = (new SocioNegocio())->getSocioNegocio($this->CodEmpresa, $valor['IdSocioN'] ?? 0, 'IdSocioN , ' . (new SocioNegocio())->getRazonSocial() . ' AS razonsocial', [], '', '')[0];

                    $option_socio_negocio = $valor['IdSocioN'] ? '<option value="' . $socio_negocio['IdSocioN'] . '">' . $socio_negocio['razonsocial'] . '</option>' : '';

                    $tr .= '
                        <tr id="tr_tipo_vouchers' . $valor['NumItem'] . '" class="clase_tipo_vouchers">
                            <td>
                                <input type="text" name="NumItem[]" class="NumItem form-control form-control-sm" value="' . $valor['NumItem'] . '" readonly />
                            </td>
                            <td>
                                <select name="CodCuenta[]" class="CodCuenta form-control form-control-sm" id="CodCuenta' . $valor['NumItem'] . '" onchange="cambiar_cuenta(' . $valor['NumItem'] . ')">
                                    ' . $option_plan_contable . '
                                </select>
                            </td>
                            <td>
                                <select name="Debe_Haber[]" class="Debe_Haber form-control form-control-sm" id="Debe_Haber' . $valor['NumItem'] . '">
                                    ' . $option_debe_haber . '
                                </select>
                            </td>
                            <td class="tdTipo1 ' . $display_tdTipo1 . '">
                                <select name="Parametro[]" class="Parametro form-control form-control-sm" id="Parametro' . $valor['NumItem'] . '">
                                    ' . $option_parametro . '
                                </select>
                            </td>
                            <td class="tdTipo1 tdTipo9 tdTipoBackground1 ' . $tdTipoBackground9 . ' ' . $display_tdTipo1 . ' ' . $display_tdTipo9 . '" id="td_CodMoneda_' . $valor['NumItem'] . '">
                                <select name="CodMoneda[]" class="CodMoneda form-control form-control-sm ' . $display_CodMoneda . '" id="CodMoneda' . $valor['NumItem'] . '">
                                    ' . $option_moneda . '
                                </select>
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <input type="text" name="MontoD[]" class="MontoD form-control form-control-sm ' . $display_MontoD . '" oninput="esMayorCero(this)" onkeypress="esNumero(event)" value="' . $valor['MontoD'] . '" />
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <select name="CodCcosto[]" class="CodCcosto form-control form-control-sm ' . $display_CodCcosto . '" id="CodCcosto' . $valor['NumItem'] . '">
                                    ' . $option_centro_costo . '
                                </select>
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <select name="IdActivo[]" class="IdActivo form-control form-control-sm ' . $display_IdActivo . '" id="IdActivo' . $valor['NumItem'] . '">
                                    ' . $option_activo_fijo . '
                                </select>
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <select name="IdSocioN[]" class="IdSocioN form-control form-control-sm ' . $display_IdSocioN . '" id="IdSocioN' . $valor['NumItem'] . '">
                                    ' . $option_socio_negocio . '
                                </select>
                            </td>
                            <td align="center">
                                <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminar(' . $valor['NumItem'] . ')">Eliminar</button>
                            </td>
                        <tr>
                    ';
                }

                $tipos = array('Diario', 'Ventas Contado', 'Ventas Crédito', 'Compras Contado', 'Compras Crédito', 'Cobro Cliente', 'Pago Proveedor', 'Honorario Contado', 'Honorario Crédito', 'Sistema');

                $options_tipos = '';

                foreach ($tipos as $indice => $valor) {
                    $selected = '';

                    if ($indice == $tipoVoucherCab['Tipo']) $selected = 'selected';

                    $options_tipos .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
                }

                $flujo_efectivo = (new Anexo())->getAnexo($this->CodEmpresa, $tipoVoucherCab['CodEFE'] ?? 0, 0, '', 'IdAnexo, DescAnexo', [], '', '')[0];

                $option_CodEFE = $tipoVoucherCab['CodEFE'] ? '<option value="' . $flujo_efectivo['IdAnexo'] . '">' . $flujo_efectivo['DescAnexo'] . '</option>' : '';

                $CodTVcaja = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, $tipoVoucherCab['CodTVcaja'] ?? '', 0, 'CodTV, CONCAT("(", CodTV, ") ", DescVoucher) AS DescVoucher', [], '', '')[0];

                $option_CodTVcaja = $tipoVoucherCab['CodTVcaja'] ? '<option value="' . $CodTVcaja['CodTV'] . '">' . $CodTVcaja['DescVoucher'] . '</option>' : '';

                $script = "
                    var id_tipo_vouchers = " . (count($tipoVoucherDet) + 1) . ";
                    $('#CodTVcaja').val('" . $tipoVoucherCab['CodTVcaja'] . "');
                    var tipoVoucherCab_CodTV = '" . $tipoVoucherCab['CodTV'] . "';
                    var tipoVoucherCab_DescVoucher = '" . $tipoVoucherCab['DescVoucher'] . "';
                ";

                $script = (new Empresa())->generar_script($script, ['app/mantenience/types_of_vouchers/edit.js']);

                return viewApp($this->page, 'app/mantenience/types_of_vouchers/edit', [
                    'tipoVoucherCab' => $tipoVoucherCab,
                    'tipoVoucherDet' => $tipoVoucherDet,
                    'options_tipos' => $options_tipos,
                    'option_CodEFE' => $option_CodEFE,
                    'option_CodTVcaja' => $option_CodTVcaja,
                    'th' => $th,
                    'tr' => $tr,
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

            $post['CodTV'] = strtoupper(trim($post['CodTV']));
            $post['DescVoucher'] = strtoupper(trim($post['DescVoucher']));

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            (new TipoVoucherCab())->agregar($post);

            $NumItem = $post['NumItem'];
            $CodCuenta = $post['CodCuenta'];
            $Debe_Haber = $post['Debe_Haber'];

            if (count($CodCuenta) > 0 && count($Debe_Haber) > 0) {
                foreach ($NumItem as $indice => $valor) {
                    $data = [
                        'CodTV' => $post['CodTV'],
                        'NumItem' => $valor,
                        'CodEmpresa' => $post['CodEmpresa'],
                        'Periodo' => $post['Periodo'],
                        'CodCuenta' => $post['CodCuenta'][$indice],
                        'Debe_Haber' => $post['Debe_Haber'][$indice],
                        'Parametro' => $post['Parametro'][$indice] ?? null,
                        'CodMoneda' => empty($post['CodMoneda'][$indice]) ? 'MO001' : $post['CodMoneda'][$indice],
                        'MontoD' => $post['MontoD'][$indice],
                        'CodCcosto' => $post['CodCcosto'][$indice] ?? null,
                        'IdActivo' => $post['IdActivo'][$indice] ?? null,
                        'IdSocioN' => $post['IdSocioN'][$indice] ?? null,
                    ];

                    (new TipoVoucherDet())->agregar($data);
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

            return redirect()->to(base_url('app/mantenience/types_of_vouchers/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update()
    {
        try {
            $post = $this->request->getPost();

            $CodTV = strtoupper(trim($post['CodTV_auxiliar']));
            $post['CodTV'] = strtoupper(trim($post['CodTV']));
            $post['DescVoucher'] = strtoupper(trim($post['DescVoucher']));

            unset($post['CodTV_auxiliar']);

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            (new TipoVoucherCab())->actualizar($this->CodEmpresa, $CodTV, $post);

            $NumItem = $post['NumItem'];
            $CodCuenta = $post['CodCuenta'];
            $Debe_Haber = $post['Debe_Haber'];

            if (count($CodCuenta) > 0 && count($Debe_Haber) > 0) {
                (new TipoVoucherDet())->eliminar($post['CodEmpresa'], $CodTV, $post['Periodo']);

                foreach ($NumItem as $indice => $valor) {
                    $data = [
                        'CodTV' => $post['CodTV'],
                        'NumItem' => $valor,
                        'CodEmpresa' => $post['CodEmpresa'],
                        'Periodo' => $post['Periodo'],
                        'CodCuenta' => $post['CodCuenta'][$indice],
                        'Debe_Haber' => $post['Debe_Haber'][$indice],
                        'Parametro' => $post['Parametro'][$indice] ?? null,
                        'CodMoneda' => empty($post['CodMoneda'][$indice]) ? 'MO001' : $post['CodMoneda'][$indice],
                        'MontoD' => $post['MontoD'][$indice],
                        'CodCcosto' => $post['CodCcosto'][$indice] ?? null,
                        'IdActivo' => $post['IdActivo'][$indice] ?? null,
                        'IdSocioN' => $post['IdSocioN'][$indice] ?? null,
                    ];

                    (new TipoVoucherDet())->agregar($data);
                }
            } else {
                (new TipoVoucherDet())->eliminar($post['CodEmpresa'], $post['CodTV'], $post['Periodo']);
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

            return redirect()->to(base_url('app/mantenience/types_of_vouchers/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete($CodTV)
    {
        try {
            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            (new TipoVoucherCab())->eliminar($this->CodEmpresa, $CodTV);

            (new TipoVoucherDet())->eliminar($this->CodEmpresa, $CodTV, '');

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

            return redirect()->to(base_url('app/mantenience/types_of_vouchers/index'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function excel()
    {
        try {
            $excel = new Excel();

            $excel->creacion('Tipo de Vouchers - Reporte');

            $columnas = array('Código', 'Descripción del Voucher', 'Glosa del Voucher', 'Item', 'Cuenta', 'Descripción', 'D / H');

            $excel->setValues($columnas);

            $excel->body(1, 'columnas');

            $result = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', 0, '', [], '', '');

            $index = 0;

            foreach ($result as $indice => $valor) {
                $values = array(
                    $valor['CodTV'],
                    $valor['DescVoucher'],
                    $valor['GlosaVoucher']
                );

                $excel->setValues($values);

                $excel->body($index + 2, 'valor');

                $result_2 = (new TipoVoucherDet())->getTipoVoucherDet(
                    $this->CodEmpresa,
                    date('Y'),
                    '',
                    $valor['CodTV'],
                    'tipovoucherdet.CodCuenta, 
                    tipovoucherdet.NumItem, 
                    tipovoucherdet.Debe_Haber, 
                    p.DescCuenta',
                    [
                        array('tabla' => 'plan_contable p', 'on' => 'p.CodCuenta = tipovoucherdet.CodCuenta AND p.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    'tipovoucherdet.NumItem ASC'
                );

                foreach ($result_2 as $indice_2 => $valor_2) {
                    $index++;

                    $values_2 = array('', '', '', $valor_2['NumItem'], $valor_2['CodCuenta'], $valor_2['DescCuenta'], $valor_2['Debe_Haber']);

                    $excel->setValues($values_2);

                    $excel->body($index + 2, 'valor');
                }

                $index++;
            }

            $excel->footer('tipo_vouchers_reporte.xlsx');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function pdf()
    {
        try {
            $result = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', 0, '', [], '', '');

            $columnas = array('Código', 'Descripción del Voucher', 'Glosa del Voucher', 'Item', 'Cuenta', 'Descripción', 'D / H');

            $tr = '<tr>';

            foreach ($columnas as $indice => $valor) {
                $tr .= '<th>' . $valor . '</th>';
            }

            $tr .= '</tr>';

            foreach ($result as $indice => $valor) {
                $tr .= '
                <tr>
                    <td align="left">' . $valor['CodTV'] . '</td>
                    <td align="left">' . $valor['DescVoucher'] . '</td>
                    <td align="left">' . $valor['GlosaVoucher'] . '</td>
                <tr>
            ';

                $result_2 = (new TipoVoucherDet())->getTipoVoucherDet(
                    $this->CodEmpresa,
                    date('Y'),
                    '',
                    $valor['CodTV'],
                    'tipovoucherdet.CodCuenta, 
                    tipovoucherdet.NumItem, 
                    tipovoucherdet.Debe_Haber, 
                    p.DescCuenta',
                    [
                        array('tabla' => 'plan_contable p', 'on' => 'p.CodCuenta = tipovoucherdet.CodCuenta AND p.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left')
                    ],
                    '',
                    'tipovoucherdet.NumItem ASC'
                );

                foreach ($result_2 as $indice_2 => $valor_2) {
                    $tr .= '
                    <tr>
                        <td colspan="3"></td>
                        <td align="left">' . $valor_2['NumItem'] . '</td>
                        <td align="left">' . $valor_2['CodCuenta'] . '</td>
                        <td align="left">' . $valor_2['DescCuenta'] . '</td>
                        <td align="left">' . $valor_2['Debe_Haber'] . '</td>
                    <tr>
                ';
                }
            }

            $pdf = new PDF();

            $pdf->setFilename('tipo_vouchers_reporte');
            $pdf->creacion('Tipo de Vouchers - Reporte', $tr, '', 'A3', true);
            $pdf->imprimir();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_detalles()
    {
        try {
            $CodTV = $this->request->getPost('CodTV');

            $result = (new TipoVoucherDet())->getTipoVoucherDet(
                $this->CodEmpresa,
                date('Y'),
                '',
                $CodTV,
                '
                    tipovoucherdet.CodCuenta, 
                    tipovoucherdet.NumItem, 
                    tipovoucherdet.Debe_Haber,
                    tipovoucherdet.MontoD, 
                    tipovoucherdet.MontoH,
                    p.DescCuenta, 
                    m.DescMoneda,
                    c.DesccCosto,
                    af.descripcion,
                    so.razonsocial
                ',
                [
                    array('tabla' => 'plan_contable p', 'on' => 'p.CodCuenta = tipovoucherdet.CodCuenta AND p.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'moneda m', 'on' => 'm.CodMoneda = tipovoucherdet.CodMoneda', 'tipo' => 'left'),
                    array('tabla' => 'centrocosto c', 'on' => 'c.CodcCosto = tipovoucherdet.CodCcosto AND c.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'activosfijos af', 'on' => 'af.IdActivo = tipovoucherdet.IdActivo AND af.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left'),
                    array('tabla' => 'socionegocio so', 'on' => 'so.IdSocioN = tipovoucherdet.IdSocioN AND so.CodEmpresa = tipovoucherdet.CodEmpresa', 'tipo' => 'left')
                ],
                '',
                'tipovoucherdet.NumItem ASC'
            );

            $table = '
            <div class="card-header py-3">
                <span class="titulo-header-card">Detalles</span>
            </div>
            <div class="card-body">
                <div class="table-responsive-md mt-4">
                    <table class="table table-sm table-bordered" id="dataTableDetalles" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Cuenta</th>
                                <th>Descripción</th>
                                <th>D/H</th>
                                <th>Moneda</th>
                                <th>Monto</th>
                                <th>C. Costo</th>
                                <th>Act. Fijo</th>
                                <th>Razon Social</th>
                            </tr>
                        </thead>
                        <tbody>
        ';

            foreach ($result as $indice => $valor) {
                $monto = $valor['Debe_Haber'] == 'D' ? $valor['MontoD'] : $valor['MontoD'];
                $monto = $monto == 0 ? '' : number_format($monto, 2, '.', ',');

                $table .=
                    '<tr>
                    <td>' . $valor['NumItem'] . '</td>
                    <td>' . $valor['CodCuenta'] . '</td>
                    <td>' . $valor['DescCuenta'] . '</td>
                    <td>' . $valor['Debe_Haber'] . '</td>
                    <td>' . $valor['DescMoneda'] . '</td>
                    <td>' . $monto . '</td>
                    <td>' . $valor['DesccCosto'] . '</td>
                    <td>' . $valor['descripcion'] . '</td>
                    <td>' . $valor['razonsocial'] . '</td>
                </tr>';
            }

            $table .= '
                        </tbody>
                    </table>
                </div>
            </div>
        ';

            echo $table;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function consulta_codigo()
    {
        try {
            $tipo = $this->request->getPost('tipo');
            $CodTV = strtoupper(trim(strval($this->request->getPost('CodTV'))));
            $DescVoucher = strtoupper(trim(strval($this->request->getPost('DescVoucher'))));

            if ($tipo == 'nuevo') {
                $existe_codigo = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, $CodTV, 0, '', [], '', '');

                if (count($existe_codigo) > 0) {
                    echo json_encode(['codigo' => $CodTV, 'tipo' => 'codigo', 'estado' => true]);
                } else {
                    $existe_codigo = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', 0, '', [], 'DescVoucher = "' . $DescVoucher . '"', '');

                    if (count($existe_codigo) > 0) {
                        echo json_encode(['codigo' => $DescVoucher, 'tipo' => 'descripcion', 'estado' => true]);
                    } else {
                        echo json_encode(['codigo' => '', 'tipo' => '', 'estado' => false]);
                    }
                }
            } else if ($tipo == 'editar') {
                $NotCodTV = strtoupper(trim(strval($this->request->getPost('NotCodTV'))));
                $NotDescVoucher = strtoupper(trim(strval($this->request->getPost('NotDescVoucher'))));

                $existe_codigo = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, $CodTV, 0, '', [], 'CodTV != "' . $NotCodTV . '"', '');

                if (count($existe_codigo) > 0) {
                    echo json_encode(['codigo' => $CodTV, 'tipo' => 'codigo', 'estado' => true]);
                } else {
                    $existe_codigo = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', 0, '', [], 'DescVoucher = "' . $DescVoucher . '" AND DescVoucher != "' . $NotDescVoucher . '"', '');

                    if (count($existe_codigo) > 0) {
                        echo json_encode(['codigo' => $DescVoucher, 'tipo' => 'descripcion', 'estado' => true]);
                    } else {
                        echo json_encode(['codigo' => '', 'tipo' => '', 'estado' => false]);
                    }
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado()
    {
        $resultado = (new TipoVoucherCab())->autoCompletado();
        return $this->response->setJSON($resultado);
    }

    public function autocompletado_()
    {
        try {
            $post = $this->request->getPost();

            if (isset($post['search'])) {
                $search = $post['search'];

                $tipoVoucherCab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', $post['Tipo'], 'CodTV AS id, CONCAT("(", CodTV, ") ", DescVoucher) AS text', [], 'DescVoucher LIKE "%' . $search . '%"', '');
            } else {
                $tipoVoucherCab = (new TipoVoucherCab())->getTipoVoucherCab($this->CodEmpresa, '', $post['Tipo'], 'CodTV AS id, CONCAT("(", CodTV, ") ", DescVoucher) AS text', [], '', '');
            }

            echo json_encode($tipoVoucherCab);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
