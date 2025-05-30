<!DOCTYPE html>
<?php
include __DIR__ . '/../../config/database.php';

//eliminar ofertas caducadas
// Eliminar ofertas cuya fecha de salida es menor que hoy
$hoy = date("Y-m-d");
$sql = "DELETE FROM ofertas WHERE Fecha_expirada < '$hoy'";

if ($conn->query($sql) === TRUE) {
  // echo "Ofertas vencidas eliminadas correctamente.";
} else {
  echo "Error eliminando ofertas: " . $conn->error;
}

//$conn->close(); 
?>

<html data-bs-theme="light" lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <title>Administrador</title>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aclonica&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Acme&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=ADLaM+Display&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alef&amp;display=swap" />
  <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css" />
  <link rel="stylesheet" href="../assets/fonts/typicons.min.css" />
  <link rel="stylesheet" href="../assets/css/bs-theme-overrides.css" />
  <link rel="stylesheet" href="../assets/css/Checkbox-Input.css" />
  <link rel="stylesheet" href="../assets/css/Features-Cards-icons.css" />
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/css/theme.bootstrap_4.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <link rel="stylesheet" href="../assets/css/Table-with-Search--Sort-Filters-v20.css" />
  <link rel="stylesheet" href="../assets/css/untitled.css" />
</head>

