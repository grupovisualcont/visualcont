<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/movements/sales/index') ?>" class="link-titulo">Ingreso de Ventas</a> / Nuevo <button type="button" class="btn btn-primary btn-sm float-end" onclick="verificarFormulario()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/movements/sales/save') ?>">
                    <input type="hidden" name="CodEmpresa" value="<?= $_COOKIE['empresa'] ?>" />
                    <input type="hidden" name="Banco" id="Banco" value="" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Tipo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select name="CodTV" id="CodTV" class="form-control form-control-sm" onchange="cambiar_condicion_pago()" autofocus>
                                            <?= $option_tipo_voucher ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Voucher</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="Codmov" id="Codmov" class="form-control form-control-sm" value="<?= $codigo_voucher_maximo ?>" onfocus="cambiar_codigo()" onfocusout="cambiar_codigo()" maxlength="20" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Fecha Reg.</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="date" name="FecContable" id="FecContable" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="cambiar_fecha_contable()" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                        <label>Glosa</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10">
                                        <input type="text" name="Glosa" id="Glosa" class="form-control form-control-sm" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>&nbsp;</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <div class="float-right">
                                            <label for="Estado">Anulado</label> <input type="checkbox" name="Estado" id="Estado" value="0" onchange="cambiar_estado()" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active text-link" id="tab1-tab" data-bs-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Datos del Comprobante</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-link disabled" id="tab2-tab" data-bs-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Datos Comprob. de Referencia</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                                <div class="row py-2">
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                                <label>Cliente</label>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-10 col-xl-10 d-lg-flex">
                                                <select name="IdSocioN" id="IdSocioN" class="form-control form-control-sm">
                                                    <!-- $options_socio_negocio -->
                                                </select>
                                                <span class="input-group-btn">
                                                    <button type="button" tabindex="-1" class="btn btn-sm height-sm btn-secondary mx-1" data-bs-toggle="modal" data-target="#clienteModal"><i class="fa fa-plus-circle" aria-hidden="true"></i></button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Comprobante</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <select name="CodDocumento" id="CodDocumento" class="form-control form-control-sm" onchange="cambiar_documento()">
                                                            <?= $option_documento ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">

                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Serie</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="text" name="Serie" id="Serie" class="form-control form-control-sm" oninput="verificar_serie()" onfocusout="cambiar_serie()" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Num. Inicial</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="text" name="NumeroDoc" id="NumeroDoc" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Num. Final</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="text" name="NumeroDocF" id="NumeroDocF" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Cond. Pago</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <select name="CodCondPago" id="CodCondPago" class="form-control form-control-sm">
                                                            <?= $option_condicion_pago_credito ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Fec Emisión</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="date" name="FecEmision" id="FecEmision" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" onchange="cambiar_tipo_cambio_from_FecEmision()" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Fec Vcto.</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="date" name="FecVcto" id="FecVcto" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Moneda</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <select name="CodMoneda" id="CodMoneda" class="form-control form-control-sm" onchange="cambiar_moneda_principal()">
                                                            <?= $option_moneda ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>T/C</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="text" name="ValorTC" id="ValorTC" class="form-control form-control-sm" value="<?= $tipo_cambio_venta ?>" onkeypress="esNumero(event)" />
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>T. Opraci.</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <select name="TipoOperacion" id="TipoOperacion" class="form-control form-control-sm">
                                                            <?= $option_tipo_operacion ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label>Tasa IGV</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                                        <input type="text" id="TasaIGV" class="form-control form-control-sm" value="18" onkeypress="esNumero(event)" onchange="cambiar_igv()" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <div class="row background-cuadro-total p-2">
                                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Afecto</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Afecto" class="form-control form-control-sm" value="0" oninput="set_total('Afecto')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelAfecto"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Inafecto</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Inafecto" class="form-control form-control-sm" value="0" oninput="set_total('Inafecto')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelInafecto"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Exonerado</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Exonerado" class="form-control form-control-sm" value="0" oninput="set_total('Exonerado')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelExonerado"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Descuento</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Descuento" class="form-control form-control-sm" oninput="set_total('Descuento')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelDescuento"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Anticipo</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Anticipo" class="form-control form-control-sm" oninput="set_total('Anticipo')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelAnticipo"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">ISC</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="ISC" class="form-control form-control-sm" value="0" oninput="set_total('ISC')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelISC"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Igv 18%</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Igv" class="form-control form-control-sm" value="0" oninput="set_total('Igv')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelIgv"></label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                                        <label class="text-white">Total</label>
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                                        <input type="text" id="Total" class="form-control form-control-sm" value="0" oninput="set_total('Total')" onkeypress="esNumero(event)" />
                                                    </div>
                                                    <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                                        <label class="text-white" id="labelTotal"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                                <div class="mt-3 py-3">
                                    <div class="row py-3 background-referencia">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                                    <button type="button" class="btn btn-sm estilo-referencia" id="btnReferenciaExistente" onclick="consulta_notas_debito_credito()" disabled>Referencia Existente</button>
                                                    <button type="button" class="btn btn-sm estilo-referencia" id="btnReferenciaManual" disabled>Referencia Manual</button>
                                                    <button type="button" class="btn btn-sm estilo-referencia" id="btnQuitarReferencia" disabled>Quitar Referencia</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 p-0">
                                            <div class="table-responsive-md table-wrapper">
                                                <table class="table table-sm table-bordered" id="tabla_referencia" cellspacing="0">
                                                    <thead class="background-referencia text-white">
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
                                                        <tr id="tr_vacio_referencia">
                                                            <td align="center" colspan="10">No hay datos para mostrar</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">

                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 p-0">
                                            <div class="d-lg-flex float-right">
                                                <span class="w-100">Total Referencia</span>
                                                <input type="text" name="" id="referencia_Total" class="form-control form-control-sm text-right estilo-referencia" value="0.00" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 p-2 background-cuadro-forma-pago">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                            <label>F. de Pago</label>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <select id="FormaPago" class="form-control form-control-sm" onchange="cambiar_forma_pago()" disabled>
                                                <!-- $option_forma_pago_credito -->
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 p-2 background-cuadro-forma-pago">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">

                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                            <label for="Detraccion">Detracción</label> <input type="checkbox" name="Detraccion" id="Detraccion" value="0" onchange="cambiar_detraccion()" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4 p-2">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">

                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 p-0">
                                            <button type="button" class="btn btn-sm btn-secondary float-right" id="btnAgregar" onclick="return agregar()">Agregar</button>
                                            <button type="button" class="btn btn-sm btn-success float-right display-none" id="btnAgregarMas" onclick="return agregar_fila()">Agregar <i class="fa ml-2 fa-plus-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <div class="table-responsive-md table-wrapper">
                                        <table class="table table-sm table-bordered" id="tabla_ingreso_ventas" cellspacing="0">
                                            <thead>
                                                <th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
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
                                                <th>&nbsp;&nbsp;Serie&nbsp;&nbsp;</th>
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
                                                <tr id="tr_vacio_ingreso_ventas">
                                                    <td align="center" colspan="31">No hay datos para mostrar</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <div class="table-responsive-md table-wrapper">
                                        <table class="table table-sm table-bordered" id="tabla_resultado" cellspacing="0">
                                            <thead>
                                                <th>Debe Soles</th>
                                                <th>Haber Soles</th>
                                                <th>Debe Dolar</th>
                                                <th>Haber Dolar</th>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="background-soles"><input type="text" name="TotalSol" class="form-control form-control-sm background-transparente border-none" id="total_DebeSol" readonly /></td>
                                                    <td class="background-soles"><input type="text" class="form-control form-control-sm background-transparente border-none" id="total_HaberSol" readonly /></td>
                                                    <td class="background-dolar"><input type="text" name="TotalDol" class="form-control form-control-sm background-transparente border-none" id="total_DebeDol" readonly /></td>
                                                    <td class="background-dolar"><input type="text" class="form-control form-control-sm background-transparente border-none" id="total_HaberDol" readonly /></td>
                                                </tr>
                                            </tbody>
                                        </table>
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

