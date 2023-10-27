<?= $this->extend('web/layout/master') ?>

<?= $this->section('menu') ?>
<nav style="--bs-breadcrumb-divider: '/'" aria-label="breadcrumb" class="nav-breadcrumb" >
    <ol class="breadcrumb" >
        <li class="breadcrumb-item" >
            Mantenimiento 
        </li>
        <li class="breadcrumb-item" >
            <a href="#">Socio Negocio</a>
        </li>
        <li class="breadcrumb-item" >
            lista
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <section class="content-buttons btn-groups" >
        <a class="btn btn-primary btn-sm px-4" href="<?= baseUrlWeb('mantenience/business_partner/create') ?>" >
            <div class="d-flex align-items-center" >
                <i class="fa fa-plus me-2 fs-4" ></i> Nuevo
            </div>
        </a>
        <a  class="btn btn-info btn-sm px-4" href="<?= baseUrlWeb('mantenience/business_partner/excel') ?>" >
            <div class="d-flex align-items-center" >
                <i class="fa fa-file-excel me-2 fs-4" ></i> Exportar a Excel
            </div>
        </a>
        <a  class="btn btn-info btn-sm px-4" href="<?= baseUrlWeb('mantenience/business_partner/pdf') ?>" >
            <div class="d-flex align-items-center" >
                <i class="fa fa-print me-2 fs-4" ></i> Exportar a PDF
            </div>
        </a>
    </section>
    <section class="content-body" >
        <div class="card">
            <div class="card-body">
                <div class="table-responsive-md">
                    <table class="table table-sm table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Razón Social / Nombre Completo</th>
                                <th>Ruc</th>
                                <th>Dni</th>
                                <th>Estado</th>
                                <th>Condición</th>
                                <th>OP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($socio_negocio as $indice => $valor) {
                            ?>
                                <tr>
                                    <td><?= $valor['SocioNegocioNombre'] ?></td>
                                    <td><?= $valor['Ruc'] ?></td>
                                    <td><?= $valor['DocIdentidad'] ?></td>
                                    <td><?= $valor['Estado'] ?></td>
                                    <td><?= $valor['Condicion'] ?></td>
                                    <td align="center">
                                        <button type="button" class="btn btn-light btn-sm dropdown-toggle" data-bs-boundary="viewport" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-bars"></i>
                                        </button>
                                        <div class="dropdown-menu" x-placement="left-start">
                                            <a class="dropdown-item" href="<?= base_url('web/mantenience/business_partner/edit/' . $valor['CodSocioN']) ?>">
                                                <i class="fa fa-edit"></i> Editar
                                            </a>
                                            <button class="dropdown-item opcion-eliminar" data-id="<?= $valor['CodSocioN'] ?>" >
                                                <i class="fa fa-trash"></i> Eliminar
                                            </button>
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
    </section>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script src="<?= assetVersion('js/web/socio_negocio/lista.js') ?>" ></script>
<?= $this->endSection() ?>