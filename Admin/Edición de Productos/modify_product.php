<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<?php
session_start();

if (!isset($_SESSION['user'])) {
  header("Location: /Admin/Menú/login.php");
  die();
}

include __DIR__ . '/../../config/database.php';

// Obtener categorías
$sqlCategorias = "SELECT id, nombre FROM categorias";
$resultadoCategorias = $conn->query($sqlCategorias);
$categorias = [];

if ($resultadoCategorias->num_rows > 0) {
  while ($fila = $resultadoCategorias->fetch_assoc()) {
    $categorias[] = $fila;
  }
}

// Obtener datos del producto a modificar
$producto = null;
$imagenes = [];
if (isset($_GET['producto'])) {
  $productoId = $_GET['producto'];
  
  $sqlProducto = "SELECT p.id, p.nombre, p.precio, p.descripcion, p.id_categoria, c.nombre as categoria_nombre 
                  FROM productos p 
                  LEFT JOIN categorias c ON p.id_categoria = c.id 
                  WHERE p.id = ?";
  $stmt = $conn->prepare($sqlProducto);
  $stmt->bind_param("i", $productoId);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $producto = $result->fetch_assoc();
    
    $sqlImagenes = "SELECT id, ruta_imagen FROM imagenes_producto WHERE id_producto = ?";
    $stmtImagenes = $conn->prepare($sqlImagenes);
    $stmtImagenes->bind_param("i", $productoId);
    $stmtImagenes->execute();
    $resultImagenes = $stmtImagenes->get_result();
    
    $imagenes = [];
    if ($resultImagenes->num_rows > 0) {
        while ($fila = $resultImagenes->fetch_assoc()) {
            $fila['ruta_imagen'] = str_replace('\\', '/', $fila['ruta_imagen']);
            $imagenes[] = $fila;
        }
    }
  }
  $stmt->close();
}

// Procesar el formulario de modificación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modificar'])) {
  $id = $_POST['id'];
  $nombre = $_POST['nombre'];
  $descripcion = $_POST['descripcion'];
  $precio = $_POST['precio'];
  $categoria = $_POST['categoria'];
  
  // Actualizar producto
  $sqlUpdate = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, id_categoria = ? WHERE id = ?";
  $stmtUpdate = $conn->prepare($sqlUpdate);
  $stmtUpdate->bind_param("ssdii", $nombre, $descripcion, $precio, $categoria, $id);
  $stmtUpdate->execute();
  
  // Manejo de nuevas imágenes
  if (!empty($_FILES['imagen']['name'][0])) {
    $carpetaDestino = realpath(__DIR__ . '../../assets/img/productos') . DIRECTORY_SEPARATOR;
    
    foreach ($_FILES['imagen']['name'] as $key => $name) {
      if ($_FILES['imagen']['error'][$key] === UPLOAD_ERR_OK) {
        $nombreOriginal = basename($name);
        $nombreFinal = uniqid() . "_" . $nombreOriginal;
        $rutaCompleta = $carpetaDestino . $nombreFinal;
        $rutaEnDB = 'assets/img/productos/' . $nombreFinal;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'][$key], $rutaCompleta)) {
          $sqlImg = "INSERT INTO imagenes_producto (id_producto, ruta_imagen) VALUES (?, ?)";
          $stmtImg = $conn->prepare($sqlImg);
          $stmtImg->bind_param("is", $id, $rutaEnDB);
          $stmtImg->execute();
          $stmtImg->close();
        }
      }
    }
  }
  
  // Eliminar imágenes seleccionadas
  if (!empty($_POST['eliminar_imagenes'])) {
    foreach ($_POST['eliminar_imagenes'] as $imagenId) {
      $sqlGetImg = "SELECT ruta_imagen FROM imagenes_producto WHERE id = ?";
      $stmtGetImg = $conn->prepare($sqlGetImg);
      $stmtGetImg->bind_param("i", $imagenId);
      $stmtGetImg->execute();
      $resultGetImg = $stmtGetImg->get_result();
      
      if ($resultGetImg->num_rows > 0) {
        $imgData = $resultGetImg->fetch_assoc();
        $rutaImagen = realpath(__DIR__ . '/../../' . $imgData['ruta_imagen']);
        if (file_exists($rutaImagen)) {
          unlink($rutaImagen);
        }
      }
      
      $sqlDelImg = "DELETE FROM imagenes_producto WHERE id = ?";
      $stmtDelImg = $conn->prepare($sqlDelImg);
      $stmtDelImg->bind_param("i", $imagenId);
      $stmtDelImg->execute();
      $stmtDelImg->close();
    }
  }
  
  header("Location: ../Menú/products.php?status=updated");
  exit();
}
?>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
  <title>Administrador</title>
  <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Aclonica&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Acme&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=ADLaM+Display&amp;display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Alef&amp;display=swap" />
  <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css" />
  <link rel="stylesheet" href="../assets/fonts/typicons.min.css" />
  <link rel="stylesheet" href="../assets/css/bs-theme-overrides.css" />
  <link rel="stylesheet" href="../assets/css/Checkbox-Input.css" />
  <link rel="stylesheet" href="../assets/css/Features-Cards-icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/css/theme.bootstrap_4.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
  <link rel="stylesheet" href="../assets/css/Table-with-Search--Sort-Filters-v20.css" />
  <link rel="stylesheet" href="../assets/css/untitled.css" />