<div class="modal fade" id="clienteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Nuevo Cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                                    <select name="CodTipPer" id="CodTipPer" class="form-control form-control-sm" onchange="verificar_Tipo_Documento_Identidad()">
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
                                    <select name="CodTipoDoc" id="CodTipoDoc" class="form-control form-control-sm" onchange="verificar_Tipo_Documento_Identidad()">
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
                                    <input type="text" name="ruc" id="ruc" class="form-control form-control-sm" oninput="verificar_Longitud_Documento(this)" onkeypress="esNumero(event)" />
                                    <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('ruc')">
                                        <img src="<?= base_url() ?>/public/assets/img/ruc.png" width="15" height="15" />
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
                                    <input type="text" name="docidentidad" id="docidentidad" class="form-control form-control-sm" maxlength="11" oninput="verificar_Longitud_Documento(this)" onkeypress="esNumero(event)" />
                                    <button type="button" class="btn btn-sm height-sm border" onclick="consulta_sunat('dni')">
                                        <img src="<?= base_url() ?>/public/assets/img/dni.png" width="15" height="15" />
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
                                    <input type="text" name="ApePat" id="ApePat" class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>A. Materno</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" name="ApeMat" id="ApeMat" class="form-control form-control-sm" />
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
                                    <input type="text" name="Nom1" id="Nom1" class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <label>Nombre 2</label>
                                </div>
                                <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" name="Nom2" id="Nom2" class="form-control form-control-sm" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <label>Razón Social</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9">
                            <input type="text" name="razonsocial" id="razonsocial" class="form-control form-control-sm" readonly />
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <label>Dirección Principal</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-9 col-xl-9">
                            <input type="text" name="direccion1" id="direccion1" class="form-control form-control-sm" />
                        </div>
                    </div>
                    <div class="row mt-1">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <label>Condición</label>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                            <select name="IdCondicion" id="IdCondicion" class="form-control form-control-sm">
                                <?= $option_condicion ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="return verificarFormularioCliente()">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="bancoModal" tabindex="-1" role="dialog" aria-labelledby="bancoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bancoModalLabel">Seleccionar Cuenta bancaria - Cheque</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formBanco">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <label>Cuenta</label>
                                <select name="CodCuenta" id="CodCuentaBanco" class="form-control form-control-sm" onchange="cambiar_cuenta_contable_banco()">
                                    <!-- $options_plan_contable -->
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                <label>Descripción</label>
                                <input type="text" id="DescCuentaBanco" class="form-control form-control-sm" readonly />
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label>Cheque</label>
                                <input type="text" name="IdCheque" class="form-control form-control-sm" readonly />
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label>Número</label>
                                <input type="text" name="NumCheque" id="NumChequeBanco" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-5 col-xl-5">
                                <label>Tipo de Pago</label>
                                <select name="CodTipoPago" id="CodTipoPagoBanco" class="form-control form-control-sm">
                                    <!-- $options_tipo_pago -->
                                </select>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label>N° Transacci.</label>
                                <input type="text" name="GlosaDet" id="GlosaDetBanco" class="form-control form-control-sm" onkeypress="esNumero(event)" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="verificarFormularioBanco()">Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="documentoModal" tabindex="-1" role="dialog" aria-labelledby="documentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentoModalLabel">Listado de Documentos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="movimientoModal" tabindex="-1" role="dialog" aria-labelledby="movimientoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header background-header-movimiento">
                <h5 class="modal-title w-100" id="movimientoModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>