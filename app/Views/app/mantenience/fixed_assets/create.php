<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/fixed_assets/index') ?>" class="link-titulo">Activos Fijos</a> / Nuevo <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active text-link" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Datos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-link" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Adicionales</a>
                    </li>
                </ul>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/fixed_assets/save') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
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
                                                <input type="text" name="codActivo" class="form-control form-control-sm" value="<?= $codigo_maximo ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="codTipoActivo" id="codTipoActivo" class="form-control form-control-sm">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Descripción</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="descripcion" id="descripcion" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Marca</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="marca" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Modelo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="modelo" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Serie</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="serie" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Fecha Adquisición</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="input-group input-group-sm input-group-vc">
                                                    <input type="text" name="fechaAdqui" id="fechaAdqui" class="form-control form-control-sm mydatepicker" placeholder="dd/mm/yyyy">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Fecha de Inicio</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="input-group input-group-sm input-group-vc">
                                                    <input type="text" name="fechaInicio" id="fechaInicio" class="form-control form-control-sm mydatepicker" placeholder="dd/mm/yyyy">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Fecha de Retiro</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="input-group input-group-sm input-group-vc">
                                                    <input type="text" name="fechaRetiro" class="form-control form-control-sm mydatepicker" placeholder="dd/mm/yyyy">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Depreciación</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 d-flex">
                                                <select name="depresiacion" id="depresiacion" class="form-control form-control-sm">

                                                </select>
                                                <span class="ml-2 mt-1">
                                                    %
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Vida Util</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="AnioEstimado" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Cta. de Gasto</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CtaCtableGasto" id="CtaCtableGasto" class="form-control form-control-sm">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Cta. de Depreciación</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CtaCtableDepreciacion" id="CtaCtableDepreciacion" class="form-control form-control-sm">

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
                                                <select name="estado" id="estado" class="form-control form-control-sm">
                                                    <?= $option_estado ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                            <div class="container-fluid my-3">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Centro de Costo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodCcosto" id="CodCcosto" class="form-control form-control-sm">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Ubigeo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="codubigeo" id="codubigeo" class="form-control form-control-sm">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Localidad \ Comunidad \ Centro poblado</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Localidad" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Dirección</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Direccion" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Responsable</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="Responsable" class="form-control form-control-sm" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Situación</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="idSituacion" class="form-control form-control-sm">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Catalogo existente</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="IdCatalogo" id="IdCatalogo" class="form-control form-control-sm">
                                                    <?= $option_catalogo_existente ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Cod. de Existencia</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="CodSunatExi" class="form-control form-control-sm">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Tipo de activo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="IdTipoActivo" id="IdTipoActivo" class="form-control form-control-sm">
                                                    <?= $option_tipo_activo ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Estado de Activo</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="IdEstadoActivo" id="IdEstadoActivo" class="form-control form-control-sm">
                                                    <?= $option_estado_activo ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Metd. depreciación</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="IdMetodo" id="IdMetodo" class="form-control form-control-sm">
                                                    <?= $option_metodo_depreciacion ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <label class="font-weight-bold">Arrendamiento financiero</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Num. de Contrato</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="ArrNumero" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Fecha de Contrato</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <div class="input-group input-group-sm input-group-vc">
                                                    <input type="text" name="ArrFecha" class="form-control form-control-sm mydatepicker" placeholder="dd/mm/yyyy">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Número de Cuotas pactadas</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="ArrCuota" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label>Monto total del Contrato</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <input type="text" name="ArrMonto" class="form-control form-control-sm" onkeypress="esNumero(event)" />
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