<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/some/index') ?>" class="link-titulo">Varios</a> / Nuevo <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/some/save') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Tipo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="Tipo" id="Tipo" class="form-control form-control-sm" onchange="cambiarPorTipo()">
                                            <?= $options_tipos ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-block Tipo" id="Tipo1">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Descripción</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="DescAnexo1" id="DescAnexo1" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Cuenta Contable</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="CodInterno1" class="CodInterno form-control form-control-sm">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Estado</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="Estado1" class="form-control form-control-sm">
                                                <?= $options_estado ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Tipo" id="Tipo2">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Descripción</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="DescAnexo2" id="DescAnexo2" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Cuenta Contable</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="CodInterno2" class="CodInterno form-control form-control-sm">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Estado</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="Estado2" class="form-control form-control-sm">
                                                <?= $options_estado ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Tipo" id="Tipo3">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Descripción</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="DescAnexo3" id="DescAnexo3" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Cod. Interno</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="CodInterno3" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Estado</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="Estado3" class="form-control form-control-sm">
                                                <?= $options_estado ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Tipo" id="Tipo4">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Descripción</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="DescAnexo4" id="DescAnexo4" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Estado</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="Estado4" class="form-control form-control-sm">
                                                <?= $options_estado ?>
                                            </select>
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