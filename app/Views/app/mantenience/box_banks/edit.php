<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/box_banks/index') ?>" class="link-titulo">Caja - Bancos</a> / Editar <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-link" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Datos Banco</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Talonario de Cheques</a>
                    </li>
                </ul>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/box_banks/update') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $banco['CodEmpresa'] ?>" />
                    <input type="hidden" name="salDoctacte" value="<?= $banco['salDoctacte'] ?>" />
                    <input type="hidden" name="Periodo" value="<?= $banco['Periodo'] ?>" />
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                            <div class="container-fluid my-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Código</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Codbanco" class="form-control form-control-sm" value="<?= $banco['Codbanco'] ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="float-right">
                                                    <input type="checkbox" id="telecredito" disabled />
                                                    <label class="disabled" for="telecredito">Telecrédito</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Nombre Bco</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodEntidad" id="CodEntidad" class="form-control form-control-sm">
                                                    <?= $option_entidad_financiera ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Abreviatura</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="abreviatura" id="abreviatura" class="form-control form-control-sm" value="<?= $banco['abreviatura'] ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label for="Propio">Propio banco</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="checkbox" name="Propio" id="Propio" value="<?= $banco['Propio'] ?>" onchange="cambiar_estado_checkbox('Propio')" <?= $banco['Propio'] ? 'checked' : '' ?> />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Cta. Contable</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="codcuenta" id="codcuenta" class="form-control form-control-sm" disabled>
                                                    <?= $option_plan_contable ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Moneda</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodMoneda" id="CodMoneda" class="form-control form-control-sm" disabled>
                                                    <?= $option_moneda ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>N° Cta. Cte.</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="ctacte" id="ctacte" class="form-control form-control-sm" value="<?= $banco['ctacte'] ?>" disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label for="PagoDetraccion">Pago de detracción</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="checkbox" name="PagoDetraccion" id="PagoDetraccion" value="<?= $banco['PagoDetraccion'] ?>" onchange="cambiar_estado_checkbox('PagoDetraccion')" <?= $banco['PagoDetraccion'] ? 'checked' : '' ?> />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                            <div class="container-fluid my-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <label class="font-weight-bold">Lista de Cheques</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row mb-3">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <button type="button" class="btn btn-sm btn-success shadow-sm float-right" onclick="agregar()">
                                                    Agregar <i class="fas fa-plus-circle text-white"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="table-responsive-md">
                                                    <table class="table table-sm table-bordered table-layout" id="tabla_cheque" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th>Descripción</th>
                                                                <th>N°. Inicial</th>
                                                                <th>N°. Final</th>
                                                                <th>Numerador</th>
                                                                <th width="10%">Eliminar</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            if (count($cheques) == 0) {
                                                            ?>
                                                                <tr id="tr_vacio_cheque">
                                                                    <td align="center" colspan="5">No hay datos para mostrar</td>
                                                                </tr>
                                                                <?php
                                                            } else {
                                                                foreach ($cheques as $indice => $valor) {
                                                                ?>
                                                                    <tr id="tr_cheque<?= $indice + 1 ?>" class="clase_cheque">
                                                                        <td>
                                                                            <input type="hidden" name="idCheque[]" value="<?= $valor['idCheque'] ?>" />
                                                                            <input type="hidden" name="CodCheque[]" value="<?= $valor['CodCheque'] ?>" />
                                                                            <input type="text" name="DescCheque[]" class="DescCheque form-control form-control-sm" id="DescCheque<?= $indice + 1 ?>" value="<?= $valor['DescCheque'] ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="nroinicial[]" class="nroinicial form-control form-control-sm" id="nroinicial<?= $indice + 1 ?>" oninput="esMayorCero(this)" onkeypress="esNumero(event)" value="<?= $valor['nroinicial'] ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="nrOfinal[]" class="nrOfinal form-control form-control-sm" id="nrOfinal<?= $indice + 1 ?>" oninput="esMayorCero(this)" onkeypress="esNumero(event)" value="<?= $valor['nrOfinal'] ?>" />
                                                                        </td>
                                                                        <td>
                                                                            <input type="text" name="numerador[]" class="numerador form-control form-control-sm" id="numerador<?= $indice + 1 ?>" oninput="esMayorCero(this)" onkeypress="esNumero(event)" value="<?= $valor['numerador'] ?>" />
                                                                        </td>
                                                                        <td align="center">
                                                                            <button type="button" class="Buttons btn btn-sm btn-danger shadow-sm" onclick="eliminar('<?= $indice + 1 ?>')">Eliminar</button>
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
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>