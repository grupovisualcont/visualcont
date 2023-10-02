<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Centro de Costo</span>
            <div class="float-end">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" x-placement="left-start">
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/cost_center/create') ?>">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/cost_center/excel') ?>">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/cost_center/pdf') ?>" target="_blank">
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
                            <th>Centro de Costo</th>
                            <th>Porcentaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($centro_costo as $indice => $valor) {
                        ?>
                            <tr>
                                <td><?= $valor['CodcCosto'] ?></td>
                                <td><?= $valor['DesccCosto'] ?></td>
                                <td><?= $valor['Porcentaje'] ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start">
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/cost_center/edit/' . $valor['CodcCosto']) ?>">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/cost_center/delete/' . $valor['CodcCosto']) ?>" onclick="return confirm('¿Está seguro de eliminar?')">
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
</div>

<?= $this->endSection() ?>