</head>

<body>
  <div class="modal fade" role="dialog" tabindex="-1" id="modal_confirm">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="color: rgb(0, 0, 0)">
            Modificado Correctamente
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
  <div id="wrapper">

    <!-- inicia menu -->
    <?php include dirname(__DIR__, 2) . '/Admin/Menú/menu.php'; ?>
     <!-- termina menu -->

      <div class="d-flex justify-content-center align-items-center" id="content"></div>

      <div class="container" style="
            width: 500px;
            height: auto;
            margin-top: 10px;
            margin-bottom: 40px;
          ">
        <div class="card shadow-sm p-4">
          <h2 class="text-center mb-4" style="color: rgb(0, 0, 0); font-weight: bold">
            Modificar Producto
          </h2>
          <?php if ($producto): ?>
          <form id="formModificarProducto" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
            <input type="hidden" name="modificar" value="1">
            
            <!-- Nombre -->
            <div class="mb-3">
              <label id="producto" class="form-label" for="nombre" style="color: rgb(0, 0, 0)">Nombre del producto:</label>
              <input class="form-control form-control" type="text" id="nombre" name="nombre" required="" 
                     value="<?php echo htmlspecialchars($producto['nombre']); ?>" />
                                   <div id="errorNombre" class="text-danger"></div>
            </div>
            
            <!-- Descripcion -->
            <div class="mb-3">
              <label class="form-label" for="descripcion" style="color: rgb(0, 0, 0)">Descripción:</label>
              <textarea class="form-control form-control" id="descripcion" name="descripcion" rows="3" required=""><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                            <div id="errorDescripcion" class="text-danger"></div>
            </div>

            <!-- Precio -->
            <div class="mb-3">
              <label class="form-label" for="precio" style="color: rgb(0, 0, 0)">Precio:</label>
              <input class="form-control form-control" type="number" id="precio" name="precio" min="0" step="0.01" required="" 
                     value="<?php echo htmlspecialchars($producto['precio']); ?>" />
                                   <div id="errorPrecio" class="text-danger"></div>
            </div>
            
            <!-- Categoria -->
            <div class="mb-3">
              <label class="form-label" for="categoria" style="color: rgb(0, 0, 0)">Categoría:</label>
              <select class="form-select form-select" id="categoria" name="categoria" required="">
                <option value="">Seleccione una categoría</option>
                <?php foreach ($categorias as $categoriaItem): ?>
                  <option value="<?php echo $categoriaItem['id']; ?>" 
                          <?php echo ($categoriaItem['id'] == $producto['id_categoria']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categoriaItem['nombre']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div id="errorCategoria" class="text-danger"></div>
            </div>

            <!-- imagenes -->
            <?php if (!empty($imagenes)): ?>
    <div class="mb-3">
        <label class="form-label" style="color: rgb(0, 0, 0)">Imágenes actuales:</label>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($imagenes as $imagen): ?>
                <div class="position-relative" style="width: 100px;">
                    <img src="../<?php echo ltrim(htmlspecialchars($imagen['ruta_imagen']), '/'); ?>" 
                         class="img-thumbnail" 
                         style="width: 100px; height: 100px; object-fit: cover;"
                         onerror="this.onerror=null; this.src='/assets/img/productos/default.jpg';">
                    <div class="form-check position-absolute top-0 start-0">
                        <input class="form-check-input" type="checkbox" name="eliminar_imagenes[]" 
                               value="<?php echo $imagen['id']; ?>" id="eliminar_<?php echo $imagen['id']; ?>">
                        <label class="form-check-label" for="eliminar_<?php echo $imagen['id']; ?>"></label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <small class="text-muted">Marque las imágenes que desea eliminar</small>
    </div>
<?php endif; ?>

            <!-- termina imagenes -->
            <div class="mb-3">
              <label class="form-label" for="imagenes" style="color: rgb(0, 0, 0)">Agregar nuevas imágenes:</label>
              <input class="form-control" type="file" id="imagenes" name="imagen[]" multiple="" accept="image/*" />
              <div id="errorImagenes" class="text-danger"></div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
              <button class="btn btn-primary" type="submit" id="btnModificar" style="
                    background: var(--bs-info);
                    font-weight: bold;
                    margin-top: 10px;
                  ">
                Modificar
              </button>
              <a class="btn btn-secondary" role="button" style="
                    background: var(--bs-success);
                    font-weight: bold;
                    margin-top: 10px;
                  " href="../Menú/products.php">Cancelar</a>
            </div>
          </form>
          <?php else: ?>
            <div class="alert alert-danger" role="alert">
              No se encontró el producto solicitado.
            </div>
            <a class="btn btn-secondary" role="button" href="../Menú/products.php">Volver</a>
          <?php endif; ?>
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-storage.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script src="../assets/js/TableZoomSorter.js"></script>
  <script src="../assets/js/Tema_Admin.js"></script>
  <script src="../assets/js/WaveClickFX.js"></script>
  <script src="../JS/validar_mod_prod.js"></script>

</body>
</html>