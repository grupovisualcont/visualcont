<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/asset_types/index') ?>" class="link-titulo">Tipos Activos Fijos</a> / Nuevo <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/asset_types/save') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Código</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="codTipoActivo" class="form-control form-control-sm" value="<?= $codigo_maximo ?>" readonly />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Descripción</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="descTipoActivo" id="descTipoActivo" class="form-control form-control-sm" />
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