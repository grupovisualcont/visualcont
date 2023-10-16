<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <section class="content-buttons btn-groups">
        <button type="button" class="btn btn-info font-medium rounded-pill px-4" onclick="return verificarFormulario()">
            <div class="d-flex align-items-center">
                <i class="fa fa-save me-2 fs-4"></i> Grabar
            </div>
        </button>
        <button type="button" class="btn btn-link font-medium rounded-pill px-4">
            <div class="d-flex align-items-center">
                Cancelar
            </div>
        </button>
    </section>
    <section class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="form" class="mt-3" method="POST" action="<?= base_url('app/movements/sales/update') ?>">
                            <input type="hidden" name="IdMov" id="IdMov" value="<?= $movimiento_cab['IdMov'] ?>" />
                            <input type="hidden" name="IdMovRef" value="<?= $IdMovRef ?>" />
                            <input type="hidden" name="IdMovAplica" value="<?= count($movimiento_cab_referencia) > 0 ? $movimiento_cab_referencia[0]['IdMov'] : '' ?>" />
                            <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                            <input type="hidden" name="Banco" id="Banco" value="" />
                            <input type="hidden" name="CodTV" value="<?= $movimiento_cab['CodTV'] ?>" />
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-12 col-12">
                                    <div class="form-group row">
                                        <label for="" class="control-label col-xl-2 col-lg-2 col-12">
                                            Tipo
                                        </label>
                                        <div class="col-xl-10 col-lg-10 col-12">
                                            <select name="CodTV" id="CodTV" class="form-select form-select-sm form-select-vc" onchange="cambiar_condicion_pago()" disabled>
                                                <?= $option_tipo_voucher ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                    <div class="form-group row">
                                        <label for="" class="control-label col-xl-3 col-lg-3 col-12">
                                            Voucher
                                        </label>
                                        <div class="col-xl-9 col-lg-9 col-12">
                                            <input type="text" name="Codmov" id="Codmov" class="form-control form-control-sm form-control-vc" value="<?= $movimiento_cab['Codmov'] ?>" onfocus="cambiar_codigo()" onfocusout="cambiar_codigo()" maxlength="20" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                                    <div class="form-group row">
                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12">
                                            Fecha Reg.
                                        </label>
                                        <div class="col-xl-8 col-lg-8 col-12">
                                            <div class="input-group input-group-sm input-group-vc">
                                                <input type="text" name="FecContable" id="FecContable" class="form-control mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y', strtotime($movimiento_cab['FecContable'])) ?>" onchange="cambiar_fecha_contable()" disabled>
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-8 col-lg-8 col-md-8 col-12">
                                    <div class="form-group row">
                                        <label for="" class="control-label col-xl-2 col-lg-2 col-12">
                                            Glosa
                                        </label>
                                        <div class="col-xl-10 col-lg-10 col-12">
                                            <input type="text" name="Glosa" id="Glosa" class="form-control form-control-sm form-control-vc" value="<?= $movimiento_cab['Glosa'] ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-2 col-12">
                                    <div class="form-group">
                                        <div class="form-check form-check-reverse">
                                            <label class="form-check-label" for="Estado">
                                                Anulado
                                            </label>
                                            <input class="form-check-input" type="checkbox" name="Estado" id="Estado" value="<?= $movimiento_cab['Estado'] ?>" onchange="cambiar_estado()" <?= $movimiento_cab['Estado'] == 0 ? '' : 'checked' ?>>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link disabled" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1-tab-pane" type="button" role="tab" aria-controls="tab1-tab-pane" aria-selected="true">
                                        Datos del comprobante
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2-tab-pane" type="button" role="tab" aria-controls="tab2-tab-pane" aria-selected="false">
                                        Datos del comprobamte Referencia
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane px-2 py-3 fade" id="tab1-tab-pane" role="tabpanel" aria-labelledby="tab1-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-xl-8 col-md-8 col-12">
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-3 col-lg-3 col-12">
                                                    Cliente
                                                </label>
                                                <div class="col-xl-9 col-lg-9 col-12 d-lg-flex">
                                                    <select name="IdSocioN" id="IdSocioN" class="form-select form-select-sm form-select-vc">
                                                        <?= $option_socio_negocio ?>
                                                    </select>
                                                    <span class="input-group-btn">
                                                        <button type="button" tabindex="-1" class="btn btn-sm height-sm btn-info mx-1" data-bs-toggle="modal" data-bs-target="#clienteModal"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-3 col-lg-3 col-12">
                                                    Comprobante
                                                </label>
                                                <div class="col-xl-9 col-lg-9 col-12">
                                                    <select name="CodDocumento" id="CodDocumento" class="form-select form-select-sm form-select-vc" onchange="cambiar_documento()">
                                                        <?= $option_documento ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12">
                                                            Serie
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12">
                                                            <input type="text" name="Serie" id="Serie" class="form-control form-control-sm form-control-vc" value="<?= $movimiento_cab['SerieDoc'] ?>" oninput="verificar_serie()" onfocusout="cambiar_serie()">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12">
                                                            Nro Inicial
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12">
                                                            <input type="text" name="NumeroDoc" id="NumeroDoc" class="form-control form-control-sm form-control-vc" value="<?= $movimiento_cab['NumeroDoc'] ?>" onkeypress="esNumero(event)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12">
                                                            Nro Final
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12">
                                                            <input type="text" name="NumeroDocF" id="NumeroDocF" class="form-control form-control-sm form-control-vc" value="<?= $movimiento_cab['NumeroDocF'] ?>" onkeypress="esNumero(event)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12">
                                                            Cond. Pago
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12">
                                                            <select name="CodCondPago" id="CodCondPago" class="form-select form-select-sm form-select-vc">
                                                                <?= $option_condicion_pago ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12">
                                                            Fecha Emisión
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12">
                                                            <div class="input-group input-group-sm input-group-vc">
                                                                <input type="text" name="FecEmision" id="FecEmision" class="form-control mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y', strtotime($movimiento_cab['FecEmision'])) ?>" onchange="cambiar_tipo_cambio_from_fecEmision()">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12">
                                                            Fecha Vcto
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12">
                                                            <div class="input-group input-group-sm input-group-vc">
                                                                <input type="text" name="FecVcto" id="FecVcto" class="form-control mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y', strtotime($movimiento_cab['FecVcto'])) ?>">
                                                                <span class="input-group-text">
                                                                    <i class="fa fa-calendar"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12">
                                                            Moneda
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12">
                                                            <select name="CodMoneda" id="CodMoneda" class="form-select form-select-sm form-select-vc" onchange="cambiar_moneda_principal()">
                                                                <?= $option_moneda ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12">
                                                            T/C
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12">
                                                            <input type="text" name="ValorTC" id="ValorTC" class="form-control form-control-sm form-control-vc" value="<?= $movimiento_cab['ValorTC'] ?>" onkeypress="esNumero(event)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12">
                                                            Tipo Operación
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12">
                                                            <select name="TipoOperacion" id="TipoOperacion" class="form-select form-select-sm form-select-vc">
                                                                <?= $option_tipo_operacion ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12">
                                                            Tasa Igv
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12">
                                                            <input type="text" id="TasaIGV" class="form-control form-control-sm form-control-vc" value="18" onkeypress="esNumero(event)" onchange="cambiar_igv()">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-4 col-12 bg-vc-100">
                                            <div class="form-group row mt-3">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    Afecto
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Afecto" class="form-control text-end bg-white" value="0" oninput="set_total('Afecto')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelAfecto">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    Inafecto
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Inafecto" class="form-control text-end bg-white" value="0" oninput="set_total('Inafecto')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelInafecto">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    Exonerado
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Exonerado" class="form-control text-end bg-white" value="0" oninput="set_total('Exonerado')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelExonerado">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    Descuento
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Descuento" class="form-control text-end bg-white" oninput="set_total('Descuento')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelDescuento">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    Anticipo
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Anticipo" class="form-control text-end bg-white" oninput="set_total('Anticipo')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelAnticipo">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    ISC
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="ISC" class="form-control text-end bg-white" value="0" oninput="set_total('ISC')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelISC">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white fw-bolder">
                                                    Igv 18%
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Igv" class="form-control text-end bg-white" value="0" oninput="set_total('Igv')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelIgv">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white">
                                                    Total
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Total" class="form-control text-end bg-white" value="0" oninput="set_total('Total')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelTotal">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane px-2 py-3 fade show active" id="tab2-tab-pane" role="tabpanel" aria-labelledby="tab2-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-xl-7 col-lg-7 col-md-7 col-12">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info font-medium px-4 me-1" id="btnReferenciaExistente" disabled>
                                                    Referencia existente
                                                </button>
                                                <button type="button" class="btn btn-info font-medium px-4 me-1" id="btnReferenciaManual" disabled>
                                                    Referencia manual
                                                </button>
                                                <button type="button" class="btn btn-info font-medium px-4" id="btnQuitarReferencia" disabled>
                                                    Quitar Referencia
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-lg-5 col-md-5 col-12">
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-12">
                                                    Total Referencia
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-12">
                                                    <input type="text" name="" id="referencia_Total" class="form-control form-control-sm form-control-vc text-end" value="0.00" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-3">
                                        <table class="table table-sm table-bordered" id="tabla_referencia">
                                            <thead class="table-primary">
                                                <th>Tipo</th>
                                                <th>Comprobante</th>
                                                <th>Serie</th>
                                                <th>Número</th>
                                                <th>F. Emisión</th>
                                                <th>Total S</th>
                                                <th>Total D</th>
                                                <th>T.C</th>
                                                <th>Cuenta</th>
                                                <th>CodMov</th>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($movimiento_det_referencias)) { ?>
                                                    <tr id="tr_vacio_referencia">
                                                        <td align="center" colspan="10">No hay datos para mostrar</td>
                                                    </tr>
                                                <?php } else {
                                                    echo $movimiento_det_referencias;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-xl-8 col-lg-8 col-md-8 col-12">
                                    <div class="row">
                                        <div class="col-xl-8 col-lg-8 col-md-8 col-12">
                                            <div class="form-group row">
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5">
                                                    Forma de Pago
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7">
                                                    <select id="FormaPago" class="form-control form-control-sm form-control-vc" onchange="cambiar_forma_pago()">
                                                        <?= $option_forma_pago ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12">
                                            <div class="form-check">
                                                <label class="form-check-label" for="Detraccion">
                                                    Detracción
                                                </label>
                                                <input class="form-check-input" type="checkbox" name="Detraccion" id="Detraccion" value="<?= $movimiento_cab['Detraccion'] ?>" onchange="cambiar_detraccion()" <?= $movimiento_cab['Detraccion'] == 0 ? '' : 'checked' ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-12 text-end">
                                    <button type="button" class="btn btn-info font-medium px-4 display-none" id="btnAgregar" onclick="return agregar()">
                                        Agregar
                                    </button>
                                    <button type="button" class="btn btn-info font-medium px-4" id="btnAgregarMas" onclick="return agregar_fila()">
                                        <i class="fa fa-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-sm table-bordered" id="tabla_ingreso_ventas">
                                    <thead class="table-primary">
                                        <th>OP</th>
                                        <th>Item</th>
                                        <th>Cuenta</th>
                                        <th>Moneda</th>
                                        <th>Tipo Cambio</th>
                                        <th>Debe Soles</th>
                                        <th>Haber Soles</th>
                                        <th>Debe Dolar</th>
                                        <th>Haber Dolar</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Vencimiento</th>
                                        <th>Razón Social</th>
                                        <th>Documento</th>
                                        <th>Serie</th>
                                        <th>Número</th>
                                        <th>Número Final</th>
                                        <th>Tipo de Operación</th>
                                        <th>Centro de Costo</th>
                                        <th>Condición de Pago</th>
                                        <th>Doc. Retención</th>
                                        <th>Doc. Detracción</th>
                                        <th>Parametro</th>
                                        <th>% Retención</th>
                                        <th>% Detracción</th>
                                        <th>Fecha Detracción</th>
                                        <th>TO. Det</th>
                                        <th>35-Contrato-Proyecto</th>
                                        <th>Periodo a Declarar</th>
                                        <th>Estado a Declarar</th>
                                        <th>Activo Fijo</th>
                                        <th>Eliminar</th>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($movimiento_det)) { ?>
                                            <tr id="tr_vacio_ingreso_ventas">
                                                <td align="center" colspan="31">No hay datos para mostrar</td>
                                            </tr>
                                        <?php } else {
                                            echo $movimiento_det;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row mt-3">
                                <label for="" class="control-label col-xl-5 col-lg-5 col-12 text-danger">
                                    Comprobante (03), va al Reg. Ventas 14.1
                                </label>
                                <div class="col-xl-7 col-lg-7 col-12">
                                    <div class="input-group input-group-sm input-group-vc">
                                        <input type="text" name="TotalSol" class="form-control form-control-sm form-control-vc me-2" id="total_DebeSol" readonly>
                                        <input type="text" class="form-control form-control-sm form-control-vc me-2" id="total_HaberSol" readonly>
                                        <input type="text" name="TotalDol" class="form-control form-control-sm form-control-vc me-2" id="total_DebeDol" readonly>
                                        <input type="text" class="form-control form-control-sm form-control-vc" id="total_HaberDol" readonly>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>

<div class="modal fade" id="clienteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Cliente</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="clienteForm">
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>Tipo Persona</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <select name="CodTipPer" id="CodTipPer" class="form-select form-select-sm form-select-vc" onchange="verificar_tipo_documento_identidad()">
                                        <?= $option_tipo_persona ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>Tipo Documento</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <select name="CodTipoDoc" id="CodTipoDoc" class="form-select form-select-sm form-select-vc" onchange="verificar_tipo_documento_identidad()">
                                        <?= $option_tipo_documento_identidad ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>RUC</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 d-lg-flex">
                                    <input type="text" name="ruc" id="ruc" class="form-control form-control-sm form-control-vc" maxlength="11" oninput="verificar_longitud_documento(this)" onkeypress="esNumero(event)">
                                    <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('ruc')">
                                        <img src="<?= base_url('assets/img/ruc.png') ?>" width="15" height="15" />
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>DNI \ Otros</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 d-lg-flex">
                                    <input type="text" name="docidentidad" id="docidentidad" class="form-control form-control-sm form-control-vc" oninput="verificar_longitud_documento(this)" onkeypress="esNumero(event)">
                                    <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('dni')">
                                        <img src="<?= base_url('assets/img/dni.png') ?>" width="15" height="15" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>A. Paterno</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" name="ApePat" id="ApePat" class="form-control form-control-sm form-control-vc">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>A. Materno</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" name="ApeMat" id="ApeMat" class="form-control form-control-sm form-control-vc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>Nombre 1</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" name="Nom1" id="Nom1" class="form-control form-control-sm form-control-vc">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>Nombre 2</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" name="Nom2" id="Nom2" class="form-control form-control-sm form-control-vc">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <label>Razón Social</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9">
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control form-control-sm form-control-vc" readonly>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <label>Dirección Principal</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9">
                            <input type="text" name="direccion1" id="direccion1" class="form-control form-control-sm form-control-vc">
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <label>Condición</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <select name="IdCondicion" id="IdCondicion" class="form-select form-select-sm form-select-vc">
                                <?= $option_condicion ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="return verificar_formulario_cliente()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bancoModal" tabindex="-1" role="dialog" aria-labelledby="bancoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bancoModalLabel">Seleccionar Cuenta bancaria - Cheque</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formBanco">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <label>Cuenta</label>
                                <select name="CodCuenta" id="CodCuentaBanco" class="form-select form-select-sm form-select-vc" onchange="cambiar_cuenta_contable_banco()">
                                    <?= $option_plan_contable ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                <label>Descripción</label>
                                <input type="text" id="DescCuentaBanco" class="form-control form-control-sm form-control-vc" value="<?= $descripcion_plan_contable ?>" readonly>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label>Cheque</label>
                                <input type="text" name="IdCheque" class="form-control form-control-sm form-control-vc" value="<?= count($movimiento_cab_banco) > 0 ? $movimiento_cab_banco[0]['IdCheque'] : '' ?>" readonly>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label>Número</label>
                                <input type="text" name="NumCheque" id="NumChequeBanco" class="form-control form-control-sm form-control-vc" value="<?= count($movimiento_cab_banco) > 0 ? $movimiento_cab_banco[0]['NumCheque'] : '' ?>" onkeypress="esNumero(event)">
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <label>Tipo de Pago</label>
                                <select name="CodTipoPago" id="CodTipoPagoBanco" class="form-select form-select-sm form-select-vc">
                                    <?= $option_tipo_pago ?>
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label>N° Transacci.</label>
                                <input type="text" name="GlosaDet" id="GlosaDetBanco" class="form-control form-control-sm form-control-vc" value="<?= count($movimiento_cab_banco) > 0 ? $movimiento_cab_banco[0]['GlosaDet'] : '' ?>" onkeypress="esNumero(event)">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="verificarFormularioBanco()">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="documentoModal" tabindex="-1" role="dialog" aria-labelledby="documentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentoModalLabel">Listado de Documentos</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formBanco">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <div class="table-responsive-md table-wrapper">
                                    <table class="table table-sm table-bordered" id="tabla_documentos" cellspacing="0">
                                        <thead>
                                            <th>Documento</th>
                                            <th>Razón Social</th>
                                            <th>Ruc\Otros</th>
                                            <th>Fec. Emisión</th>
                                            <th>M</th>
                                            <th>T/C</th>
                                            <th>Saldo</th>
                                            <th>Cuenta</th>
                                            <th>CodMov</th>
                                            <th>Mes</th>
                                        </thead>
                                        <tbody>
                                            <tr id="tr_vacio_documentos">
                                                <td align="center" colspan="10">No hay datos para mostrar</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="movimientoModal" tabindex="-1" role="dialog" aria-labelledby="movimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header background-header-movimiento">
                <h5 class="modal-title w-100" id="movimientoModalLabel"></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="table-responsive-md table-wrapper">
                                <table class="table table-sm table-bordered" id="tabla_movimiento" cellspacing="0">
                                    <thead>
                                        <th>Item</th>
                                        <th>Cuenta</th>
                                        <th>Descripción</th>
                                        <th>M</th>
                                        <th>T/C</th>
                                        <th>Debe Soles</th>
                                        <th>Haber Soles</th>
                                        <th>Debe Dolar</th>
                                        <th>Haber Dolar</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Vcmto.</th>
                                        <th>Razón Social</th>
                                        <th>Documento</th>
                                        <th>Serie</th>
                                        <th>Número</th>
                                        <th>Número Final</th>
                                        <th>Tipo de Operación</th>
                                        <th>Centro de Costo</th>
                                        <th>Condición de Pago</th>
                                        <th>Doc. Retención</th>
                                        <th>Doc. Detracción</th>
                                        <th>Parametro</th>
                                        <th>% Ret.</th>
                                        <th>% Det.</th>
                                        <th>Fecha Detracción</th>
                                        <th>Tipo de Pago</th>
                                        <th>Glosa</th>
                                        <th>34-Desc. Bienes Servicios</th>
                                        <th>35-Contrato Proyecto</th>
                                        <th>11-T.C. Suten. C.F.</th>
                                        <th>12-Serie C.F.</th>
                                        <th>14-Número C.F.</th>
                                        <th>31-Convenio</th>
                                        <th>32-Exoneración</th>
                                        <th>33-Tipo Renta</th>
                                        <th>34-Modalidad</th>
                                        <th>Periodo a Declarar</th>
                                        <th>Estado a Declarar</th>
                                        <th>Activo Fijo</th>
                                        <th>Operación A.Fijo</th>
                                    </thead>
                                    <tbody>
                                        <tr id="tr_vacio_movimiento">
                                            <td align="center" colspan="40">No hay datos para mostrar</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="total_movimiento"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    var Codmov = '<?= $movimiento_cab['Codmov'] ?>';
    var mes = '<?= date('m') ?>';
    var datos_ruc_CodTipPer = '<?= $datos_ruc['CodTipPer'] ?>';
    var datos_ruc_CodTipoDoc = '<?= $datos_ruc['CodTipoDoc'] ?>';
    var datos_ruc_N_tip = '<?= $datos_ruc['N_tip'] ?>';
    var datos_extranjero_CodTipPer = '<?= $datos_extranjero['CodTipPer'] ?>';
    var datos_extranjero_CodTipoDoc = '<?= $datos_extranjero['CodTipoDoc'] ?>';
    var Referencia = <?= $Referencia ?>;
    var Importado = <?= $Importado ?>;
    var facturas = JSON.parse('<?= json_encode($facturas) ?>');
    var notas_credito = JSON.parse('<?= json_encode($notas_credito) ?>');
</script>

<?= $this->endSection() ?>