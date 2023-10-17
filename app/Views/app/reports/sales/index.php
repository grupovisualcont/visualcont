<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Reporte - Ventas</span>
        </div>
        <div class="card-body">
            <ul class="mb-1 p-0 pb-2">
                <li class="li_reporte_ventas">
                    <a class="dropdown-item item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#registroVentasModal">
                        <i class="fas fa-print"></i> Registro de Ventas
                    </a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="li_reporte_ventas">
                    <a class="dropdown-item item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#registroVentasSunatModal">
                        <i class="fas fa-print"></i> Registro de Ventas Sunat
                    </a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="li_reporte_ventas">
                    <a class="dropdown-item item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#registroVentasSunatA4Modal">
                        <i class="fas fa-print"></i> Registro de Ventas Sunat A4
                    </a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="li_reporte_ventas">
                    <a class="dropdown-item item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#DAOTVentasModal">
                        <i class="fas fa-print"></i> DAOT Ventas
                    </a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="li_reporte_ventas">
                    <a class="dropdown-item item" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#registroVentasFiltrosModal">
                        <i class="fas fa-print"></i> Registro de Ventas - Filtros
                    </a>
                </li>
                <div class="dropdown-divider"></div>
            </ul>
        </div>
    </div>
</div>

<div class="modal fade" id="registroVentasModal" tabindex="-1" role="dialog" aria-labelledby="registroVentasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroVentasModalLabel">Registro de Ventas</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form" method="POST" action="<?= base_url('app/reports/sales/registro_ventas/pdf') ?>" target="_blank">
                    <div class="container-fluid pb-4">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Moneda</legend>
                                    <div class="pl-3">
                                        <?= $options_moneda_1 ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Periodo</legend>
                                    <div class="pl-3">
                                        <?= $options_periodo_1 ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Ordenar</legend>
                                    <div class="pl-3">
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">1.-</label>
                                                <select name="ordenar1" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_1 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">2.-</label>
                                                <select name="ordenar2" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_2 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">3.-</label>
                                                <select name="ordenar3" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_3 ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4"></div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-lg-flex">
                                <label class="mx-2">Mes</label>
                                <select name="mes" class="form-control form-control-sm">
                                    <?= $options_mes ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer display-block">
                <button type="button" class="btn btn-sm btn-success" onclick="submit_excel('form')">Exportar a Excel</button>
                <div class="float-end">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="submit_pdf('form')">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="registroVentasSunatModal" tabindex="-1" role="dialog" aria-labelledby="registroVentasSunatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroVentasSunatModalLabel">Registro de Ventas Sunat</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_sunat" method="POST" action="<?= base_url('app/reports/sales/registro_ventas_sunat/pdf') ?>" target="_blank">
                    <div class="container-fluid pb-4">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="formato" id="formato" onchange="cambiar_checkbox('form_sunat')">
                                    <label class="form-check-label" for="formato">Formato 14.1</label>
                                </div>
                                <div class="mt-2 d-lg-flex">
                                    <span>Serie</span><input type="text" name="serie" class="form-control form-control-sm ml-2" />
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Moneda</legend>
                                    <div class="pl-3">
                                        <?= $options_moneda_2 ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Periodo</legend>
                                    <div class="pl-3">
                                        <?= $options_periodo_2 ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Ordenar</legend>
                                    <div class="pl-3">
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">1.-</label>
                                                <select name="ordenar1" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_1 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">2.-</label>
                                                <select name="ordenar2" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_2 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">3.-</label>
                                                <select name="ordenar3" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_3 ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4"></div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-lg-flex">
                                <label class="mx-2">Mes</label>
                                <select name="mes" class="form-control form-control-sm">
                                    <?= $options_mes ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer display-block">
                <button type="button" class="btn btn-sm btn-success" onclick="submit_excel('form_sunat')">Exportar a Excel</button>
                <div class="float-end">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="submit_pdf('form_sunat')">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="registroVentasSunatA4Modal" tabindex="-1" role="dialog" aria-labelledby="registroVentasSunatA4ModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroVentasSunatA4ModalLabel">Registro de Ventas Sunat A4</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_sunat_A4" method="POST" action="<?= base_url('app/reports/sales/registro_ventas_sunat_A4/pdf') ?>" target="_blank">
                    <div class="container-fluid pb-4">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="mostrar" id="mostrar" value="si">
                                    <label class="form-check-label" for="mostrar">Mostar Glosa y Cuenta (Oficio)</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Moneda</legend>
                                    <div class="pl-3">
                                        <?= $options_moneda_3 ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Periodo</legend>
                                    <div class="pl-3">
                                        <?= $options_periodo_3 ?>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <fieldset class="rounded-3 p-2 h-100">
                                    <legend class="text-center w-auto px-3">Ordenar</legend>
                                    <div class="pl-3">
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">1.-</label>
                                                <select name="ordenar1" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_1 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">2.-</label>
                                                <select name="ordenar2" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_2 ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-1">
                                            <div class="col-12 d-lg-flex">
                                                <label class="font-weight-bold">3.-</label>
                                                <select name="ordenar3" class="form-control form-control-sm mx-2">
                                                    <?= $options_ordernar_3 ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4"></div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 d-lg-flex">
                                <label class="mx-2">Mes</label>
                                <select name="mes" class="form-control form-control-sm">
                                    <?= $options_mes ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer display-block">
                <button type="button" class="btn btn-sm btn-success" onclick="submit_excel('form_sunat_A4')">Exportar a Excel</button>
                <div class="float-end">
                    <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Salir</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="submit_pdf('form_sunat_A4')">Imprimir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>