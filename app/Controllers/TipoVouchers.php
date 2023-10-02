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
    protected $empresa;
    protected $CodEmpresa;

    protected $db;

    protected $tipoVoucherCabModel;
    protected $tipoVoucherDetModel;
    protected $anexoModel;
    protected $centroCostoModel;
    protected $activoFijoModel;
    protected $planContableModel;
    protected $monedaModel;
    protected $socioNegocioModel;

    public function __construct()
    {
        $this->page = 'Tipos de Vouchers';
        $this->empresa = new Empresa;
        $this->CodEmpresa = $this->empresa->getCodEmpresa();

        $this->db = \Config\Database::connect();

        $this->tipoVoucherCabModel = new TipoVoucherCab();
        $this->tipoVoucherDetModel = new TipoVoucherDet();
        $this->anexoModel = new Anexo();
        $this->centroCostoModel = new CentroCosto();
        $this->activoFijoModel = new ActivoFijo();
        $this->planContableModel = new PlanContable();
        $this->monedaModel = new Moneda();
        $this->socioNegocioModel = new SocioNegocio();
    }

    public function index()
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $tipoVoucherCab = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', '', '');

                $this->empresa = new Empresa();

                $script = $this->empresa->generar_script('', ['app/mantenience/types_of_vouchers/index.js']);

                return viewApp($this->page, 'app/mantenience/types_of_vouchers/index', [
                    'tipoVoucherCab' => $tipoVoucherCab,
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
                $tipos = array('Diario', 'Ventas Contado', 'Ventas Crédito', 'Compras Contado', 'Compras Crédito', 'Cobro Cliente', 'Pago Proveedor', 'Honorario Contado', 'Honorario Crédito', 'Sistema');

                $options_tipos = '';

                foreach ($tipos as $indice => $valor) {
                    $options_tipos .= '<option value="' . $indice . '">' . $valor . '</option>';
                }

                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $tipo_1 = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', 'CodTV, DescVoucher', 'Tipo = 5');

                $options_tipo_1 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipo_1 as $indice => $valor) {
                    $options_tipo_1 .= '<option value="' . $valor['CodTV'] . '">' . '(' . $valor['CodTV'] . ') ' . $valor['DescVoucher'] . '</option>';
                }

                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $tipo_3 = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', 'CodTV, DescVoucher', 'Tipo = 6');

                $options_tipo_3 = '<option value="" disabled selected>Seleccione</option>';

                foreach ($tipo_3 as $indice => $valor) {
                    $options_tipo_3 .= '<option value="' . $valor['CodTV'] . '">' . '(' . $valor['CodTV'] . ') ' . $valor['DescVoucher'] . '</option>';
                }

                $options_tipo_7 = $options_tipo_3;

                $options_CodEFE = '<option background-readonly h5 text-black" value="" disabled>Operación</option>';

                $this->anexoModel = new Anexo();

                $operaciones = $this->anexoModel->getAnexo($this->CodEmpresa, '', '', '', 'IdAnexo, DescAnexo', 'CodInterno >= 101 AND CodInterno <= 109', '');

                foreach ($operaciones as $indice => $valor) {
                    $options_CodEFE .= '<option value="' . $valor['IdAnexo'] . '">' . $valor['DescAnexo'] . '</option>';
                }

                $options_CodEFE .= '<option background-readonly h5 text-black" value="" disabled>Financiamiento</option>';

                $this->anexoModel = new Anexo();

                $financiamientos = $this->anexoModel->getAnexo($this->CodEmpresa, '', '', '', 'IdAnexo, DescAnexo', 'CodInterno >= 201 AND CodInterno <= 206', '');

                foreach ($financiamientos as $indice => $valor) {
                    $options_CodEFE .= '<option value="' . $valor['IdAnexo'] . '">' . $valor['DescAnexo'] . '</option>';
                }

                $options_CodEFE .= '<option background-readonly h5 text-black" value="" disabled>Inversión</option>';

                $this->anexoModel = new Anexo();

                $inversiones = $this->anexoModel->getAnexo($this->CodEmpresa, '', '', '', 'IdAnexo, DescAnexo', 'CodInterno >= 301 AND CodInterno <= 308', '');

                foreach ($inversiones as $indice => $valor) {
                    $options_CodEFE .= '<option value="' . $valor['IdAnexo'] . '">' . $valor['DescAnexo'] . '</option>';
                }

                $options_debe_haber = $this->options_debe_haber('')['options'];

                $options_centro_costo = $this->options_centro_costo('', true)['options'];

                $options_activo_fijo = $this->options_activo_fijo('', true)['options'];

                $options_socio_negocio = $this->options_socio_negocio('', true)['options'];

                $options_parametro = $this->options_parametro('')['options'];

                $options_moneda = $this->options_moneda('', 'DescMoneda')['options'];

                $this->empresa = new Empresa();

                $script = "
                    var options_tipo_1 = '" . $options_tipo_1 . "';
                    var options_tipo_3 = '" . $options_tipo_3 . "';
                    var options_tipo_7 = '" . $options_tipo_7 . "';
                    var options_debe_haber = '" . $options_debe_haber . "';
                    var options_parametro = '" . $options_parametro . "';
                    var options_moneda = '" . $options_moneda . "';
                    var options_centro_costo = '" . $options_centro_costo . "';
                    var options_activo_fijo = '" . $options_activo_fijo . "';
                    var options_socio_negocio = '" . $options_socio_negocio . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/types_of_vouchers/create.js']);

                return viewApp($this->page, 'app/mantenience/types_of_vouchers/create', [
                    'options_tipos' => $options_tipos,
                    'options_CodEFE' => $options_CodEFE,
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

    public function edit($CodTV)
    {
        try {
            if ($this->empresa->verificar_inicio_sesion()) {
                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $tipoVoucherCab = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, $CodTV, '', '')[0];

                $this->tipoVoucherDetModel = new TipoVoucherDet();

                $tipoVoucherDet = $this->tipoVoucherDetModel->getTipoVoucherDet(
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

                    $tr .= '
                        <tr id="tr_tipo_vouchers' . $valor['NumItem'] . '" class="clase_tipo_vouchers">
                            <td>
                                <input type="text" name="NumItem[]" class="NumItem form-control form-control-sm" value="' . $valor['NumItem'] . '" readonly />
                            </td>
                            <td>
                                <select name="CodCuenta[]" class="CodCuenta form-control form-control-sm" id="CodCuenta' . $valor['NumItem'] . '" onchange="cambiar_cuenta(' . $valor['NumItem'] . ')">
                                    ' . $this->option_plan_contable($valor['CodCuenta'], true)['options'] . '
                                </select>
                            </td>
                            <td>
                                <select name="Debe_Haber[]" class="Debe_Haber form-control form-control-sm" id="Debe_Haber' . $valor['NumItem'] . '">
                                    ' . $this->options_debe_haber($valor['Debe_Haber'])['options'] . '
                                </select>
                            </td>
                            <td class="tdTipo1 ' . $display_tdTipo1 . '">
                                <select name="Parametro[]" class="Parametro form-control form-control-sm" id="Parametro' . $valor['NumItem'] . '">
                                    ' . $this->options_parametro($valor['Parametro'])['options'] . '
                                </select>
                            </td>
                            <td class="tdTipo1 tdTipo9 tdTipoBackground1 ' . $tdTipoBackground9 . ' ' . $display_tdTipo1 . ' ' . $display_tdTipo9 . '" id="td_CodMoneda_' . $valor['NumItem'] . '">
                                <select name="CodMoneda[]" class="CodMoneda form-control form-control-sm ' . $display_CodMoneda . '" id="CodMoneda' . $valor['NumItem'] . '">
                                    ' . $this->options_moneda($valor['CodMoneda'], 'DescMoneda')['options'] . '
                                </select>
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <input type="text" name="MontoD[]" class="MontoD form-control form-control-sm ' . $display_MontoD . '" oninput="esMayorCero(this)" onkeypress="esNumero(event)" value="' . $valor['MontoD'] . '" />
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <select name="CodCcosto[]" class="CodCcosto form-control form-control-sm ' . $display_CodCcosto . '" id="CodCcosto' . $valor['NumItem'] . '">
                                    ' . $this->options_centro_costo($valor['CodCcosto'], true)['options'] . '
                                </select>
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <select name="IdActivo[]" class="IdActivo form-control form-control-sm ' . $display_IdActivo . '" id="IdActivo' . $valor['NumItem'] . '">
                                    ' . $this->options_activo_fijo($valor['IdActivo'], true)['options'] . '
                                </select>
                            </td>
                            <td class="tdTipo0 tdTipoBackground1 ' . $tdTipoBackground1 . ' ' . $display_tdTipo0 . '">
                                <select name="IdSocioN[]" class="IdSocioN form-control form-control-sm ' . $display_IdSocioN . '" id="IdSocioN' . $valor['NumItem'] . '">
                                    ' . $this->options_socio_negocio($valor['IdSocioN'], true)['options'] . '
                                </select>
                            </td>
                            <td align="center">
                                <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminarFilaTipoVouchers(' . $valor['NumItem'] . ')">Eliminar</button>
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

                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $tipo_1 = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', 'CodTV, DescVoucher', 'Tipo = 5');

                $options_tipo_1 = '';

                foreach ($tipo_1 as $indice => $valor) {
                    $options_tipo_1 .= '<option value="' . $valor['CodTV'] . '">' . '(' . $valor['CodTV'] . ') ' . $valor['DescVoucher'] . '</option>';
                }

                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $tipo_3 = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', 'CodTV, DescVoucher', 'Tipo = 6');

                $options_tipo_3 = '';

                foreach ($tipo_3 as $indice => $valor) {
                    $options_tipo_3 .= '<option value="' . $valor['CodTV'] . '">' . '(' . $valor['CodTV'] . ') ' . $valor['DescVoucher'] . '</option>';
                }

                $options_tipo_7 = $options_tipo_3;

                $options_CodEFE = '<option class="background-readonly h5 text-black" value="" disabled>Operación</option>';

                $this->anexoModel = new Anexo();

                $operaciones = $this->anexoModel->getAnexo($this->CodEmpresa, '', '', '', 'IdAnexo, DescAnexo', 'CodInterno >= 101 AND CodInterno <= 109', '');

                foreach ($operaciones as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $tipoVoucherCab['CodEFE']) $selected = 'selected';

                    $options_CodEFE .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $options_CodEFE .= '<option class="background-readonly h5 text-black" value="" disabled>Financiamiento</option>';

                $this->anexoModel = new Anexo();

                $financiamientos = $this->anexoModel->getAnexo($this->CodEmpresa, '', '', '', 'IdAnexo, DescAnexo', 'CodInterno >= 201 AND CodInterno <= 206', '');

                foreach ($financiamientos as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $tipoVoucherCab['CodEFE']) $selected = 'selected';

                    $options_CodEFE .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $options_CodEFE .= '<option class="background-readonly h5 text-black" value="" disabled>Inversión</option>';

                $this->anexoModel = new Anexo();

                $inversiones = $this->anexoModel->getAnexo($this->CodEmpresa, '', '', '', 'IdAnexo, DescAnexo', 'CodInterno >= 301 AND CodInterno <= 308', '');

                foreach ($inversiones as $indice => $valor) {
                    $selected = '';

                    if ($valor['IdAnexo'] == $tipoVoucherCab['CodEFE']) $selected = 'selected';

                    $options_CodEFE .= '<option value="' . $valor['IdAnexo'] . '" ' . $selected . '>' . $valor['DescAnexo'] . '</option>';
                }

                $options_debe_haber = $this->options_debe_haber('')['options'];

                $options_centro_costo = $this->options_centro_costo('', true)['options'];

                $options_activo_fijo = $this->options_activo_fijo('', true)['options'];

                $options_socio_negocio = $this->options_socio_negocio('', true)['options'];

                $options_parametro = $this->options_parametro('')['options'];

                $options_moneda = $this->options_moneda('', 'DescMoneda')['options'];

                $this->empresa = new Empresa();

                $script = "
                    var id_tipo_vouchers = " . (count($tipoVoucherDet) + 1) . ";
                    // $('#CodTVcaja').val('" . $tipoVoucherCab['CodTVcaja'] . "');
                    var options_tipo_1 = '" . $options_tipo_1 . "';
                    var options_tipo_3 = '" . $options_tipo_3 . "';
                    var options_tipo_7 = '" . $options_tipo_7 . "';
                    var options_debe_haber = '" . $options_debe_haber . "';
                    var options_parametro = '" . $options_parametro . "';
                    var options_moneda = '" . $options_moneda . "';
                    var options_centro_costo = '" . $options_centro_costo . "';
                    var options_activo_fijo = '" . $options_activo_fijo . "';
                    var options_socio_negocio = '" . $options_socio_negocio . "';
                    var tipoVoucherCab_CodTV = '" . $tipoVoucherCab['CodTV'] . "';
                    var tipoVoucherCab_DescVoucher = '" . $tipoVoucherCab['DescVoucher'] . "';
                ";

                $script = $this->empresa->generar_script($script, ['app/mantenience/types_of_vouchers/edit.js']);

                return viewApp($this->page, 'app/mantenience/types_of_vouchers/edit', [
                    'tipoVoucherCab' => $tipoVoucherCab,
                    'tipoVoucherDet' => $tipoVoucherDet,
                    'th' => $th,
                    'tr' => $tr,
                    'options_tipos' => $options_tipos,
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

            $post['CodTV'] = strtoupper(trim($post['CodTV']));
            $post['DescVoucher'] = strtoupper(trim($post['DescVoucher']));

            $this->db->disableForeignKeyChecks();

            $this->db->transBegin();

            $this->tipoVoucherCabModel = new TipoVoucherCab();

            $this->tipoVoucherCabModel->agregar($post);

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
                        'Parametro' => $post['Parametro'][$indice],
                        'CodMoneda' => empty($post['CodMoneda'][$indice]) ? 'MO001' : $post['CodMoneda'][$indice],
                        'MontoD' => $post['MontoD'][$indice],
                        'CodCcosto' => $post['CodCcosto'][$indice] ?? null,
                        'IdActivo' => $post['IdActivo'][$indice] ?? null,
                        'IdSocioN' => $post['IdSocioN'][$indice] ?? null,
                    ];

                    $this->tipoVoucherDetModel = new TipoVoucherDet();

                    $this->tipoVoucherDetModel->agregar($data);
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

            $this->tipoVoucherCabModel = new TipoVoucherCab();

            $this->tipoVoucherCabModel->actualizar($this->CodEmpresa, $CodTV, $post);

            $NumItem = $post['NumItem'];
            $CodCuenta = $post['CodCuenta'];
            $Debe_Haber = $post['Debe_Haber'];

            if (count($CodCuenta) > 0 && count($Debe_Haber) > 0) {
                $this->tipoVoucherDetModel = new TipoVoucherDet();

                $this->tipoVoucherDetModel->eliminar($post['CodEmpresa'], $CodTV, $post['Periodo']);

                foreach ($NumItem as $indice => $valor) {
                    $data = [
                        'CodTV' => $post['CodTV'],
                        'NumItem' => $valor,
                        'CodEmpresa' => $post['CodEmpresa'],
                        'Periodo' => $post['Periodo'],
                        'CodCuenta' => $post['CodCuenta'][$indice],
                        'Debe_Haber' => $post['Debe_Haber'][$indice],
                        'Parametro' => $post['Parametro'][$indice],
                        'CodMoneda' => empty($post['CodMoneda'][$indice]) ? 'MO001' : $post['CodMoneda'][$indice],
                        'MontoD' => $post['MontoD'][$indice],
                        'CodCcosto' => $post['CodCcosto'][$indice] ?? null,
                        'IdActivo' => $post['IdActivo'][$indice] ?? null,
                        'IdSocioN' => $post['IdSocioN'][$indice] ?? null,
                    ];

                    $this->tipoVoucherDetModel = new TipoVoucherDet();

                    $this->tipoVoucherDetModel->agregar($data);
                }
            } else {
                $this->tipoVoucherDetModel = new TipoVoucherDet();

                $this->tipoVoucherDetModel->eliminar($post['CodEmpresa'], $post['CodTV'], $post['Periodo']);
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

            $this->tipoVoucherCabModel = new TipoVoucherCab();

            $this->tipoVoucherCabModel->eliminar($this->CodEmpresa, $CodTV);

            $this->tipoVoucherDetModel = new TipoVoucherDet();

            $this->tipoVoucherDetModel->eliminar($this->CodEmpresa, $CodTV, '');

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

            $this->tipoVoucherCabModel = new TipoVoucherCab();

            $result = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', '', '');

            $index = 0;

            foreach ($result as $indice => $valor) {
                $values = array(
                    $valor['CodTV'],
                    $valor['DescVoucher'],
                    $valor['GlosaVoucher']
                );

                $excel->setValues($values);

                $excel->body($index + 2, 'valor');

                $this->tipoVoucherDetModel = new TipoVoucherDeT();

                $result_2 = $this->tipoVoucherDetModel->getTipoVoucherDet(
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
            $this->tipoVoucherCabModel = new TipoVoucherCab();

            $result = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', '', '');

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

                $this->tipoVoucherDetModel = new TipoVoucherDet();

                $result_2 = $this->tipoVoucherDetModel->getTipoVoucherDet(
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

            $this->tipoVoucherDetModel = new TipoVoucherDet();

            $result = $this->tipoVoucherDetModel->getTipoVoucherDet(
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
                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $existe_codigo = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, $CodTV, '', '');

                if (count($existe_codigo) > 0) {
                    echo json_encode(['codigo' => $CodTV, 'tipo' => 'codigo', 'estado' => true]);
                } else {
                    $this->tipoVoucherCabModel = new TipoVoucherCab();

                    $existe_codigo = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', '', 'DescVoucher = "' . $DescVoucher . '"');

                    if (count($existe_codigo) > 0) {
                        echo json_encode(['codigo' => $DescVoucher, 'tipo' => 'descripcion', 'estado' => true]);
                    } else {
                        echo json_encode(['codigo' => '', 'tipo' => '', 'estado' => false]);
                    }
                }
            } else if ($tipo == 'editar') {
                $NotCodTV = strtoupper(trim(strval($this->request->getPost('NotCodTV'))));
                $NotDescVoucher = strtoupper(trim(strval($this->request->getPost('NotDescVoucher'))));

                $this->tipoVoucherCabModel = new TipoVoucherCab();

                $existe_codigo = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, $CodTV, '', 'CodTV != "' . $NotCodTV . '"');

                if (count($existe_codigo) > 0) {
                    echo json_encode(['codigo' => $CodTV, 'tipo' => 'codigo', 'estado' => true]);
                } else {
                    $this->tipoVoucherCabModel = new TipoVoucherCab();

                    $existe_codigo = $this->tipoVoucherCabModel->getTipoVoucherCab($this->CodEmpresa, '', '', 'DescVoucher = "' . $DescVoucher . '" AND DescVoucher != "' . $NotDescVoucher . '"');

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

    public function option_plan_contable($CodCuenta, $verDescCuenta)
    {
        try {
            $this->planContableModel =  new PlanContable();

            $planes_contable = $this->planContableModel->getPlanContable($this->CodEmpresa, date('Y'), $CodCuenta, 'CodCuenta, DescCuenta, IF(Child = 0, "disabled", "") AS Disabled, RelacionCuenta', '', '');

            $options_plan_contable = '<option value="" disabled selected>Seleccione</option>';

            $DescCuenta = '';

            foreach ($planes_contable as $indice => $valor) {
                $selected = '';

                if (!empty($CodCuenta) && $valor['CodCuenta'] == $CodCuenta) {
                    $selected = 'selected';
                    $DescCuenta = $valor['DescCuenta'];
                }

                if ($verDescCuenta) {
                    $descripcion = $valor['CodCuenta'] . ' - ' . $valor['DescCuenta'];
                } else {
                    $descripcion = $valor['CodCuenta'];
                }

                $options_plan_contable .= '<option data-relacion-cuenta="' . $valor['RelacionCuenta'] . '" value="' . $valor['CodCuenta'] . '" ' . $valor['Disabled'] . ' ' . $selected . '>' . $descripcion . '</option>';
            }

            return array('options' => $options_plan_contable, 'DescCuenta' => $DescCuenta);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function options_debe_haber($Debe_Haber)
    {
        try {
            $debe_haber = array('D' => 'Debe', 'H' => 'Haber');

            $options_debe_haber = '<option value="" disabled selected>Seleccione</option>';

            foreach ($debe_haber as $indice => $valor) {
                $selected = '';

                if (!empty($Debe_Haber) && $indice == $Debe_Haber) $selected = 'selected';

                $options_debe_haber .= '<option value="' . $indice . '" ' . $selected . '>' . $valor . '</option>';
            }

            return array('options' => $options_debe_haber);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function options_parametro($Parametro)
    {
        try {
            $parametro = array('AFECTO', 'ANTICIPO', 'DESCUENTO', 'IGV', 'PERCEPCION', 'ISC', 'INAFECTO', 'EXONERADO', 'TOTAL', 'OTRO TRIBUTO', 'ICBP');

            $options_parametro = '<option value="" disabled selected>Seleccione</option>';

            foreach ($parametro as $indice => $valor) {
                $selected = '';

                if (!empty($Parametro) && $valor == $Parametro) $selected = 'selected';

                $options_parametro .= '<option value="' . $valor . '" ' . $selected . '>' . $valor . '</option>';
            }

            return array('options' => $options_parametro);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function options_moneda($CodMoneda, $verColumna)
    {
        try {
            $this->monedaModel = new Moneda();

            $moneda = $this->monedaModel->getMoneda('', '');

            $options_moneda = '<option value="" disabled selected>Seleccione</option>';

            foreach ($moneda as $indice => $valor) {
                $selected = '';

                if ($valor['CodMoneda'] == $CodMoneda) $selected = 'selected';

                $options_moneda .= '<option value="' . $valor['CodMoneda'] . '" ' . $selected . '>' . $valor["$verColumna"] . '</option>';
            }

            return array('options' => $options_moneda);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function options_centro_costo($CodCcosto, $verDesccCosto)
    {
        try {
            $this->centroCostoModel = new CentroCosto();

            $centro_costo = $this->centroCostoModel->getCentroCosto($this->CodEmpresa, '', '', [], '', '', '', '');

            $options_centro_costo = '<option value="" disabled selected>Seleccione</option>';

            foreach ($centro_costo as $indice => $valor) {
                $selected = '';

                if (!empty($CodCcosto) && $valor['CodcCosto'] == $CodCcosto) $selected = 'selected';

                if ($verDesccCosto) {
                    $descripcion = $valor['CodcCosto'] . ' - ' . $valor['DesccCosto'];
                } else {
                    $descripcion = $valor['DesccCosto'];
                }

                $options_centro_costo .= '<option value="' . $valor['CodcCosto'] . '" ' . $selected . '>' . $descripcion . '</option>';
            }

            return array('options' => $options_centro_costo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function options_activo_fijo($IdActivo, $verDescripcion)
    {
        try {
            $this->activoFijoModel = new ActivoFijo();

            $activo_fijo = $this->activoFijoModel->getActivoFijo($this->CodEmpresa, '', '');

            $options_activo_fijo = '<option value="" disabled selected>Seleccione</option>';

            foreach ($activo_fijo as $indice => $valor) {
                $selected = '';

                if (!empty($IdActivo) && $valor['IdActivo'] == $IdActivo) $selected = 'selected';

                if ($verDescripcion) {
                    $descripcion = $valor['codActivo'] . ' - ' . $valor['descripcion'];
                } else {
                    $descripcion = $valor['descripcion'];
                }

                $options_activo_fijo .= '<option value="' . $valor['IdActivo'] . '" ' . $selected . '>' . $descripcion . '</option>';
            }

            return array('options' => $options_activo_fijo);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function options_socio_negocio($IdSocioN, $verNumeroDocumento)
    {
        try {
            $this->socioNegocioModel = new SocioNegocio();

            $socio_negocio = $this->socioNegocioModel->getSocioNegocio($this->CodEmpresa, '', [], 'IdSocioN, IF(LENGTH(razonsocial) = 0, CONCAT(Nom1, " ", IF(LENGTH(Nom2) = 0, "", CONCAT(Nom2, " ")), ApePat, " ", ApeMat), razonsocial) AS razonsocial, IF(LENGTH(ruc) = 0 OR ruc IS NULL, docidentidad, ruc) AS numeroDocumento', '', '');

            $options_socio_negocio = '<option value="" disabled selected>Seleccione</option>';

            foreach ($socio_negocio as $indice => $valor) {
                $selected = '';

                if (!empty($IdSocioN) && $valor['IdSocioN'] == $IdSocioN) {
                    $selected = 'selected';
                }

                if ($verNumeroDocumento) {
                    $descripcion = $valor['numeroDocumento'] . ' - ' . $valor['razonsocial'];
                } else {
                    $descripcion = $valor['razonsocial'];
                }

                $options_socio_negocio .= '<option value="' . $valor['IdSocioN'] . '" ' . $selected . '>' . $descripcion . '</option>';
            }

            return array('options' => $options_socio_negocio);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function autoCompletado()
    {
        $resultado = (new TipoVoucherCab())->autoCompletado();
        return $this->response->setJSON($resultado);
    }
}
