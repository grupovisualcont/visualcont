<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card display-sm-grid"><a href="<?= base_url('app/movements/sales/index') ?>" class="link-titulo">Ingreso de Ventas</a> / Importar <button type="button" class="btn btn-primary btn-sm float-end" onclick="verificarFormulario()">Importar</button></span>
            <div class="my-3 d-lg-flex display-sm-grid">
                <button type="button" class="btn background-excel text-white btn-sm my-2" onclick="pegar_celdas()">Pegar Celdas <i class="fas fa-clone mx-1"></i></button>
                <input type="file" id="xml" class="d-none" onchange="traer_xml(event)" accept="text/xml" webkitdirectory />
                <button type="button" class="btn btn-success btn-sm px-3 my-2" onclick="document.getElementById('xml').click()">Traer XML <i class="fas fa-upload mx-1"></i></button>
                <button type="button" class="btn btn-primary btn-sm px-3 my-2" onclick="descargar_estructura()">Estructura <i class="fas fa-file-download mx-1"></i></button>
                <button type="button" class="btn btn-ligth btn-sm border border-dark px-3 my-2" onclick="limpiar()">Limpiar <i class="fas fa-file mx-1"></i></button>
                <button type="button" class="btn btn-dark btn-sm px-3 my-2" onclick="historial()" data-bs-toggle="modal" data-bs-target="#historialModal">Historial <i class="fas fa-history mx-1"></i></button>
            </div>
        </div>
        <div class="card-body">
            <div>
                <div class="container-fluid p-0">
                    <form id="form">
                        <div class="split">
                            <div class="px-3" id="split-0">
                                <div class="table-responsive-md table-wrapper">
                                    <table class="table table-sm table-bordered display-none" id="tabla_importar" cellspacing="0">
                                        <thead class="background-importar text-white">
                                            <th>Reg</th>
                                            <th>CodDoc</th>
                                            <th>SerieDoc</th>
                                            <th>NroDocDel</th>
                                            <th>NroDocAl</th>
                                            <th>Cond_Pago</th>
                                            <th>Ruc_Clie</th>
                                            <th>Fecha</th>
                                            <th>Fec_Vcmto</th>
                                            <th>Moneda</th>
                                            <th>ValorTC</th>
                                            <th>Tipo_Ope</th>
                                            <th>Neto</th>
                                            <th>Isc</th>
                                            <th>Descuento</th>
                                            <th>Igv</th>
                                            <th>Percepcion</th>
                                            <th>Inafecto</th>
                                            <th>Exonerado</th>
                                            <th>Exportacion</th>
                                            <th>Otros_Trib</th>
                                            <th>ICBP</th>
                                            <th>Total</th>
                                            <th>Cue_Cargo</th>
                                            <th>Cue_Abono</th>
                                            <th>Glosa</th>
                                            <th>Fecha_Detra</th>
                                            <th>Cons_Detra</th>
                                            <th>Total_Detra</th>
                                            <th>Cons_Reten</th>
                                            <th>Total_Reten</th>
                                            <th>CodDocRef</th>
                                            <th>SerieDocRef</th>
                                            <th>NumDocRef</th>
                                            <th>CodCCosto</th>
                                            <th>CodCta_Cancel</th>
                                            <th>Tipo_Pago</th>
                                            <th>Num_Tran_Ban</th>
                                            <th>CodTipoCliente</th>
                                            <th>Cod_Nucleo</th>
                                            <th>Cod_Sede</th>
                                            <th>Detraccion</th>
                                        <!-- <th>Validar</th>
                                            <th>EstCP</th> -->
                                        </thead>
                                        <tbody>
                                            <tr id="tr_vacio_importar">
                                                <td align="center" colspan="41">No hay datos para mostrar</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="my-3">
                                    <div class="table-responsive-md table-wrapper">
                                        <table class="table table-sm table-bordered display-none" id="tabla_importar_totales" cellspacing="0">
                                            <thead>
                                                <th class="font-weight-bold text-black">Reg.</th>
                                                <th class="font-weight-bold text-info">Afecto</th>
                                                <th class="font-weight-bold text-info">Inafecto</th>
                                                <th class="font-weight-bold text-info">Exonerado</th>
                                                <th class="font-weight-bold text-danger">ICBP</th>
                                                <th class="font-weight-bold text-danger">Igv</th>
                                                <th class="font-weight-bold text-danger">Otros Trib.</th>
                                                <th class="font-weight-bold text-primary">Total</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="font-weight-bold text-black" id="Reg_Total">0</th>
                                                    <th class="font-weight-bold text-info" id="Afecto_Total">0.00</th>
                                                    <th class="font-weight-bold text-info" id="Inafecto_Total">0.00</th>
                                                    <th class="font-weight-bold text-info" id="Exonerado_Total">0.00</th>
                                                    <th class="font-weight-bold text-danger" id="ICBP_Total">0.00</th>
                                                    <th class="font-weight-bold text-danger" id="Igv_Total">0.00</th>
                                                    <th class="font-weight-bold text-danger" id="Otros_Trib_Total">0.00</th>
                                                    <th class="font-weight-bold text-primary" id="Total_Total">0.00</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="px-3" id="split-1">
                                <div class="mb-3 display-none" id="Opciones">
                                    <div class="row">
                                        <div class="display-flex">
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="opcionRadio" id="opcionRadioCuentas" class="form-check-input" onchange="cambiar_opcion()" checked>  Cuentas
                                                </label>
                                            </div>
                                            <div class="form-check-inline">
                                                <label class="form-check-label">
                                                    <input type="radio" name="opcionRadio" id="opcionRadioObservaciones" class="form-check-input" onchange="cambiar_opcion()">  Observaciones
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="display-none" id="Cuentas">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <label class="font-weight-bold">Cuentas</label>
                                        </div>
                                    </div>
                                    <br>
                                    <?php
                                    foreach ($cuentas as $indice => $valor) {
                                    ?>
                                        <div class="row mt-1">
                                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                <label><?= $valor['label'] ?></label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                <select name="<?= $valor['name'] ?>_Cuenta" id="<?= $valor['name'] ?>_Cuenta" class="Cuentas form-control form-control-sm">
                                                    <?= $valor['options'] ?>
                                                </select>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="row mt-3">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <label>Equivalente de comprobantes para los archivos XLM</label>
                                        </div>
                                    </div>
                                    <div class="row mt-1">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>T.C</th>
                                                        <th>DOC</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    foreach ($documentos as $indice => $valor) {
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <input type="hidden" name="TC[]" id="TC<?= $valor['TC'] ?>" value="<?= $valor['TC'] ?>" />
                                                                <?= $valor['TC'] ?>
                                                            </td>
                                                            <td>
                                                                <select name="Documento[]" id="Documento<?= $valor['TC'] ?>" class="Documentos form-control form-control-sm" onchange="cambiar_documento('<?= $valor['TC'] ?>')">
                                                                    <?= $valor['DOC'] ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="display-none" id="Observaciones"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="historialModal" tabindex="-1" role="dialog" aria-labelledby="historialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historialModalLabel">Listado de Historial</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive-md table-wrapper">
                                <table class="table table-sm table-bordered" id="tabla_historial" cellspacing="0">
                                    <thead>
                                        <th>Fecha \ Hora</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td align="center" colspan="1">No hay datos para mostrar</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-dark" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm btn-danger" id="btnEliminarHistorial">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    var monedas = JSON.parse('<?= $monedas ?>');
    var documentos = JSON.parse('<?= $documentos_venta ?>');
</script>

<?= $this->endSection() ?>