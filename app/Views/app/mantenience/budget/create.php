<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/budget/index') ?>" class="link-titulo">Presupuesto</a> / Nuevo <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/budget/save') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Tipo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="Tipo" id="Tipo" class="form-control form-control-sm" onchange="cambiarPorNivel()">
                                            <?= $options_niveles ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-block Nivel" id="Nivel1">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="CodConceptoPres1" class="form-control form-control-sm" value="<?= $codigo_nivel_1 ?>" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría Principal</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="descConceptoPres1" id="Descripcion1" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Nivel" id="Nivel2">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="CodConceptoPres2" id="Codigo2" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría Principal</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="Niveles1-2" class="Niveles1 form-control form-control-sm" onchange="setCodigo()">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría 2</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="descConceptoPres2" id="Descripcion2" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Nivel" id="Nivel3">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="CodConceptoPres3" id="Codigo3" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría Principal</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="Niveles1-3" class="Niveles1 form-control form-control-sm" onchange="get_options_nivel_2()">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría 2</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="Niveles2-3" class="form-control form-control-sm" onchange="setCodigo()" disabled>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría 3</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="descConceptoPres3" id="Descripcion3" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Nivel" id="Nivel4">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="CodConceptoPres4" id="Codigo4" class="form-control form-control-sm" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría Principal</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="Niveles1-4" class="Niveles1 form-control form-control-sm" onchange="get_options_nivel_2()">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría 2</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="Niveles2-4" class="form-control form-control-sm" onchange="get_options_nivel_3()" disabled>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Categoría 3</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="Niveles3-4" class="form-control form-control-sm" onchange="setCodigo()" disabled>

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Detalle</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="descConceptoPres4" id="Descripcion4" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Cuenta Contable</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select name="CodCuenta" id="CodCuenta" class="form-control form-control-sm">
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="display-none Nivel" id="Nivel5">
                            <div class="row mt-1">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Código</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="CodVoucherPre" class="form-control form-control-sm" value="<?= $codigo_nivel_5 ?>" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>Concepto</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <input type="text" name="DescVoucherPre" id="Descripcion5" class="form-control form-control-sm" />
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