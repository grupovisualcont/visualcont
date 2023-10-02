<?= $this->extend('app/layout/master') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <span class="titulo-header-card">Plan Contable</span>
            <div class="float-end">
                <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <div class="dropdown-menu" x-placement="left-start">
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/accounting_plan/create') ?>">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/accounting_plan/excel') ?>">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </a>
                    <a class="dropdown-item" href="<?= base_url('app/mantenience/accounting_plan/pdf') ?>" target="_blank">
                        <i class="fas fa-print"></i> Imprimir
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive-md">
                <table class="table table-sm table-bordered" id="dataTablePlanContable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Cuenta</th>
                            <th>Descripción</th>
                            <th>Cta. Padre</th>
                            <th>Relación Cuenta</th>
                            <th>Tipo Resultado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>