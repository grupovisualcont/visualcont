<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Ingreso de Ventas</span>
            <div class="float-end">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" x-placement="left-start">
                    <a class="dropdown-item" href="<?= base_url('app/movements/sales/create2') ?>">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/movements/sales/import') ?>">
                        <i class="fas fa-download"></i> Importar
                    </a>
                    <a class="dropdown-item" id="excel" role="button">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                    <a class="dropdown-item" id="pdf" target="_blank" role="button">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-md">
                <table class="table table-sm table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre Voucher</th>
                            <th>Fec. Contable</th>
                            <th>Total Soles</th>
                            <th>Total Dolares</th>
                            <th>Glosa</th>
                            <th>P</th>
                            <th>A</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($movimiento_cab as $indice => $valor) {
                        ?>
                            <tr class="IdMov" id="<?= $valor['IdMov'] ?>">
                                <td><a href="javascript:void(0)" class="text-black-tabla" id="a<?= $valor['IdMov'] ?>" onclick="getMovimientoDet('<?= $valor['IdMov'] ?>')"><?= $valor['Codmov'] ?></a></td>
                                <td><?= $valor['DescVoucher'] ?></td>
                                <td><?= date('d/m/Y', strtotime($valor['FecContable'])) ?></td>
                                <td><?= number_format($valor['TotalSol'], 2, '.', ',') ?></td>
                                <td><?= number_format($valor['TotalDol'], 2, '.', ',') ?></td>
                                <td><?= $valor['Glosa'] ?></td>
                                <td align="center">
                                    <?php if (!empty($valor['IdMovRef'])) { ?>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#PAModal" onclick="set_IdMov_datos(<?= $valor['IdMovRef'] ?>)"><img src="<?= base_url('assets/img/voucher_cancelacion.png') ?>" width="25" height="25" /></a>
                                    <?php } ?>
                                </td>
                                <td align="center">
                                    <?php if (!empty($valor['IdMovAplica'])) { ?>
                                        <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#PAModal" onclick="set_IdMov_datos(<?= $valor['IdMovAplica'] ?>)"><img src="<?= base_url('assets/img/voucher_aplicacion.png') ?>" width="25" height="25" /></a>
                                    <?php } ?>
                                </td>
                                <td align="center" class="vertical-align-middle">
                                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start">
                                        <a class="dropdown-item" href="<?= base_url('app/movements/sales/editar/' . $valor['IdMov']) ?>">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <a class="dropdown-item" href="<?= base_url('app/movements/sales/eliminar/' . $valor['IdMov']) ?>" onclick="return confirm('¿Está seguro de eliminar?')">
                                            <i class="fa fa-trash"></i> Eliminar
                                        </a>
                                    </div>
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
    <div class="card shadow mb-4">
        <div id="tablaDetalles"></div>
    </div>
</div>

<div class="modal fade" id="PAModal" tabindex="-1" role="dialog" aria-labelledby="PAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title w-100 mx-3" id="PAModalLabel"></h5>
                <button type="button" class="close border-none" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="PAModalBody">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>