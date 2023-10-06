<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/accounting_plan/index') ?>" class="link-titulo">Plan Contable</a> / Editar <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/accounting_plan/update') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $plan_contable['CodEmpresa'] ?>" />
                    <input type="hidden" name="Periodo" value="<?= $plan_contable['Periodo'] ?>" />
                    <input type="hidden" name="CodCuentaPrincipal" value="<?= $plan_contable['CodCuenta'] ?>" />
                    <input type="hidden" name="CuentaAjuste" value="<?= $plan_contable['CuentaAjuste'] ?>" />
                    <input type="hidden" name="Child" value="<?= $plan_contable['Child'] ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Cuenta</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="CodCuenta" id="CodCuenta" class="form-control form-control-sm" oninput="getCuentaPadre()" onkeypress="esNumero(event)" value="<?= $plan_contable['CodCuenta'] ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Cuenta Padre</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="CuentaPadre" id="CuentaPadre" class="form-control form-control-sm" value="<?= $plan_contable['CuentaPadre'] ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                        <label>Descripción</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">
                                        <input type="text" name="DescCuenta" id="DescCuenta" class="form-control form-control-sm" value="<?= $plan_contable['DescCuenta'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Relación</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="RelacionCuenta" id="RelacionCuenta" class="form-control form-control-sm" onchange="mostrarActivoFijo()">
                                            <?= $options_relacion_cuentas ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Tipo Cuenta</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="TipoResultado" id="TipoResultado" class="form-control form-control-sm" onchange="mostrarTipoDebeHaber()">
                                            <?= $options_tipo_cuentas ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 d-none" id="columnaActivoFijo">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Tipo Activo Fijo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="TipoCuenta" class="form-control form-control-sm">
                                            <?= $options_tipo_activo_fijos ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6" id="columnaActivoFijoVacio">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-none"></div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 d-none"></div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-none hidden-in-sm">
                                        <label>&nbsp;</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="checkbox" name="AjusteDC" id="AjusteDC" value="<?= $plan_contable['AjusteDC'] ?>" onchange="mostrarAjusteDC()" <?= $plan_contable['AjusteDC'] == 1 ? 'checked' : '' ?>> <label for="AjusteDC">Ajuste por diferencia de cambio</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 d-none hidden-in-sm">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-none hidden-in-sm">
                                        <label>&nbsp;</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 d-none" id="columnaTcambio_CV">
                                        <select name="Tcambio_CV" class="form-control form-control-sm">
                                            <?= $options_diferencia_cambios ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 border">
                                        <label>Tipo: Debe / Haber</label>
                                        <br>
                                        <div class="d-lg-flex" id="columnaTipoDebeHaber">
                                            <?= $radios_tipos_debe_haber ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <input type="checkbox" name="Amarres" id="Amarres" onchange="mostrarTablaAmarres()" <?= count($amarres) > 0 ? 'checked' : '' ?>> <label for="Amarres">Amarres</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 <?= count($amarres) > 0 ? 'd-block' : 'd-none' ?>" id="columnaAmarres">
                                <div class="row mb-3">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <button type="button" class="btn btn-sm btn-success shadow-sm float-right" onclick="nuevaFilaAmarre()">
                                            Agregar <i class="fas fa-plus-circle text-white"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="table-responsive-md table-wrapper">
                                            <table class="table table-sm table-bordered" id="tabla_amarres" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Items</th>
                                                        <th width="30%">Amarre Debe</th>
                                                        <th width="30%">Amarre Haber</th>
                                                        <th>Porcentaje</th>
                                                        <th>Eliminar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if (count($amarres) == 0) {
                                                    ?>
                                                        <tr id="tr_vacio_amarre">
                                                            <td align="center" colspan="5">No hay datos para mostrar</td>
                                                        </tr>
                                                        <?php
                                                    } else {
                                                        foreach ($amarres as $indice => $valor) {
                                                        ?>
                                                            <tr id="tr_amarre<?= $indice + 1 ?>" class="clase_amarre">
                                                                <td>
                                                                    <input type="text" name="Items[]" class="Items form-control form-control-sm" value="<?= $indice + 1 ?>" readonly />
                                                                </td>
                                                                <td>
                                                                    <select name="CuentaDebe[]" class="CuentaDebe form-control form-control-sm" id="CuentaDebe<?= $indice + 1 ?>">
                                                                        <?= $valor['CuentaDebe'] ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select name="CuentaHaber[]" class="CuentaHaber form-control form-control-sm" id="CuentaHaber<?= $indice + 1 ?>">
                                                                        <?= $valor['CuentaHaber'] ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="Porcentaje[]" class="Porcentaje form-control form-control-sm" id="Porcentaje<?= $indice + 1 ?>" oninput="esMayorCero(this)" onkeypress="esNumero(event)" value="<?= $valor['Porcentaje'] ?>" />
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-danger shadow-sm" onclick="eliminarFilaAmarre('<?= $indice + 1 ?>')">Eliminar</button>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 my-5 <?= count($amarres) == 0 ? 'd-block' : 'd-none' ?>" id="columnaAmarresVacio">&nbsp;</div>
                        </div>
                        <!-- <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                        <label class="font-weight-bold">Códigos Equivalentes (Solo para importación de ventas - civime)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="row mt-1">
                                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                        <div class="table-responsive-md">
                                                            <table class="table table-sm table-bordered" id="tablaCodigos" width="100%" cellspacing="0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Código</th>
                                                                        <th width="65%">Descripción</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" name="CodCuentaEquiv" class="form-control form-control-sm" value="<?= count($equivalente) > 0 ? $equivalente['CodCuentaEquiv'] : '' ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="Descripcion" class="form-control form-control-sm" value="<?= count($equivalente) > 0 ? $equivalente['DescCuenta'] : '' ?>" />
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        <div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                        <label class="font-weight-bold">
                                                            Nota:
                                                            <br>
                                                            * Al crear una nueva cuenta se creara también en todos los periodos siempre que no exista la cuenta en dicho periodo
                                                            <br>
                                                            * La modificación y eliminación de la cuenta es independiente para cada periodo
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>