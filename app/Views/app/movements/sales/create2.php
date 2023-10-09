<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <section class="content-buttons btn-groups" >
        <button type="button" class="btn btn-info font-medium rounded-pill px-4" >
            <div class="d-flex align-items-center" >
                <i class="fa fa-save me-2 fs-4" ></i> Grabar
            </div>
        </button>
        <button type="button" class="btn btn-link font-medium rounded-pill px-4" >
            <div class="d-flex align-items-center" >
                Cancelar
            </div>
        </button>
    </section>
    <section class="content-body" >
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-12 col-12" >
                                    <div class="form-group row" >
                                        <label for="" class="control-label col-xl-2 col-lg-2 col-12" >
                                            Tipo
                                        </label>
                                        <div class="col-xl-10 col-lg-10 col-12" >
                                            <select name="CodTV" id="CodTV" class="form-select form-select-sm form-select-vc" onchange="cambiar_condicion_pago()" autofocus>
                                                <?= $option_tipo_voucher ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 col-12" >
                                    <div class="form-group row" >
                                        <label for="" class="control-label col-xl-3 col-lg-3 col-12" >
                                            Voucher
                                        </label>
                                        <div class="col-xl-9 col-lg-9 col-12" >
                                            <input type="text" name="Codmov" id="Codmov" class="form-control form-control-sm form-control-vc" value="<?= $codigo_voucher_maximo ?>" onfocus="cambiar_codigo()" onfocusout="cambiar_codigo()" maxlength="20">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-6 col-12" >
                                    <div class="form-group row" >
                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12" >
                                            Fecha Reg.
                                        </label>
                                        <div class="col-xl-8 col-lg-8 col-12" >
                                            <div class="input-group input-group-sm input-group-vc">
                                                <input type="text" name="FecContable" id="FecContable" class="form-control mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y') ?>" onchange="cambiar_fecha_contable()" >
                                                <span class="input-group-text" >
                                                    <i class="fa fa-calendar" ></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-xl-8 col-lg-8 col-md-8 col-12" >
                                    <div class="form-group row" >
                                        <label for="" class="control-label col-xl-2 col-lg-2 col-12" >
                                            Glosa
                                        </label>
                                        <div class="col-xl-10 col-lg-10 col-12" >
                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                            value="" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-2 col-12" >
                                    <div class="form-group" >
                                        <div class="form-check form-check-reverse">
                                            <label class="form-check-label" for="flexCheckDefault">
                                                Anulado
                                            </label>
                                            <input class="form-check-input" type="checkbox" id="flexCheckDefault" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">
                                        Datos del comprobante
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">
                                        Datos del comprobamte Referencia
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane px-2 py-3 fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-xl-8 col-md-8 col-12" >
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-3 col-lg-3 col-12" >
                                                    Cliente
                                                </label>
                                                <div class="col-xl-9 col-lg-9 col-12" >
                                                    <select class="form-select form-select-sm form-select-vc" >
                                                        <option value="1" >Cliente 1</option>
                                                        <option value="2" >Cliente 2</option>
                                                        <option value="3" >Cliente 3</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-3 col-lg-3 col-12" >
                                                    Comprobante
                                                </label>
                                                <div class="col-xl-9 col-lg-9 col-12" >
                                                    <select class="form-select form-select-sm form-select-vc" >
                                                        <option value="1" >Factura</option>
                                                        <option value="2" >Boleta</option>
                                                        <option value="3" >Nota de Credito</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" >
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-12" >
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12" >
                                                            Serie
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12" >
                                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                                            value="" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12" >
                                                            Nro Inicial
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12" >
                                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                                            value="" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12" >
                                                            Nro Final
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12" >
                                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                                            value="" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-4 col-lg-4 col-12" >
                                                            Cond. Pago
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12" >
                                                            <select name="CodCondPago" id="CodCondPago" class="form-select form-select-sm form-select-vc" >
                                                                <?= $option_condicion_pago_credito ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-12" >
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Fecha Emisión
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <div class="input-group input-group-sm input-group-vc">
                                                                <input type="text" name="FecEmision" id="FecEmision" class="form-control mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y') ?>" onchange="cambiar_tipo_cambio_from_FecEmision()" >
                                                                <span class="input-group-text" >
                                                                    <i class="fa fa-calendar" ></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Fecha Vcto
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <div class="input-group input-group-sm input-group-vc">
                                                                <input type="text" name="FecVcto" id="FecVcto" class="form-control mydatepicker" placeholder="dd/mm/yyyy" value="<?= date('d/m/Y') ?>" >
                                                                <span class="input-group-text" >
                                                                    <i class="fa fa-calendar" ></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Moneda
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <select class="form-select form-select-sm form-select-vc" >
                                                                <option value="1" >Soles</option>
                                                                <option value="2" >Dolares</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            T/C
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <input type="text" name="ValorTC" id="ValorTC" class="form-control form-control-sm form-control-vc" value="<?= $tipo_cambio_venta ?>" onkeypress="esNumero(event)">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Tipo Operación
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <select class="form-select form-select-sm form-select-vc" >
                                                                <option value="1" >Gravada</option>
                                                                <option value="2" >Inafecto</option>
                                                                <option value="2" >Exonerado</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Tasa Igv
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                                            value="" >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-4 col-12 bg-vc-100" >
                                            <div class="form-group row mt-3" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    Afecto
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Afecto" class="form-control text-end bg-white" value="0" oninput="set_total('Afecto')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelAfecto">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    Inafecto
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Inafecto" class="form-control text-end bg-white" value="0" oninput="set_total('Inafecto')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelInafecto">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    Exonerado
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Exonerado" class="form-control text-end bg-white" value="0" oninput="set_total('Exonerado')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelExonerado">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    Descuento
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Descuento" class="form-control text-end bg-white" oninput="set_total('Descuento')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelDescuento">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    Anticipo
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Anticipo" class="form-control text-end bg-white" oninput="set_total('Anticipo')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelAnticipo">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    ISC
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="ISC" class="form-control text-end bg-white" value="0" oninput="set_total('ISC')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelIsc">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white fw-bolder" >
                                                    Igv 18%
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Igv" class="form-control text-end bg-white" value="0" oninput="set_total('Igv')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelIgv">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5 text-white" >
                                                    Total
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" id="Total" class="form-control text-end bg-white" value="0" oninput="set_total('Total')" onkeypress="esNumero(event)">
                                                        <span class="input-group-text text-white px-3" id="labelTotal">-</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane px-2 py-3 fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                    <div class="row" >
                                        <div class="col-xl-7 col-lg-7 col-md-7 col-12" >
                                            <div class="btn-group" >
                                                <button type="button" class="btn btn-info font-medium px-4 me-1" >
                                                    Referencia existente
                                                </button>
                                                <button type="button" class="btn btn-info font-medium px-4 me-1" >
                                                    Referencia manual
                                                </button>
                                                <button type="button" class="btn btn-info font-medium px-4" >
                                                    Quitar Referencia
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-xl-5 col-lg-5 col-md-5 col-12" >
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-12" >
                                                    Total Referencia
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-12" >
                                                    <input type="text" class="form-control form-control-sm form-control-vc text-end" 
                                                    value="0.00" >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-3" >
                                        <table class="table table-sm table-bordered" >
                                            <thead class="table-primary" >
                                                <th>OP</th>
                                                <th>Tipo</th>
                                                <th>Comprobante</th>
                                                <th>Serie</th>
                                                <th>Numero</th>
                                                <th>F. Emision</th>
                                                <th>Cuenta</th>
                                                <th>Cod Mov</th>
                                            </thead>
                                            <tfoot>
                                                <tr><td colspan="8" >&nbsp;</td></tr>
                                                <tr><td colspan="8" >&nbsp;</td></tr>
                                                <tr><td colspan="8" >&nbsp;</td></tr>
                                                <tr><td colspan="8" >&nbsp;</td></tr>
                                                <tr><td colspan="8" >&nbsp;</td></tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3" >
                                <div class="col-xl-8 col-lg-8 col-md-8 col-12" >
                                    <div class="row" >
                                        <div class="col-xl-8 col-lg-8 col-md-8 col-12" >
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5" >
                                                    Forma de Pago
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <select id="FormaPago" class="form-control form-control-sm form-control-vc" onchange="cambiar_forma_pago()" disabled>
                                                        <option value="NINGUNO">NINGUNO</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-12" >
                                            <div class="form-check">
                                                <label class="form-check-label" for="flexCheckDefault2">
                                                    Detracción
                                                </label>
                                                <input class="form-check-input" type="checkbox" id="flexCheckDefault2" value="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-12 text-end" >
                                    <button type="button" class="btn btn-info font-medium px-4" >
                                        <i class="fa fa-plus" ></i> Agregar
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive mt-3" >
                                <table class="table table-sm table-bordered" >
                                    <thead class="table-primary" >
                                        <th>OP</th>
                                        <th>Item</th>
                                        <th>Descripción</th>
                                        <th>M</th>
                                        <th>T/C</th>
                                        <th>Debes Soles</th>
                                        <th>Haber Soles</th>
                                        <th>Debes Dolar</th>
                                        <th>Haber Dolar</th>
                                        <th>Fecha Emisión</th>
                                        <th>Fecha Vencto</th>
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
                                        <th>% Ret</th>
                                        <th>% Detra</th>
                                        <th>Fecha Detra</th>
                                        <th>TO Det</th>
                                        <th>35 Contrato Proyect</th>
                                        <th>Periodo a Declarar</th>
                                        <th>Estado a Declarar</th>
                                        <th>Activo Fijo</th>
                                    </thead>
                                    <tfoot>
                                        <tr><td colspan="30" >&nbsp;</td></tr>
                                        <tr><td colspan="30" >&nbsp;</td></tr>
                                        <tr><td colspan="30" >&nbsp;</td></tr>
                                        <tr><td colspan="30" >&nbsp;</td></tr>
                                        <tr><td colspan="30" >&nbsp;</td></tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="form-group row mt-3" >
                                <label for="" class="control-label col-xl-5 col-lg-5 col-12 text-danger" >
                                    Comprobante (03), va al Reg. Ventas 14.1
                                </label>
                                <div class="col-xl-7 col-lg-7 col-12" >
                                    <div class="input-group input-group-sm input-group-vc" >
                                        <input type="text" class="form-control form-control-sm form-control-vc me-2" 
                                        value="" >
                                        <input type="text" class="form-control form-control-sm form-control-vc me-2" 
                                        value="" >
                                        <input type="text" class="form-control form-control-sm form-control-vc me-2" 
                                        value="" >
                                        <input type="text" class="form-control form-control-sm form-control-vc" 
                                        value="" >
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive mt-3" >
                                <table class="table table-sm table-bordered" >
                                    <thead class="table-primary" >
                                        <th>OP</th>
                                        <th>Item</th>
                                        <th>Cuenta</th>
                                        <th>Descripción</th>
                                        <th>M</th>
                                        <th>T/C</th>
                                        <th>Debe Sol</th>
                                        <th>Haber Sol</th>
                                        <th>Debe Dol</th>
                                        <th>Haber Dol</th>
                                        <th>Cod Doc.</th>
                                        <th>Serie</th>
                                        <th>Numero</th>
                                        <th>Cuenta Origen</th>
                                        <th>C. Costo</th>
                                    </thead>
                                    <tfoot>
                                        <tr><td colspan="15" >&nbsp;</td></tr>
                                        <tr><td colspan="15" >&nbsp;</td></tr>
                                        <tr><td colspan="15" >&nbsp;</td></tr>
                                        <tr><td colspan="15" >&nbsp;</td></tr>
                                        <tr><td colspan="15" >&nbsp;</td></tr>
                                    </tfoot>
                                </table>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    </section>
</div>

<?= $this->endSection() ?>