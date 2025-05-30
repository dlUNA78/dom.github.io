<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<!-- Incluir conexion PHP -->
<?php
include __DIR__ . '/../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location:/Admin/Menú/login.php");
  die();
}
?>

<!-- Fin de la conexión -->

<head>
  <meta charset="utf-8" />
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <title>Administrador</title>
  <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
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
  <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css" />
  <link rel="stylesheet" href="assets/fonts/typicons.min.css" />
  <link rel="stylesheet" href="assets/css/bs-theme-overrides.css" />
  <link rel="stylesheet" href="assets/css/Checkbox-Input.css" />
  <link rel="stylesheet" href="assets/css/Features-Cards-icons.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/css/theme.bootstrap_4.min.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <link
    rel="stylesheet"
    href="assets/css/Table-with-Search--Sort-Filters-v20.css" />
  <link rel="stylesheet" href="assets/css/untitled.css" />
</head>

<body>
  <div class="modal fade" role="dialog" tabindex="-1" id="deleteModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="color: rgb(0, 0, 0)">
            ModificadoCorrectamente
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
    <?php include '../Admin/Menú/menu.php';
    ?>
    <div class="d-flex flex-column" id="content-wrapper">

      <h1 style="color: rgb(0, 0, 0); margin-left: 10px">Categorías</h1>
      <div id="content" style="height: 400px;">
        <div class="d-flex justify-content-end">
          <form
            action="/Admin/JS/search_input.js"
            method="GET"
            class="input-group"
            id="buscador"
            style="background: var(--bs-light); width: 300px; margin-right: 10px; margin-top: 10px">
            <input
              name="query"
              class="bg-light form-control border-0 small"
              type="text"
              placeholder="Buscar categoría..."
              style="background: var(--bs-light); color: rgb(0, 0, 0)"
              onkeyup="searchCategories()"
              id="searchInput" />
            <button
              class="btn btn-primary py-0"
              type="submit"
              style="color: var(--bs-light); background: var(--bs-info)">
              <i class="fas fa-search"></i>
            </button>
          </form>

        </div>
        <div>
          <div
            class="d-flex justify-content-center align-items-center align-content-center mb-3">
            <div class="table-responsive d-flex justify-content-center">
              <table
                class="table table-striped table tablesorter"
                id="ipi-table">
                <thead class="thead-dark">
                  <tr>
                    <th
                      class="text-center"
                      style="background: var(--bs-info)">
                      Nombre de la categoría
                    </th>
                    <th
                      class="text-center filter-false sorter-false"
                      style="background: var(--bs-info); width: 150px">
                      Acciones
                    </th>
                  </tr>
                </thead>
                <tbody class="text-center" id="tableBody">

                  .
                  <!-- Verificar si hay registros -->
                  <?php
                  $sql = "SELECT * FROM categorias";
                  $result = $conn->query($sql);

                  if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>
    <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['nombre']) . "</td>
    <td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>";
                      if ($row['nombre'] !== 'default' && $row['id'] != 1) {
                        echo "
      <a class='btn btn-success' role='button'
         style='margin-left: 5px; background: var(--bs-warning); color: var(--bs-yellow);'
         href='\\p\\Admin\\Edición de Productos\\modify_cat.php?id=" . $row['id'] . "'>
          <i class='far fa-edit' style='font-size: 15px; color: rgb(7, 7, 7)'></i>
      </a>
      <button class='btn btn-danger' style='margin-left: 5px' type='button' data-bs-toggle='modal' data-bs-target='#deleteModal" . $row['id'] . "'>
        <i class='fa fa-trash' style='font-size: 15px'></i>
      </button>
      <!-- Modal -->
      <div class='modal fade' id='deleteModal" . $row['id'] . "' tabindex='-1' aria-labelledby='deleteModalLabel" . $row['id'] . "' aria-hidden='true'>
        <div class='modal-dialog'>
          <div class='modal-content'>
            <div class='modal-header'>
              <h5 class='modal-title' id='deleteModalLabel" . $row['id'] . "'>Confirmar Eliminación</h5>
              <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
              ¿Estás seguro de que deseas eliminar esta categoría?
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
              <form action='./Edición de Productos/php/eliminar_cat.php' method='post' style='display:inline;'>
                <input type='hidden' name='id' value='" . $row['id'] . "'>
                <button class='btn btn-danger' type='submit'>Eliminar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    ";
                      } else {
                        echo "<span class='text-muted'>No disponible</span>";
                      }
                      echo "</td></tr>";
                    }
                  } else {
                    echo "<tr><td colspan='2' style='padding: 10px; border: 1px solid #ddd; text-align: center;'>No se encontraron categorías</td></tr>";
                  }
                  ?>

                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-center align-items-end">
          <a
            class="btn btn-primary btn-icon-split"
            role="button"
            style="background: var(--bs-info); margin-left: 10px"
            href="../Admin/Edición de Productos/add_cat.php"><span class="text-white text">Agregar Nueva Categoría</span></a>
        </div>
      </div>

  </div>
  <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
  </div>
  <div class="modal fade" role="dialog" tabindex="-1" id="modal-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="color: rgb(0, 0, 0)">
            Estás Seguro de Continuar?
          </h4>
          <button
            class="btn-close"
            type="button"
            aria-label="Close"
            data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <p style="color: rgb(0, 0, 0)">Operacion X</p>
        </div>
        <div class="modal-footer">
          <button
            class="btn btn-light"
            type="button"
            data-bs-dismiss="modal"
            style="background: var(--bs-danger)">
            Cancelar</button><button
            class="btn btn-primary"
            type="button"
            style="background: var(--bs-dark)">
            Confirmar
          </button>
        </div>
      </div>
    </div>
  </div>
    <footer class="bg-white sticky-footer" ">
        <div class=" container my-auto">
        <div class="text-center my-auto copyright">
          <span><br />TECNM Campus Coalcomán Ingeniería en Sistemas
            Computacionales 6°Semestre -2025<br /><br /></span>
        </div>
    </div>
    </footer>


  <!-- Scripts -->
  <script>
    // Función para buscar categorías en la tabla
    function searchCategories() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toUpperCase();
      const table = document.querySelector('table');
      const rows = table.querySelectorAll('tbody tr');

      rows.forEach(row => {
        const category = row.querySelector('td').textContent.toUpperCase();
        row.style.display = category.includes(filter) || filter === '' ? '' : 'none';
      });
    }

    // Prevenir envío del formulario al presionar Enter
    document.getElementById('buscador').addEventListener('submit', function(event) {
      event.preventDefault();
    });
  </script>
  <!-- Termina la función de búsqueda por categorías en la tabla de categorías principal -->
  <script src="assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-storage.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="assets/js/TableZoomSorter.js"></script>
  <script src="assets/js/Tema_Admin.js"></script>
  <script src="assets/js/WaveClickFX.js"></script>
  <script src="/Admin/JS/search_input.js"></script>
</body>

</html>
