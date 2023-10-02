<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Caja - Bancos</span>
            <div class="float-end">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" x-placement="left-start">
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/box_banks/create') ?>">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/box_banks/excel') ?>">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/box_banks/pdf') ?>" target="_blank">
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
                            <th>Banco</th>
                            <th>Abreviado</th>
                            <th>Cta. Cte</th>
                            <th>Moneda</th>
                            <th>Cuenta</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($bancos as $indice => $valor) {
                        ?>
                            <tr>
                                <td><?= $valor['Codbanco'] ?></td>
                                <td><?= $valor['DescFinanciera'] ?></td>
                                <td><?= $valor['abreviatura'] ?></td>
                                <td><?= $valor['ctacte'] ?></td>
                                <td><?= $valor['DescMoneda'] ?></td>
                                <td><?= $valor['DescCuenta'] ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start">
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/box_banks/edit/' . $valor['Codbanco'] . '/' . $valor['Periodo']) ?>">
                                            <i class="fa fa-edit"></i> Editar
                                        </a>
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/box_banks/delete/' . $valor['Codbanco'] . '/' . $valor['Periodo']) ?>" onclick="return confirm('¿Está seguro de eliminar?')">
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