<body>

  <div id="wrapper">

    <!-- inicia menu -->
    <?php include dirname(__DIR__, 2) . '/Admin/Menú/menu.php'; ?>
    <!-- termina menu -->

    <div id="content">
      <div class="d-grid float-end" id="form" style="margin-right: 50px">
        <form class="d-none d-sm-inline-block ms-md-3 my-2 my-md-0 mw-100 navbar-search" style="
                background: var(--bs-white);
                color: rgb(255, 255, 255);
                margin-right: 0px;
              ">
          <div class="input-group" style="background: var(--bs-light)">
            <input class="bg-light form-control border-0 small" onkeyup="searchOfertas()" id="searchInput" type="text"
              placeholder="Buscar...Producto..." style="background: var(--bs-light); color: rgb(0, 0, 0)" /><button
              class="btn btn-primary py-0" type="button" style="color: var(--bs-light); background: var(--bs-info)">
              <i class="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
      <div class="col search-table-col" style="
              margin: 9px 0px 0px;
              margin-top: 50px;
              margin-left: 50px;
              margin-right: 50px;
              margin-bottom: 50px;
            ">
        <h1 style="
                color: rgb(0, 0, 0);
                font-family: Nunito, sans-serif;
                margin-left: 15px;
              ">
          <div style="margin: 10px 15px;">

          </div>
          Productos en Oferta
        </h1>


        <!-- no borara por que se me descuadra -->
        <div class="d-grid float-end" style="margin-right: 50px">

        </div>

        <div>

          <div class="table-responsive text-center d-flex"
            style="margin-left: 50px; margin-right: 50px; border-top-left-radius: 2px; border-top-right-radius: 2px; border-bottom-right-radius: 2px; border-bottom-left-radius: 2px;">

            <!-- Tabla de Ofertas -->
            <table class="table table-hover">


              <?php
              // Consulta para obtener todas las ofertas
              $resultado = $conn->query("SELECT * FROM ofertas");

              // Obtener los nombres de columnas (campos)
              $columnas = $resultado->fetch_fields();
              ?>

              <thead>
                <tr style="background: var(--bs-info)" width="100%">
                  <?php foreach ($columnas as $col): ?>
                    <th style="background: var(--bs-table-accent-bg)"><?php echo htmlspecialchars($col->name); ?></th>
                  <?php endforeach; ?>
                  <th style="background: var(--bs-table-accent-bg) " width="15%">Acción</th>
                </tr>
              </thead>

              <tbody id="offerTable">
                <?php if ($resultado->num_rows > 0): ?>
                  <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <tr>
                      <?php foreach ($columnas as $col): ?>
                        <td>
                          <?php
                          $campo = $col->name;

                          if ($campo === 'imagen') {
                            $imagen_relativa = ltrim($fila[$campo], '/');
                            $absolute_image_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $imagen_relativa;
                            $web_image_path = (file_exists($absolute_image_path) && !empty($fila[$campo]))
                              ? '/' . $imagen_relativa
                              : '/assets/img/default-product.jpg';

                            $alt_text = isset($fila['Nombre_oferta']) ? htmlspecialchars($fila['Nombre_oferta']) : 'Imagen de la oferta';

                            echo '<img src="' . htmlspecialchars($web_image_path) . '" alt="' . $alt_text . '" 
                        style="width: 100px; height: 100px; object-fit: contain; border-radius: 20px 20px 0 0;" 
                        onerror="this.onerror=null;this.src=\'/assets/img/default-product.jpg\'; this.alt=\'Imagen no disponible\';">';
                          } else {
                            echo htmlspecialchars($fila[$campo]);
                          }
                          ?>
                        </td>
                      <?php endforeach; ?>

                      <td style="text-align: center">
                        <a class="btn btn-primary" role="button" style="background: var(--bs-warning); margin-right: 5px"
                          href="../Edición de Productos/modify_offer.php?id=<?php echo urlencode($fila['id_oferta']); ?>">
                          <i class="fa fa-edit" style="color: var(--bs-black)"></i>
                        </a>
                        <button class="btn btn-danger" style="margin-left: 5px" type="button" data-bs-toggle="modal"
                          data-bs-target="#deleteModal<?php echo $fila['id_oferta']; ?>">
                          <i class="fa fa-trash" style="font-size: 15px"></i>
                        </button>
                      </td>
                    </tr>

                    <!-- Modal de eliminación -->
                    <div class="modal fade" id="deleteModal<?php echo $fila['id_oferta']; ?>" tabindex="-1"
                      aria-labelledby="deleteModalLabel<?php echo $fila['id_oferta']; ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel<?php echo $fila['id_oferta']; ?>">Confirmar
                              Eliminación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            ¿Estás seguro de que deseas eliminar esta oferta? Esta acción no se puede deshacer.
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="../Edición de Ofertas/delete_offer.php" method="post" style="display:inline;">
                              <input type="hidden" name="id_oferta" value="<?php echo $fila['id_oferta']; ?>">
                              <button class="btn btn-danger" type="submit">Eliminar</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="<?php echo count($columnas) + 1; ?>" style="text-align: center; font-weight: bold;">
                      <p>No hay ofertas disponibles actualmente</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>



              <?php $conn->close(); ?>

            </table>
            <!-- Fin de la tabla de ofertas -->
          </div>
          <div class="d-grid float-end">
            <a class="btn btn-primary" role="button" style="
                  background: var(--bs-info);
                  font-weight: bold;
                  margin-right: 50px;
                " href="../Ofertas/add_offer.php">Agregar nueva oferta</a>
          </div>

        </div>
      </div>
    </div>

  </div>
  <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>

  <!-- inicia footer -->
  <?php include dirname(__DIR__, 2) . '/Admin/Menú/footer.php'; ?>
  <!-- termina footer --><!-- inicia footer -->

  </div>

  <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar eliminación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          ¿Estás seguro de que deseas eliminar esta oferta? Esta acción no se puede deshacer.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function cargarDatosModificacion(id, nombre, precio) {
      localStorage.setItem('usuarioEditar', JSON.stringify({
        id: id,
        nombre: nombre,
        contraseña: precio,
      }));
    }
  </script>


  <script>
    //Inicia la función de búsqueda de nombres
    function searchUsers() {
      const input = document.getElementById('searchInput');
      const filter = input.value.toUpperCase();
      const table = document.querySelector('table'); // Asegúrate de que selecciona la tabla correcta
      const tr = table.getElementsByTagName('tr');

      // Convertimos las filas a un array para poder ordenarlas
      const rowsArray = Array.from(tr).slice(1); // Excluimos el encabezado

      // Ordenamos las filas según la coincidencia
      rowsArray.sort((a, b) => {
        const aUser = a.getElementsByTagName('td')[0].textContent.toUpperCase();
        const aName = a.getElementsByTagName('td')[1].textContent.toUpperCase();
        const bUser = b.getElementsByTagName('td')[0].textContent.toUpperCase();
        const bName = b.getElementsByTagName('td')[1].textContent.toUpperCase();


        // Calculamos puntajes de coincidencia
        const aUserScore = calculateMatchScore(aUser, filter);
        const aNameScore = calculateMatchScore(aName, filter);
        const bUserScore = calculateMatchScore(bUser, filter);
        const bNameScore = calculateMatchScore(bName, filter);

        // Tomamos el mejor puntaje para cada fila
        const aMaxScore = Math.max(aUserScore, aNameScore);
        const bMaxScore = Math.max(bUserScore, bNameScore);

        // Ordenamos de mayor a menor puntaje
        return bMaxScore - aMaxScore;
      });

      // Mostramos/ocultamos filas según si coinciden
      rowsArray.forEach(row => {
        const user = row.getElementsByTagName('td')[0].textContent.toUpperCase();
        const name = row.getElementsByTagName('td')[1].textContent.toUpperCase();

        if (user.includes(filter) || name.includes(filter) || filter === '') {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      // Reinsertamos las filas ordenadas
      const tbody = table.querySelector('tbody');
      rowsArray.forEach(row => tbody.appendChild(row));
    }

    function calculateMatchScore(text, filter) {
      if (filter === '') return 0;

      // Puntaje más alto si coincide desde el inicio
      if (text.startsWith(filter)) return 3;

      // Puntaje medio si contiene el filtro
      if (text.includes(filter)) return 2;

      // Puntaje bajo si coincide parcialmente (solo algunas letras)
      const filterLetters = filter.split('');
      const matches = filterLetters.filter(letter => text.includes(letter)).length;
      return matches / filterLetters.length;
    }
  </script>
  <!-- Termina la función de búsqueda por usuarios o nombres en la tabla de usuarios principal -->

  <script>
    $(document).ready(function () {
      let ofertaId = null;

      // Abrir el modal al hacer clic en el botón de eliminar
      $(".btn-delete").on("click", function () {
        ofertaId = $(this).data("id"); // Obtener el ID de la oferta
        $("#confirmDeleteModal").modal("show"); // Mostrar el modal
      });

      // Confirmar la eliminación
      $("#confirmDeleteButton").on("click", function () {
        if (ofertaId) {
          // Enviar la solicitud POST al archivo delete_offer.php
          $.ajax({
            url: "../Edición%20de%20Ofertas/delete_offer.php",
            method: "POST",
            data: {
              id_oferta: ofertaId
            },
            success: function (response) {
              // Recargar la página o eliminar la fila de la tabla
              location.reload(); // Recargar la página para reflejar los cambios
            },
            error: function (xhr, status, error) {
              console.error("Error al eliminar la oferta:", error);
            }
          });

          // Cerrar el modal
          $("#confirmDeleteModal").modal("hide");
        }
      });
    });
  </script>

  <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>
  <script
    src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-storage.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="../assets/js/TableZoomSorter.js"></script>
  <script src="../assets/js/Tema_Admin.js"></script>
  <script src="../assets/js/WaveClickFX.js"></script>
  <script src="../JS/ofertas/get_values_table_offer.js"></script>
  <script>
    function editButtonClick(button) {
      getTableValues(button);
    }
  </script>
</body>

</html>