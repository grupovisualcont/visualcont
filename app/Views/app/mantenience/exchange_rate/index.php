<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Tipo de Cambio</span>
            <div class="float-end">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" x-placement="left-start">
                    <a class="dropdown-item" href="" id="excel">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                    <a class="dropdown-item" href="" id="pdf" target="_blank">
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
                            <th>Periodo</th>
                            <th>Mes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($meses as $indice => $valor) {
                        ?>
                            <tr class="Periodo" id="<?= $indice ?>">
                                <td><?= $anio ?></td>
                                <td><a href="javascript:void(0)" class="text-black-tabla" id="a<?= $indice ?>" onclick="seleccionar(<?= $anio ?>, <?= $indice ?>)"><?= $valor ?></a></td>
                                <td align="center">
                                    <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" x-placement="left-start">
                                        <a class="dropdown-item" href="<?= base_url('app/mantenience/exchange_rate/edit/' . $anio . '/' . $indice) ?>">
                                            <i class="fa fa-edit"></i> Editar
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

<script>
    var anio_hoy = <?= $anio ?>;
    var mes_hoy = <?= date('m') ?>;
</script>

<?= $this->endSection() ?>