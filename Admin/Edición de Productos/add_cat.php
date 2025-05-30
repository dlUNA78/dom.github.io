<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<?php

session_start();

if (!isset($_SESSION['user'])) {
  header("Location:/Admin/Menú/login.php");
  die();
}
?>

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <title>Administrador</title>
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

<body>
  <div class="modal fade" role="dialog" tabindex="-1" id="Agregado">
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
            style="
                background: var(--bs-form-valid-border-color);
                color: rgb(255, 255, 255);
              ">
            Ok
          </button>
        </div>
      </div>
    </div>
  </div>
  <div id="wrapper">
    <?php include dirname(__DIR__,2) . '/Admin/Menú/menu.php'; ?>
    
    <div class="d-flex flex-column" id="content-wrapper">

      <div id="content">
        <div
          class="container d-flex justify-content-center align-items-center"
          style="width: 500px; height: 500px; margin-bottom: 40px">
          <div class="card shadow-sm p-4">
            <h2
              class="text-center mb-4"
              style="color: rgb(0, 0, 0); font-weight: bold">
              Agregar Categoría
            </h2>
            <form method="POST" action="../Edición de Productos/php/add_cat_func.php">
              <div class="mb-3">
                <label
                  class="form-label"
                  for="nombre"
                  style="color: rgb(0, 0, 0)">Nombre:</label>
                <input
                  class="form-control form-control"
                  name="nombre"
                  type="text"
                  id="nombre"
                  required="" />
                <div id="errorCategoria" class="text-danger"></div>
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button
                  class="btn btn-primary"
                  id="btnAgregar"
                  type="submit"
                  style="
                      background: var(--bs-info);
                      font-weight: bold;
                      margin-top: 10px;
                    ">
                  Agregar</button>
                <a
                  class="btn btn-secondary"
                  role="button"
                  style="
                      background: var(--bs-success);
                      font-weight: bold;
                      margin-top: 10px;
                    "
                  href="/p/Admin/categories.php">Cancelar</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
  </div>
  <footer>
    <?php include dirname(__DIR__,2) . '/Admin/Menú/footer.php'; ?>
  </footer>
  <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-storage.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="../assets/js/TableZoomSorter.js"></script>
  <script src="../assets/js/Tema_Admin.js"></script>
  <script src="../assets/js/WaveClickFX.js"></script>
  <script src="../JS/validar_cat.js"></script>
</body>

</html>