<?= $this->extend('app/layout/master') ?>

<?= $this->section('menu') ?>
<nav style="--bs-breadcrumb-divider: '/'" aria-label="breadcrumb" class="nav-breadcrumb" >
    <ol class="breadcrumb" >
        <li class="breadcrumb-item" >
            Movimientos 
        </li>
        <li class="breadcrumb-item" >
            <a href="#">Ingreso de compra</a>
        </li>
        <li class="breadcrumb-item" >
            Crear
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <section class="content-buttons btn-groups" >
        <button type="button" class="btn btn-primary btn-sm px-4" >
            <div class="d-flex align-items-center" >
                <i class="fa fa-save me-2 fs-4" ></i> Grabar
            </div>
        </button>
        <button type="button" class="btn btn-link btn-sm px-4" >
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
                                        <label for="tipo_voucher" class="control-label col-xl-2 col-lg-2 col-12" >
                                            Tipo
                                        </label>
                                        <div class="col-xl-10 col-lg-10 col-12" >
                                            <select class="form-select form-select-sm form-select-vc" 
                                            id="tipo_voucher" name="tipo_voucher" >
                                                <?php if (!empty($objTypeVoucher)) { ?>
                                                    <option value="<?= $objTypeVoucher->CodTV ?>">
                                                        <?= $objTypeVoucher->DescVoucher ?>
                                                    </option>
                                                <?php } ?>
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
                                            <div class="input-group input-group-sm" >
                                                <span class="input-group-text" >COM</span>
                                                <input type="text" class="form-control form-control-sm form-control-vc" 
                                                value="<?= substr($voucher, 3, strlen($voucher)) ?>" >
                                            </div>
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
                                                <input type="text" class="form-control mydatepicker" placeholder="dd/mm/yyyy"
                                                value="21/09/2023" >
                                                <span class="input-group-text" >
                                                    <i class="fa fa-calendar" ></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" >
                                <div class="col-xl-9 col-lg-9 col-md-9 col-12" >
                                    <div class="form-group row" >
                                        <label for="" class="control-label col-xl-1 col-lg-1 col-12" >
                                            Glosa
                                        </label>
                                        <div class="col-xl-11 col-lg-11 col-12" >
                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                            value="" >
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-1 col-lg-1 col-md-1 col-12 d-none" >
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
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
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
                                        <div class="col-xl-9 col-md-9 col-12" >
                                            <div class="form-group row" for="socio_negocio" >
                                                <label for="" class="control-label col-xl-2 col-lg-2 col-12" >
                                                    Proveedor
                                                </label>
                                                <div class="col-xl-10 col-lg-10 col-12" >
                                                    <select class="form-select form-select-sm form-select-vc" id="socio_negocio" name="socio_negocio" ></select>
                                                </div>
                                            </div>
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-2 col-lg-2 col-12" >
                                                    Comprobante
                                                </label>
                                                <div class="col-xl-10 col-lg-10 col-12" >
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
                                                            Número
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12" >
                                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                                            value="" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="condicion_pago" class="control-label col-xl-4 col-lg-4 col-12" >
                                                            Cond. Pago
                                                        </label>
                                                        <div class="col-xl-8 col-lg-8 col-12" >
                                                            <select class="form-select form-select-sm form-select-vc" id="condicion_pago" name="condicion_pago" ></select>
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
                                                                <input type="text" class="form-control mydatepicker" placeholder="dd/mm/yyyy"
                                                                value="21/09/2023" >
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
                                                                <input type="text" class="form-control mydatepicker" placeholder="dd/mm/yyyy"
                                                                value="21/09/2023" >
                                                                <span class="input-group-text" >
                                                                    <i class="fa fa-calendar" ></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="moneda" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Moneda
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <select class="form-select form-select-sm form-select-vc" id="moneda" name="moneda" >
                                                                <?php if (!empty($objCurrency)) { ?>
                                                                    <option value="<?= $objCurrency->CodMoneda ?>">
                                                                        <?= $objCurrency->DescMoneda ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            T/C
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <input type="text" class="form-control form-control-sm form-control-vc" 
                                                            value="" >
                                                        </div>
                                                    </div>
                                                    <div class="form-group row" >
                                                        <label for="operation_type" class="control-label col-xl-5 col-lg-5 col-12" >
                                                            Tipo Operación
                                                        </label>
                                                        <div class="col-xl-7 col-lg-7 col-12" >
                                                            <select class="form-select form-select-sm form-select-vc" id="operation_type" name="operation_type" >
                                                                <?php if (!empty($objOperationType)) { ?>
                                                                    <option value="<?= $objOperationType->IdAnexo ?>">
                                                                        <?= $objOperationType->DescAnexo ?>
                                                                    </option>
                                                                <?php } ?>
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
                                        <div class="col-xl-3 col-md-3 col-12 bg-vc-100" >
                                            <div class="form-group row mt-2" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    Afecto
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    Inafecto
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    Exonerado
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    Descuento
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    Ancitipo
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    ISC
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0 fw-bolder" >
                                                    IGV 18%
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-1" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-12 text-white pe-0" >
                                                    Total
                                                </label>
                                                <div class="col-xl-6 col-lg-6 col-md-6 col-8" >
                                                    <div class="input-group input-group-sm input-group-vc">
                                                        <input type="text" class="form-control text-end bg-white"
                                                        value="" >
                                                    </div>
                                                </div>
                                                <div class="col-xl-2 col-lg-2 col-md-2 col-4 p-0" >
                                                    <span class="text-white" >70111</span>
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
                            <div class="row" >
                                <div class="col-xl-8 col-lg-8 col-md-8 col-12" >
                                    <div class="row" >
                                        <div class="col-xl-8 col-lg-8 col-md-8 col-12" >
                                            <div class="form-group row" >
                                                <label for="" class="control-label col-xl-4 col-lg-4 col-md-4 col-5" >
                                                    Fecha de Pago
                                                </label>
                                                <div class="col-xl-8 col-lg-8 col-md-8 col-7" >
                                                    <input type="text" class="form-control form-control-sm form-control-vc"
                                                    readonly 
                                                    value="NINGUNO" >
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
                                    <button type="button" class="btn btn-info btn-sm px-4" >
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


<?= $this->section('script') ?>
<script src="<?= assetVersion('js/app/condicion_pago/select2.js') ?>"></script>
<script src="<?= assetVersion('js/app/tipo_voucher/select2.js') ?>"></script>
<script src="<?= assetVersion('js/app/socio_negocio/select2.js') ?>"></script>
<script src="<?= assetVersion('js/app/anexo/select2.js') ?>"></script>
<script src="<?= assetVersion('js/app/moneda/select2.js') ?>"></script>
<script src="<?= assetVersion('js/app/compra/generar.js') ?>"></script>
<?= $this->endSection() ?>