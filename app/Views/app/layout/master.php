<!DOCTYPE html>
<html lang="en">

<head>
    <!-- --------------------------------------------------- -->
    <!-- Title -->
    <!-- --------------------------------------------------- -->
    <title>Mordenize</title>

    <!-- --------------------------------------------------- -->
    <!-- Required Meta Tag -->
    <!-- --------------------------------------------------- -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="description" content="Mordenize" />
    <meta name="author" content="" />
    <meta name="keywords" content="Mordenize" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" type="image/png" href="<?= base_url('images/master/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/template/css/icons/font-awesome/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/libs/prismjs/themes/prism-okaidia.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/libs/select2/dist/css/select2.min.css') ?>" />
    <link id="themeColors" rel="stylesheet" href="<?= base_url('assets/template/css/style-aqua.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/datatables.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/alertify.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/master.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>" />
</head>

<body>

    <!-- Preloader -->
    <div class="preloader">
        <img src="<?= base_url('assets/template/images/favicon.ico') ?>" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!-- --------------------------------------------------- -->
    <!-- Body Wrapper -->
    <!-- --------------------------------------------------- -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <!-- --------------------------------------------------- -->
        <!-- Sidebar -->
        <!-- --------------------------------------------------- -->
        <?= $this->include('app/layout/left_menu'); ?>

        <!-- --------------------------------------------------- -->
        <!-- Main Wrapper -->
        <!-- --------------------------------------------------- -->
        <div class="body-wrapper">
            <!-- --------------------------------------------------- -->
            <!-- Header Start -->
            <!-- --------------------------------------------------- -->
            <?= $this->include('app/layout/top_menu') ?>
            <!-- --------------------------------------------------- -->
            <!-- Header End -->
            <!-- --------------------------------------------------- -->
            <?= $this->renderSection('content') ?>
        </div>
        <div class="dark-transparent sidebartoggler"></div>
        <div class="dark-transparent sidebartoggler"></div>

        <script src="<?= base_url('assets/template/libs/jquery/dist/jquery.min.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/simplebar/dist/simplebar.min.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>

        <script src="<?= base_url('assets/template/js/app.min.js') ?>"></script>
        <script src="<?= base_url('assets/template/js/app.init.js') ?>"></script>
        <script src="<?= base_url('assets/template/js/app-style-switcher.js') ?>"></script>
        <script src="<?= base_url('assets/template/js/sidebarmenu.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/select2/dist/js/select2.full.min.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/select2/dist/js/i18n/es.js') ?>"></script>

        <script src="<?= base_url('assets/template/js/custom.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/prismjs/prism.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/moment-js/moment.js') ?>"></script>
        <script src="<?= base_url('assets/template/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/datatables.js') ?>"></script>
        <script src="<?= base_url('assets/js/alertify.min.js') ?>"></script>
        <script src="<?= assetVersion('js/master.js') ?>"></script>
        <script>
            <?php
            if (isset($_SESSION['code']) && !empty($_SESSION['code'])) {
                if ($_SESSION['code'] == 'success') {
            ?>
                    alertify.success('<?= (isset($_SESSION['mensaje']) && !empty($_SESSION['mensaje'])) ? trim($_SESSION['mensaje']) : 'Correcto. Datos actualizados exitosamente' ?>');
                <?php
                } elseif ($_SESSION['code'] == 'error') {
                ?>
                    alertify.error('<?= (isset($_SESSION['mensaje']) && !empty($_SESSION['mensaje'])) ? trim($_SESSION['mensaje']) : 'Error. Consulte con el administrador' ?>');
            <?php
                }

                unset($_SESSION['code']);
                unset($_SESSION['mensaje']);
            }
            ?>

            alertify.defaults.transition = "slide";
            alertify.defaults.theme.ok = "btn btn-sm btn-primary";
            alertify.defaults.theme.cancel = "btn btn-sm btn-danger";

            var table = $('#dataTable').DataTable({
                language: {
                    "decimal": "",
                    "emptyTable": "No hay información",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                    "infoEmpty": "Mostrando 0 a 0 de 0 Entradas",
                    "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ Entradas",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "Sin resultados encontrados",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                },
                columnDefs: [{
                    type: '<?= isset($typeOrder) && !empty($typeOrder) ? $typeOrder : 'string' ?>',
                    'targets': [0]
                }],
                order: [
                    [0, 'asc']
                ],
                targets: 'no-sort',
                bSort: false
            });
        </script>
        <script type="text/javascript">
            const BASE_URL = "<?= base_url(); ?>"
        </script>
        <?= isset($script) && !empty($script) ? $script : '' ?>
        <?= $this->renderSection('script') ?>
</body>

</html>