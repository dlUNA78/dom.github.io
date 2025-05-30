<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<?php
include __DIR__ . '/../../config/database.php';
session_start();

if (!isset($_SESSION['user'])) {
  header("Location:/Admin/Menú/login.php");
  die();
}

// Obtener categorías
$sql = "SELECT id, nombre FROM categorias";
$resultado = $conn->query($sql);
$categorias = [];

if ($resultado->num_rows > 0) {
  while ($fila = $resultado->fetch_assoc()) {
    $categorias[] = $fila;
  }
}

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Obtener datos del formulario
  $nombre = trim($_POST['nombre']);
  $descripcion = trim($_POST['Descripción']);
  $precio = trim($_POST['precio']);
  $categoria = $_POST['categoria'];
  $imagenes = $_FILES['imagen'];

  // Si no hay errores, procesar el formulario
  if (empty($errores)) {
    // Insertar producto
    $sql = "INSERT INTO productos (nombre, descripcion, precio, id_categoria) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $categoria);
    $stmt->execute();
    $producto_id = $stmt->insert_id;
    $stmt->close();

    // Manejo de imágenes
    if (!empty($imagenes['name'][0])) {
      $carpetaDestino = realpath(__DIR__ . '/../../Admin/assets/img/productos') . DIRECTORY_SEPARATOR;

      if (!is_dir($carpetaDestino)) {
        if (!mkdir($carpetaDestino, 0777, true)) {
          die("No se pudo crear la carpeta de destino: $carpetaDestino");
        }
      }

      for ($i = 0; $i < count($imagenes['name']); $i++) {
        if ($imagenes['error'][$i] === UPLOAD_ERR_OK) {
          $nombreOriginal = basename($imagenes['name'][$i]);
          $nombreFinal = uniqid() . "_" . preg_replace('/[^a-zA-Z0-9\.\-_]/', '_', $nombreOriginal);
          $rutaCompleta = $carpetaDestino . $nombreFinal;
          $rutaEnDB = 'assets/img/productos/' . $nombreFinal;

          if (move_uploaded_file($imagenes['tmp_name'][$i], $rutaCompleta)) {
            $sqlImg = "INSERT INTO imagenes_producto (id_producto, ruta_imagen) VALUES (?, ?)";
            $stmtImg = $conn->prepare($sqlImg);
            $stmtImg->bind_param("is", $producto_id, $rutaEnDB);
            $stmtImg->execute();
            $stmtImg->close();
          }
        }
      }
    }

    header("Location: ../Menú/products.php?status=success");
    exit();
  }
}
?>

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
  <!-- Modal -->
  <div class="modal fade" role="dialog" tabindex="-1" id="Agregado">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="color: rgb(0, 0, 0)">
            Agregado Correctamente
          </h4>
          <button class="btn-close" type="button" aria-label="Close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button class="btn btn-light" type="button" data-bs-dismiss="modal" style="
                background: var(--bs-form-valid-border-color);
                color: rgb(255, 255, 255);
              ">
            Ok
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Terrmina el Modal -->

  <div id="wrapper">

    <!-- inicia menu -->
    <?php include dirname(__DIR__, 2) . '/Admin/Menú/menu.php'; ?>
     <!-- termina menu -->

      <div class="d-flex justify-content-center align-items-center" id="content">
        <div class="container d-flex flex-row justify-content-center" style="
              margin-left: 0px;
              margin-right: 0px;
              height: auto;
              width: 500px;
              margin-top: 0px;
              margin-bottom: 40px;
            ">
          <div class="card shadow-sm p-4">
            <h2 class="text-center mb-4" style="color: rgb(0, 0, 0); font-weight: bold">
              Agregar productos
            </h2>
            <form action="/p/Admin/Edición de Productos/add_product.php" method="POST" enctype="multipart/form-data">
              <div class="mb-3">
                <label class="form-label" for="nombre" style="color: rgb(0, 0, 0)">Nombre del producto:</label>
                <input class="form-control" type="text" id="nombre" name="nombre" required="" />
                <small id="errorNombre" class="text-danger"></small>
              </div>
              <div class="mb-3">
                <label class="form-label" for="descripcion" style="color: rgb(0, 0, 0)">Descripción:</label>
                <textarea class="form-control form-control" id="descripcion" name="Descripción" rows="3" required=""></textarea>
                <small id="errorDescripcion" class="text-danger"></small>
              </div>
              <div class="mb-3">
                <label class="form-label" for="precio" style="color: rgb(0, 0, 0)">Precio:</label>
                <input class="form-control" type="text" id="precio" name="precio" required="" />
                <small id="errorPrecio" class="text-danger"></small>
              </div>
              <div class="mb-3">
                <label class="form-label" for="categoria" style="color: rgb(0, 0, 0)">Categoría:</label>
                <select class="form-select" id="categoria" name="categoria" required="">
                  <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
                <small id="errorCategoria" class="text-danger"></small>
              </div>
              <div class="mb-3">
                <label class="form-label" for="imagenes" style="color: rgb(0, 0, 0)">Agregar Imágenes:</label>
                <input class="form-control" type="file" name="imagen[]" multiple>
                <small id="errorImagen" class="text-danger"></small>
              </div>
              <div class="d-flex justify-content-end gap-2">
                <button class="btn btn-primary" type="submit" id="btnAgregar"
                  style="background: var(--bs-info);">Agregar</button>
                <a class="btn btn-secondary" href="../Menú/products.php"
                  style="background: var(--bs-success);">Cancelar</a>
              </div>
            </form>
          </div>
        </div>
      </div>

  <!-- inicia footer -->
  <?php include dirname(__DIR__, 2) . '/Admin/Menú/footer.php'; ?>
  <!-- termina footer -->

    </div>
    <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
  </div>
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
  <script src="../JS/validar_add_prod.js"></script>
</body>

</html>