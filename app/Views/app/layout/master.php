<!DOCTYPE html>
<html lang="en">

<head>
    <!-- --------------------------------------------------- -->
    <!-- Title -->
    <!-- --------------------------------------------------- -->
    <title><?= $page ?></title>

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
    <div class="page-wrapper show-sidebar" id="main-wrapper" data-layout="vertical" data-sidebartype="mini-sidebar" data-sidebar-position="fixed" data-header-position="fixed">
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

        <script>
            const VC_OPEN_MENU = "<?= ($openMenu) ? 'full' : 'mini-sidebar'; ?>";
        </script>

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
        <script src="<?= base_url('assets/template/libs/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js') ?>"></script>
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

            function esNumero(evt) {
                var theEvent = evt || window.event;

                if (theEvent.type === 'paste') {
                    key = event.clipboardData.getData('text/plain');
                } else {
                    var key = theEvent.keyCode || theEvent.which;
                    key = String.fromCharCode(key);
                }

                var regex = /[0-9]|\./;

                if (!regex.test(key)) {
                    theEvent.returnValue = false;

                    if (theEvent.preventDefault) theEvent.preventDefault();
                }
            }

            function esMayorCero(evt) {
                if (parseFloat(evt.value) <= 0) {
                    evt.value = '';
                }
            }

            function autocompletado(id, data, url) {
                $(id).select2({
                    placeholder: 'Seleccione',
                    dropdownAutoWidth: true,
                    ajax: {
                        url: url,
                        dataType: 'json',
                        type: 'POST',
                        data: function(params) {
                            var query = data;

                            query.search = params.term;

                            return query;
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id,
                                        text: item.text,
                                        disabled: item.disabled,
                                        class: item.class,
                                        TipoDato: item.TipoDato,
                                        tipo_dato: item.tipo_dato,
                                        RelacionCuenta: item.RelacionCuenta
                                    }
                                })
                            };
                        },
                    },
                    templateSelection: function(data, container) {
                        if (data.TipoDato) {
                            $(data.element).attr('data-tipo-dato', data.TipoDato);
                        }

                        if (data.tipo_dato) {
                            var tipo_dato = data.tipo_dato.split('|');
                            var longitud = tipo_dato[2];
                            var serie = tipo_dato[3];
                            var es_numero = tipo_dato[4].length == 0 ? 'no' : 'si';

                            $(data.element).attr('data-es-numero', es_numero);
                            $(data.element).attr('data-serie', serie);
                            $(data.element).attr('data-longitud', longitud);
                        }

                        if(data.RelacionCuenta) {
                            $(data.element).attr('data-relacion-cuenta', data.RelacionCuenta);
                        }

                        return data.text;
                    },
                    templateResult: function(data, container) {
                        if (data.class) {
                            $(container).addClass(data.class);
                        }

                        return data.text;
                    }
                });
            }

            function nuevo_option(id, data, url) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: data,
                    async: false,
                    success: function(data) {
                        var datos = JSON.parse(data);

                        if (datos.options) {
                            $(id).html(datos.options);
                        } else {
                            var option = new Option(datos.name, datos.value, true, true);

                            if (datos.TipoDato) option.setAttribute('data-tipo-dato', datos.TipoDato);

                            $(id).html(option).trigger('change');
                            $(id).val(datos.value);
                        }
                    }
                });
            }

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