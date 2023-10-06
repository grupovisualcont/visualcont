<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="./index.html" class="text-nowrap logo-img">
                <img src="https://e-vf.softwareintegrado.com/vc-cpe/assets/image/logovisualcont.png" class="dark-logo" width="250" height="45" alt="" />
                <img src="https://e-vf.softwareintegrado.com/vc-cpe/assets/image/logovisualcont.png" class="light-logo" width="250" height="45" alt="" />
                <span class="d-block text-version"><?= 'v' . APP_VERSION ?></span>
            </a>
            <div class="close-btn d-lg-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8 text-muted"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            <ul id="sidebarnav">
                <?php
                foreach ($sidebars as $sidebar) {
                    if (empty($sidebar['titulo'])) {
                ?>
                        <li class="nav-small-cap">
                            <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                            <span class="hide-menu text-black"><?= $sidebar['nombre'] ?></span>
                        </li>
                    <?php
                    } else {
                        $detalles = array();

                        foreach ($sidebardetalles as $detalle) {
                            if ($detalle['idSidebar'] == $sidebar['id']) {
                                $detalles[] = $detalle;
                            }
                        }
                    ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?= count($detalles) > 0 ? 'has-arrow' : '' ?>" href="#" aria-expanded="false">
                                <span class="d-flex">
                                    <i class="ti ti-box-multiple"></i>
                                </span>
                                <span class="hide-menu"><?= $sidebar['nombre'] ?></span>
                            </a>
                            <?php
                                if(count($detalles) > 0){
                            ?>
                            <ul aria-expanded="false" class="collapse first-level">
                                <?php
                                foreach ($detalles as $detalle) {
                                ?>
                                    <li class="sidebar-item">
                                        <a href="<?= base_url($detalle['pagina']) ?>" class="sidebar-link text-break">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu"><?= $detalle['nombre'] ?></span>
                                        </a>
                                    </li>
                                <?php
                                }
                                ?>
                            </ul>
                            <?php } ?>
                        </li>
                <?php
                    }
                }
                ?>
                <!-- <li class="sidebar-item">
                        <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                            <div class="round-16 d-flex align-items-center justify-content-center">
                                <i class="ti ti-circle"></i>
                            </div>
                            <span class="hide-menu">Level 1.1</span>
                        </a>
                        <ul aria-expanded="false" class="collapse two-level">
                            <li class="sidebar-item">
                                <a href="./chart-apex-line.html" class="sidebar-link">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Level 2</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link has-arrow" href="#" aria-expanded="false">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Level 2.1</span>
                                </a>
                                <ul aria-expanded="false" class="collapse three-level">
                                    <li class="sidebar-item">
                                        <a href="./chart-apex-line.html" class="sidebar-link">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu">Level 3</span>
                                        </a>
                                    </li>
                                    <li class="sidebar-item">
                                        <a href="./chart-apex-area.html" class="sidebar-link">
                                            <div class="round-16 d-flex align-items-center justify-content-center">
                                                <i class="ti ti-circle"></i>
                                            </div>
                                            <span class="hide-menu">Level 3.1</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li> -->
            </ul>
            <div class="unlimited-access hide-menu bg-light-primary position-relative my-7 rounded">
                <div class="d-flex">
                    <div class="unlimited-access-title w-100">
                        <h4 class="fs-2 mb-6 text-dark w-100 fw-bolder">
                            <?= $empresa['Ruc'] . ' ' . $empresa['RazonSocial'] ?>
                        </h4>
                        <div class="fw-normal fs-2 mb-6 text-dark w-100">
                            Tipo de Cambio <?= date('d/m/Y') ?>
                        </div>
                        <div class="fs-2 mb-6 text-dark w-100">
                            <div class="d-flex justify-content-between">
                                <span class="fw-normal">Compra: <?= $tipo_cambio->compra ?? '' ?></span>
                                <span class="fw-normal">Venta: <?= $tipo_cambio->venta ?? '' ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="unlimited-access-img">
                        <img src="../../dist/images/backgrounds/rocket.png" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </nav>
        <div class="fixed-profile p-3 bg-light-secondary rounded sidebar-ad mt-3">
            <div class="hstack gap-3">
                <div class="john-img">
                    <img src="../../dist/images/profile/user-1.jpg" class="rounded-circle" width="40" height="40" alt="">
                </div>
                <div class="john-title">
                    <h6 class="mb-0 fs-4 fw-semibold">Mathew</h6>
                    <span class="fs-2 text-dark">Designer</span>
                </div>
                <button class="border-0 bg-transparent text-primary ms-auto" tabindex="0" type="button" aria-label="logout" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="logout">
                    <i class="ti ti-power fs-6"></i>
                </button>
            </div>
        </div>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>