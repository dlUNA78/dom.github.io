<!DOCTYPE html>
<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location:login.php");
  die();
}


?>
<html data-bs-theme="light" lang="en">

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <title>Dashboard - Brand</title>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap" />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Aclonica&amp;display=swap" />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Acme&amp;display=swap" />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=ADLaM+Display&amp;display=swap" />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Alef&amp;display=swap" />
  <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css" />
  <link rel="stylesheet" href="../assets/fonts/typicons.min.css" />
  <link rel="stylesheet" href="../assets/css/bs-theme-overrides.css" />
  <link rel="stylesheet" href="../assets/css/Checkbox-Input.css" />
  <link rel="stylesheet" href="../assets/css/Features-Cards-icons.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/css/theme.bootstrap_4.min.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <link
    rel="stylesheet"
    href="../assets/css/Table-with-Search--Sort-Filters-v20.css" />
  <link rel="stylesheet" href="../assets/css/untitled.css" />
</head>

<body id="page-top">
  <div id="wrapper">
  <?php include './menu.php';
        ?>
   
    <div class="d-flex flex-column" id="content-wrapper">
      <div id="content">
        <!-- AUN FALTA AGREGAR EL LOGIN -->
      
      </div>
      <div
        class="container d-flex justify-content-center align-items-center flex-wrap m-auto py-4 py-xl-5"
        style="text-align: justify">
        <div
          class="row gy-4 row-cols-4 row-cols-md-2 row-cols-xl-3 text-center d-flex justify-content-center align-items-center"
          style="margin-left: 50px; margin-right: 50px">
          <div
            class="col-md-4 d-flex justify-content-center align-items-center"
            style="
                width: 350px;
                height: 200px;
                margin-top: 20px;
                margin-right: 20px;
                margin-bottom: 20px;
                margin-left: 20px;
              ">
            <a
              class="text-decoration-none"
              href="../Edición%20de%20Productos/add_product.php">
              <div
                class="card d-flex justify-content-center align-items-center"
                style="height: 200px; width: 350px">
                <div class="card-body p-4">
                  <div
                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center d-inline-block mb-3 bs-icon"
                    style="background: var(--bs-green)">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="1em"
                      height="1em"
                      fill="currentColor"
                      viewBox="0 0 16 16"
                      class="bi bi-cart3"
                      style="color: rgb(0, 0, 0)">
                      <path
                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"></path>
                    </svg>
                  </div>
                  <h4 class="card-title" style="color: rgb(0, 0, 0)">
                    Agregar Productos
                  </h4>
                  <p class="card-text" style="color: rgb(0, 0, 0)">
                    Haga clic aquí para agregar productos.
                  </p>
                </div>
              </div>
            </a>
          </div>
          <div
            class="col-md-4 d-flex justify-content-center align-items-center"
            style="
                width: 350px;
                height: 200px;
                margin-top: 20px;
                margin-right: 20px;
                margin-bottom: 20px;
                margin-left: 20px;
              ">
            <a class="text-decoration-none" href="../Ofertas/add_offer.php">
              <div
                class="card d-flex justify-content-center align-items-center"
                style="width: 350px; height: 200px">
                <div class="card-body p-4" style="width: auto; height: auto">
                  <div
                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center d-inline-block mb-3 bs-icon"
                    style="background: var(--bs-warning)">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="1em"
                      height="1em"
                      fill="currentColor"
                      viewBox="0 0 16 16"
                      class="bi bi-percent"
                      style="color: rgb(0, 0, 0)">
                      <path
                        d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0M4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5m7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3m0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"></path>
                    </svg>
                  </div>
                  <h4 class="card-title" style="color: rgb(0, 0, 0)">
                    Agregar Ofertas
                  </h4>
                  <p class="card-text" style="color: rgb(0, 0, 0)">
                    Clic aquí para agregar más ofertas.
                  </p>
                </div>
              </div>
            </a>
          </div>
          <div
            class="col-md-4 d-flex justify-content-center align-items-center"
            style="
                width: 350px;
                height: 200px;
                margin-top: 20px;
                margin-right: 20px;
                margin-bottom: 20px;
                margin-left: 20px;
              ">
            <a
              class="text-decoration-none"
              href="../Edición%20de%20Usuarios/add_user.php">
              <div
                class="card d-flex justify-content-center align-items-center"
                style="width: 350px; height: 200px">
                <div class="card-body p-4">
                  <div
                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center d-inline-block mb-3 bs-icon"
                    style="background: var(--bs-cyan)">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="1em"
                      height="1em"
                      fill="currentColor"
                      viewBox="0 0 16 16"
                      class="bi bi-person-add"
                      style="color: rgb(0, 0, 0)">
                      <path
                        d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"></path>
                      <path
                        d="M8.256 14a4.474 4.474 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10c.26 0 .507.009.74.025.226-.341.496-.65.804-.918C9.077 9.038 8.564 9 8 9c-5 0-6 3-6 4s1 1 1 1z"></path>
                    </svg>
                  </div>
                  <h4 class="card-title" style="color: rgb(0, 0, 0)">
                    Agregar Usuario
                  </h4>
                  <p class="card-text" style="color: rgb(0, 0, 0)">
                    Aquí podrá agregar más usuarios.
                  </p>
                </div>
              </div>
            </a>
          </div>
          <div
            class="col-md-4 d-flex justify-content-center align-items-center"
            style="
                width: 350px;
                height: 200px;
                margin-top: 20px;
                margin-right: 20px;
                margin-bottom: 20px;
                margin-left: 20px;
              ">
            <a
              class="text-decoration-none"
              href="../Edición%20de%20Productos/add_cat.php">
              <div
                class="card d-flex justify-content-center align-items-center"
                style="width: 350px; height: 200px">
                <div class="card-body p-4">
                  <div
                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex justify-content-center align-items-center d-inline-block mb-3 bs-icon"
                    style="background: var(--bs-orange)">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      width="1em"
                      height="1em"
                      fill="currentColor"
                      viewBox="0 0 16 16"
                      class="bi bi-list-ul"
                      style="color: rgb(0, 0, 0)">
                      <path
                        fill-rule="evenodd"
                        d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m-3 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2m0 4a1 1 0 1 0 0-2 1 1 0 0 0 0 2"></path>
                    </svg>
                  </div>
                  <h4 class="card-title" style="color: rgb(0, 0, 0)">
                    Agregar Categoría
                  </h4>
                  <p class="card-text" style="color: rgb(0, 0, 0)">
                    Aquí puedes&nbsp; agregar las categorías.
                  </p>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>
      <footer class="bg-white sticky-footer">
        <div class="container my-auto">
          <div class="text-center my-auto copyright">
            <span><br />TECNM Campus Coalcomán Ingeniería en Sistemas
              Computacionales 6°Semestre -2025<br /><br /></span>
          </div>
        </div>
      </footer>
    </div>
    <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
  </div>
  <div class="modal fade" role="dialog" tabindex="-1" id="modal-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="color: rgb(0, 0, 0)">
            Agregado Correctamente
          </h4>
          <button
            class="btn-close"
            type="button"
            aria-label="Close"
            data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button
            class="btn btn-light"
            type="button"
            data-bs-dismiss="modal"
            style="background: var(--bs-form-valid-border-color)">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-storage.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="../assets/js/TableZoomSorter.js"></script>
  <script src="../assets/js/Tema_Admin.js"></script>
  <script src="../assets/js/WaveClickFX.js"></script>
</body>

</html>