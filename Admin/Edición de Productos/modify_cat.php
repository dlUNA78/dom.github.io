<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<!-- Inicia conexion para actulizar datos -->
<?php


session_start();

if (!isset($_SESSION['user'])) {
  header("Location:/Admin/Menú/login.php");
  die();
}



include __DIR__ . '/../../config/database.php';
// Obtener el ID del registro a editar
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Obtener los datos del registro
  $sql = "SELECT * FROM categorias WHERE id = $id";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
  } else {
    echo "Registro no encontrado.";
    exit();
  }
} else {
  echo "ID no proporcionado.";
  exit();
}
?>
<!-- Termina conexion de base de datos -->


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

  <div id="wrapper">
    <?php
    include dirname(__DIR__) . '/Menú/menu.php';

    ?>

    <div class="d-flex flex-column" id="content-wrapper">

      <div id="content">

        <div
          class="container d-flex justify-content-center align-items-center"
          style="width: 500px; height: auto; margin-bottom: 40px">
          <div class="card shadow-sm p-4">
            <h2
              class="text-center mb-4"
              style="color: rgb(0, 0, 0); font-weight: bold">
              Nuevo Nombre:
            </h2>
            <form method="POST" action="./php/update_cat.php">
              <div class="mb-3">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <label
                  class="form-label"
                  for="nombre"
                  style="color: rgb(0, 0, 0)">Nombre:</label>

                <input
                  class="form-control form-control"
                  type="text"
                  id="nombre"
                  name="nombre"
                  value="<?php echo $row['nombre']; ?>"
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
                  Agregar</button><a
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
  <footer class="bg-white sticky-footer">
    <?php
    include dirname(__DIR__) . '/Menú/footer.php';
    ?>
  </footer>
  <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-storage.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="../assets/js/TableZoomSorter.js"></script>
  <script src="../assets/js/Tema_Admin.js"></script>
  <script src="../assets/js/WaveClickFX.js"></script>
  <script src="../JS/valida_mod_cat.js"></script>
</body>

</html>