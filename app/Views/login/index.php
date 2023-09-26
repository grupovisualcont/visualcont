<!DOCTYPE html>
<html lang="en">

<head>
    <!--  Title -->
    <title>Mordenize</title>
    <!--  Required Meta Tag -->
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
    <link id="themeColors" rel="stylesheet" href="<?= base_url('assets/template/css/style-aqua.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('assets/css/master.css') ?>" />
</head>

<body>
    <!-- Preloader -->
    <div class="preloader">
        <img src="../../dist/images/logos/favicon.ico" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!-- Preloader -->
    <div class="preloader">
        <img src="../../dist/images/logos/favicon.ico" alt="loader" class="lds-ripple img-fluid" />
    </div>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden radial-gradient min-vh-100">
            <div class="position-relative z-index-5">
                <div class="row">
                    <div class="col-xl-7 col-xxl-8">
                        <a href="/" class="text-nowrap logo-img d-block px-4 py-9 w-100">
                            <img src="../../dist/images/logos/dark-logo.svg" width="180" alt="">
                        </a>
                        <div class="d-none d-xl-flex align-items-center justify-content-center" style="height: calc(100vh - 80px);">
                            <img src="https://institutocontable.org/wp-content/uploads/2023/09/cursosire.jpg" alt="" class="img-fluid" width="500">
                        </div>
                    </div>
                    <div class="col-xl-5 col-xxl-4">
                        <div class="authentication-login min-vh-100 bg-body row justify-content-center align-items-center p-4">
                            <div class="col-sm-8 col-md-6 col-xl-9">
                                <h2 class="mb-3 fs-7 fw-bolder">Inicio de Sesión</h2>
                                <p class="text-danger"><?= isset($login) ? $login : '' ?></p>
                                <span class="text-danger"><?= \Config\Services::validation()->listErrors() ?></span>
                                <form class="user" method="POST" action="<?= base_url('login') ?>">
                                    <div class="form-floating mb-3">
                                        <input type="text" name="usuario" class="form-control border border-info" placeholder="Usuario">
                                        <label>
                                            <i class="ti ti-user me-2 fs-4 text-info"></i>
                                            <span class="border-start border-info ps-3">
                                                Usuario
                                            </span>
                                        </label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="password" name="password" class="form-control border border-info" placeholder="Contraseña">
                                        <label>
                                            <i class="ti ti-lock me-2 fs-4 text-info"></i>
                                            <span class="border-start border-info ps-3">
                                                Contraseña
                                            </span>
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                Recordar sesión
                                            </label>
                                        </div>
                                        <a class="text-primary fw-medium" href="/">Recuperar contraseña ?</a>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">
                                        <i class="fa fa-sign-in-alt"></i> Iniciar Sesión
                                    </button>
                                    <!--<div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-medium">New to Modernize?</p>
                                        <a class="text-primary fw-medium ms-2" href="./authentication-register.html">Create an account</a>
                                    </div>-->
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="<?= base_url('assets/template/libs/jquery/dist/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/template/libs/simplebar/dist/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/template/libs/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>

    <script src="<?= base_url('assets/template/js/app.min.js') ?>"></script>
    <script src="<?= base_url('assets/template/js/app.init.js') ?>"></script>
    <script src="<?= base_url('assets/template/js/app-style-switcher.js') ?>"></script>
    <script src="<?= base_url('assets/template/js/sidebarmenu.js') ?>"></script>

    <script src="<?= base_url('assets/template/js/custom.js') ?>"></script>
</body>

</html>