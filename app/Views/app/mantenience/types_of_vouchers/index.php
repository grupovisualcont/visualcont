<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Tipo de Vouchers</span>
            <div class="float-end">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" x-placement="left-start">
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/types_of_vouchers/create') ?>">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/types_of_vouchers/excel') ?>">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/types_of_vouchers/pdf') ?>" target="_blank">
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
                            <th>Descripción</th>
                            <th>Glosa</th>
                            <th>N° Or</th>
                            <th>CodEFE</th>
                            <th>OP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($tipoVoucherCab as $indice => $valor) {
                        ?>
                            <tr class="CodTV" id="<?= $valor['CodTV'] ?>">
                                <td><a href="javascript:void(0)" class="text-black-tabla" id="a<?= $valor['CodTV'] ?>" onclick="getTipoVoucherDetalles('<?= $valor['CodTV'] ?>')"><?= $valor['CodTV'] ?></a></td>
                                <td><?= $valor['DescVoucher'] ?></td>
                                <td><?= $valor['GlosaVoucher'] ?></td>
                                <td><?= $valor['Norden'] ?></td>
                                <td><?= $valor['CodEFE'] ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start">
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/types_of_vouchers/edit/' . $valor['CodTV']) ?>">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/types_of_vouchers/delete/' . $valor['CodTV']) ?>" onclick="return confirm('¿Está seguro de eliminar?')">
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

<?= $this->endSection() ?>