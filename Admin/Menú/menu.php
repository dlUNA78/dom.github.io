<!-- barra izquierda -->
<nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary p-0 navbar-dark"
    style="background: var(--bs-primary)">
    <div class="container-fluid d-flex flex-column p-0">
        <a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="/p/Admin/Menú/index.php"><img
                src=" <?php dirname(__DIR__, 2) ?>/p/Admin/assets/img/Logo Yesid.svg" style="width: 50px; height: 50px; margin-right: -11px" />
            <div class="sidebar-brand-icon rotate-n-15"></div>
            <div class="sidebar-brand-text mx-3">
                <span style="color: var(--bs-black)">Administrador</span>
            </div>
        </a>
        <hr class="sidebar-divider my-0" />
        <ul class="navbar-nav text-light" id="accordionSidebar">
            <li class="nav-item">
                <a class="nav-link" href="/p/Admin/Menú/index.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                        height="1em" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-dashboard"
                        style="color: rgb(0, 0, 0); font-size: 22.6px">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 13m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M13.45 11.55l2.05 -2.05"></path>
                        <path d="M6.4 20a9 9 0 1 1 11.2 0z"></path>
                    </svg><span style="color: var(--bs-black)">Principal</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/p/Admin/Menú/products.php"><i class="typcn typcn-shopping-cart"
                        style="color: rgb(0, 0, 0); font-size: 22.6px"></i><span
                        style="color: var(--bs-black)">Productos</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/p/Admin/categories.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                        height="1em" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-category"
                        style="color: rgb(0, 0, 0); font-size: 22.6px">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 4h6v6h-6z"></path>
                        <path d="M14 4h6v6h-6z"></path>
                        <path d="M4 14h6v6h-6z"></path>
                        <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                    </svg><span style="color: var(--bs-black)">Categorías</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/p/Admin/Ofertas/view_ofer_produc.php"><svg xmlns="http://www.w3.org/2000/svg"
                        width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-currency-dollar"
                        style="color: rgb(0, 0, 0); font-size: 22.6px">
                        <path
                            d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718H4zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73l.348.086z">
                        </path>
                    </svg><span style="color: var(--bs-black)">Ofertas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/p/Admin/Menú/user.php"><svg xmlns="http://www.w3.org/2000/svg" width="1em"
                        height="1em" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-users"
                        style="font-size: 22.6px; color: rgb(0, 0, 0)">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        <path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>
                    </svg><span style="color: var(--bs-black)">Usuarios</span></a>
            </li>
        </ul>
        <div class="text-center d-none d-md-inline">
            <button class="btn rounded-circle border-0" id="sidebarToggle" type="button"
                style="background: var(--bs-info); color: var(--bs-light)"></button>
        </div>
    </div>
</nav>
<!-- barra superior -->
<div class="d-flex flex-column" id="content-wrapper">
    <div id="content">
        <nav class="navbar navbar-expand bg-white shadow mb-4 topbar">
            <div class="container-fluid">
                <button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <ul class="navbar-nav flex-nowrap ms-auto">
                    <li class="nav-item dropdown no-arrow">
                        <div class="nav-item dropdown no-arrow">
                            <?php
                            // Nombre del archivo guardado en la sesión (solo nombre, sin ruta)
                            $imagen_nombre = isset($_SESSION['imagen']) ? basename($_SESSION['imagen']) : '';

                            // Extensiones permitidas
                            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];

                            // Extraer extensión
                            $extension = strtolower(pathinfo($imagen_nombre, PATHINFO_EXTENSION));

                            // Rutas (manejo seguro con DIRECTORY_SEPARATOR)
                            $ruta_relativa = 'p/Admin/assets/img/avatars/';
                            $ruta_absoluta = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . DIRECTORY_SEPARATOR
                                . str_replace('/', DIRECTORY_SEPARATOR, $ruta_relativa)
                                . $imagen_nombre;

                            // Ruta web (para el navegador)
                            $ruta_web = '/' . trim($ruta_relativa, '/') . '/' . $imagen_nombre;

                            // Validación
                            if (
                                empty($imagen_nombre) ||
                                !in_array($extension, $extensiones_permitidas) ||
                                !file_exists($ruta_absoluta)
                            ) {
                                $web_image_path = '/' . trim($ruta_relativa, '/') . '/perfil_default.avif';
                            } else {
                                $web_image_path = $ruta_web;
                            }
                            ?>
                            <a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#">
                                <span class="d-none d-lg-inline me-2 text-gray-600 small">
                                    <?php echo isset($_SESSION['user']) ? $_SESSION['user'] : 'Guest'; ?>
                                </span>
                                <img class="border rounded-circle img-profile" src="<?php echo htmlspecialchars($web_image_path); ?>" />
                            </a>
                            <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                <a class="dropdown-item" role="button" href="/p/Admin/Menú/log_out.php"><i
                                        class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Cerrar
                                    Sesión</a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>