<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/exchange_rate/index') ?>" class="link-titulo">Tipo de Cambio</a> / Registrar <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/exchange_rate/update') ?>">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                    <input type="hidden" name="Anio" value="<?= $anio ?>" />
                    <input type="hidden" name="Mes" value="<?= $mes ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                        <label>Moneda</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <select class="form-control form-control-sm" disabled>
                                            <?= $options_moneda ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                        <label>Mes</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <select id="Mes" class="form-control form-control-sm" disabled>
                                            <?= $options_meses ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                        <label>Periodo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9 d-lg-flex">
                                        <input type="text" id="Anio" class="form-control form-control-sm" value="<?= $anio ?>" readonly />
                                        <button type="button" class="btn btn-sm btn-secondary mx-2" onclick="api_tipo_cambio()"><i class="fas fa-globe"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5">
                            <?= $datos ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>