<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/types_of_vouchers/index') ?>" class="link-titulo">Tipo de Vouchers</a> / Editar <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/types_of_vouchers/update') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $tipoVoucherCab['CodEmpresa'] ?>" />
                    <input type="hidden" name="CodTV_auxiliar" value="<?= $tipoVoucherCab['CodTV'] ?>" />
                    <input type="hidden" name="Periodo" value="<?= $tipoVoucherDet[0]['Periodo'] ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Código</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="CodTV" id="CodTV" class="form-control form-control-sm" value="<?= $tipoVoucherCab['CodTV'] ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Descripción</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="DescVoucher" id="DescVoucher" class="form-control form-control-sm" value="<?= $tipoVoucherCab['DescVoucher'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Glosa</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <textarea name="GlosaVoucher" id="GlosaVoucher" class="form-control" rows="2"><?= $tipoVoucherCab['GlosaVoucher'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Nro. Orden</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="Norden" class="form-control form-control-sm" value="<?= $tipoVoucherCab['Norden'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Tipo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="Tipo" id="Tipo" class="form-control form-control-sm" onchange="cambiarTabla()" <?= $tipoVoucherCab['Tipo'] == 9 ? 'disabled' : '' ?>>
                                            <?= $options_tipos ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php
                            if ($tipoVoucherCab['Tipo'] == 9) {
                            ?>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 display-none">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <label class="font-weight-bold">Es un voucher predeterminado del sistema</label>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                        <!-- <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Flujo Efectivo</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <select name="CodEFE" id="CodEFE" class="form-control form-control-sm">
                                                        <?= isset($options_CodEFE) && !empty($options_CodEFE) ? $options_CodEFE : '' ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>TV. Caja</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <select name="CodTVcaja" class="form-control form-control-sm" id="CodTVcaja" disabled></select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="row mb-3">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <button type="button" class="btn btn-sm btn-secondary shadow-sm float-right" id="btnNuevaFilaTipoVouchers" onclick="nuevaFilaTipoVouchers()">
                                            Agregar <i class="fa fa-plus-circle text-white ml-2"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="table-responsive-md">
                                            <table class="table table-sm table-bordered table-layout" id="tabla_tipo_vouchers" width="100%" cellspacing="0">
                                                <thead>
                                                    <?= $th ?>
                                                </thead>
                                                <tbody>
                                                    <?= $tr ?>
                                                </tbody>
                                            </table>
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