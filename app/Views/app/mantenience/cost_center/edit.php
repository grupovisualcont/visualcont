<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card"><a href="<?= base_url('app/mantenience/cost_center/index') ?>" class="link-titulo">Centro de Costo</a> / Editar <button type="button" class="btn btn-primary btn-sm float-end" onclick="submit()">Guardar</button></span>
        </div>
        <div class="card-body">
            <div>
                <form id="form" class="mt-3" method="POST" action="<?= base_url('app/mantenience/cost_center/update') ?>" onsubmit="return verificarFormulario()">
                    <input type="hidden" name="CodEmpresa" value="<?= $centro_costo['CodEmpresa'] ?>" />
                    <div class="container-fluid my-3">
                        <div class="row">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>C. Costo Superior</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <select id="CodcCostoSuperior" class="form-control form-control-sm" onchange="setCodigo()" disabled>
                                            <?= $options_centro_costo_superior ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Código</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="CodcCosto" id="CodcCosto" class="form-control form-control-sm" value="<?= $centro_costo['CodcCosto'] ?>" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Centro Costo</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                        <input type="text" name="DesccCosto" id="DesccCosto" class="form-control form-control-sm" value="<?= $centro_costo['DesccCosto'] ?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <label>Porcentaje</label>
                                    </div>
                                    <div class="col-sm-12 col-md-12 col-lg-8 col-xl-8 d-flex">
                                        <input type="text" name="Porcentaje" class="form-control form-control-sm" value="<?= $centro_costo['Porcentaje'] ?>" onkeypress="esNumero(event)" />
                                        <span class="ml-2 mt-1">%</span>
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
                                        <select name="Estado" class="form-control form-control-sm">
                                            <?= $options_estados ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#info">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                        </button>
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

<div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Información</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table width="100%" cellspacing="0">
                    <tr>
                        <th>CENTRO DE COSTO</th>
                        <th>SUB-CENTRO COSTO</th>
                        <th>TASA</th>
                    </tr>
                    <tr>
                        <td>ADMINISTRACION</td>
                        <td>HUANCAYO</td>
                        <td>30%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>LIMA</td>
                        <td>40%</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>JUNÍN</td>
                        <td>30%</td>
                    </tr>
                </table>
                <table class="my-2" width="100%" cellspacing="0">
                    <tr>
                        <td width="45%">MONTO</td>
                        <td width="45%">===></td>
                        <td width="10%" class="bg-warning text-white font-weight-bold" align="right">5,000.00</td>
                    </tr>
                </table>
                <table width="100%" cellspacing="0">
                    <tr>
                        <td width="45%">HUANCAYO</td>
                        <td width="45%">30%</td>
                        <td width="10%" align="right">1,500.00</td>
                    </tr>
                    <tr>
                        <td>LIMA</td>
                        <td>40%</td>
                        <td align="right">2,000.00</td>
                    </tr>
                    <tr>
                        <td>JUNÍN</td>
                        <td>30%</td>
                        <td align="right">1,500.00</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>