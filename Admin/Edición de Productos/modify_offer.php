<!DOCTYPE html>
<html data-bs-theme="light" lang="en">
<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location:/Admin/Menú/login.php");
  die();
}

// Conexión a la base de datos
include __DIR__ . '/../../config/database.php';

if (isset($_GET['id'])) {
  $id_oferta = $_GET['id'];

  // Obtener la oferta
  $stmt = $conn->prepare("SELECT * FROM ofertas WHERE id_oferta = ?");
  $stmt->bind_param("i", $id_oferta);
  $stmt->execute();
  $resultado = $stmt->get_result();

  if ($resultado->num_rows === 1) {
    $oferta = $resultado->fetch_assoc();

    // Obtener imágenes asociadas al producto de esta oferta
    $id_producto = $oferta['id_producto'] ?? null;

    if ($id_producto !== null) {
      $stmt_img = $conn->prepare("SELECT ruta_imagen FROM imagenes_producto WHERE id_producto = ?");
      $stmt_img->bind_param("i", $id_producto);
      $stmt_img->execute();
      $resultado_img = $stmt_img->get_result();
      $imagenes = $resultado_img->fetch_all(MYSQLI_ASSOC);
    } else {
      $imagenes = [];
    }
  } else {
    echo "No se encontró la oferta";
    exit;
  }
} else {
  echo "ID de oferta no proporcionado";
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $id = $_POST['id'];
  $nombre = $_POST['Nombre_oferta'];
  $precio = $_POST['precio'];
  $precio_oferta = $_POST['precio_oferta'];
  $fecha_inicio = $_POST['Fecha_inicio'];
  $fecha_expirada = $_POST['Fecha_expirada'];

  // Aquí el cambio importante:
  // Si descripción viene vacía, mantenemos la que había antes
  if (isset($_POST['descripcion']) && trim($_POST['descripcion']) !== '') {
    $descripcion = $_POST['descripcion'];
  } else {
    $descripcion = $oferta['descripcion'];
  }

  // Verificar si se subió una nueva imagen
  if (isset($_FILES['nueva_imagen']) && $_FILES['nueva_imagen']['error'] === UPLOAD_ERR_OK) {
    $imagen_tmp = $_FILES['nueva_imagen']['tmp_name'];
    $imagen_nombre = basename($_FILES['nueva_imagen']['name']);
    $ruta_destino = "../assets/img/ofertas/" . $imagen_nombre;

    if (move_uploaded_file($imagen_tmp, $ruta_destino)) {
      if (!empty($oferta['imagen']) && file_exists("../assets/img/ofertas/" . basename($oferta['imagen']))) {
        unlink("../assets/img/ofertas/" . basename($oferta['imagen']));
      }
      $imagen_final = '/Admin/assets/img/ofertas/' . $imagen_nombre;
    } else {
      echo "Error al subir la nueva imagen.";
      exit;
    }
  } elseif (!empty($_POST['imagen_existente'])) {
    $imagen_final = $_POST['imagen_existente'];
  } else {
    $imagen_final = $oferta['imagen'];
  }

  // Actualizar la base de datos
  $stmt = $conn->prepare("UPDATE ofertas SET Nombre_oferta=?, precio=?, precio_oferta=?, Fecha_inicio=?, Fecha_expirada=?, imagen=?, descripcion=? WHERE id_oferta=?");
  $stmt->bind_param("sddssssi", $nombre, $precio, $precio_oferta, $fecha_inicio, $fecha_expirada, $imagen_final, $descripcion, $id);

  if ($stmt->execute()) {
    header("Location: ../Ofertas/view_ofer_produc.php?success=1");
    exit;
  } else {
    echo "Error al actualizar la oferta";
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

  <div class="modal fade" role="dialog" tabindex="-1" id="modal-1">
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
          <button class="btn btn-light" type="button" data-bs-dismiss="modal"
            style="background: var(--bs-form-valid-border-color)">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
  <div id="wrapper">
    <!-- inicia menu -->
    <?php include dirname(__DIR__, 2) . '/Admin/Menú/menu.php'; ?>
    <!-- termina menu -->
    <div class="d-flex justify-content-center align-items-center" id="content" style="min-height: 80vh;">
      <div class="card shadow-sm p-4" style="max-width: 480px; width: 100%;">
        <h2 class="text-center mb-4" style="color: rgb(0, 0, 0); font-weight: bold">
          Modificar Oferta
        </h2>


        <!-- inicia formulario -->
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?php echo $oferta['id_oferta']; ?>" />
          <div class="mb-3">
            <label class="form-label" for="nombre" style="color: rgb(0, 0, 0)">Nombre del Producto:</label>
            <input class="form-control form-control" type="text" id="nombre" name="Nombre_oferta"
              value="<?php echo htmlspecialchars($oferta['Nombre_oferta']); ?>" required readonly />
          </div>

          <div class="mb-3">
            <label class="form-label" for="descripcion" style="color: rgb(0, 0, 0)">Descripción del Producto:</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required
              readonly><?php echo htmlspecialchars($oferta['descripcion']); ?></textarea>
          </div>


          <div class="mb-3">
            <label class="form-label" for="precio" style="color: rgb(0, 0, 0)">Precio:</label>
            <input id="precioBe" class="form-control" type="number" name="precio"
              value="<?php echo htmlspecialchars($oferta['precio']); ?>" required readonly />
          </div>
          <div class="mb-3">
            <label class="form-label" for="precio_oferta" style="color: rgb(0, 0, 0)">Precio con descuento:</label>
            <input id="precioNew" class="form-control form-control" type="number" name="precio_oferta"
              value="<?php echo htmlspecialchars($oferta['precio_oferta']); ?>" required />
            <div id="errorPrecioNew" class="text-danger"></div>
          </div>

          <div class="mb-3">
            <label style="color: rgb(0, 0, 0)">Fecha de inicio:</label>
            <input type="date" name="Fecha_inicio" value="<?php echo htmlspecialchars($oferta['Fecha_inicio']); ?>"
              required />
          </div>

          <div class="mb-3">
            <label style="color: rgb(0, 0, 0)">Fecha de expiración:</label>
            <input type="date" name="Fecha_expirada" value="<?php echo htmlspecialchars($oferta['Fecha_expirada']); ?>"
              required />
          </div>

          <div class="mb-3">
            <?php if (!empty($imagenes)): ?>
              <div class="mb-3">
                <label class="form-label" style="color: rgb(0, 0, 0)">Seleccionar una imagen existente:</label>
                <div class="row">
                  <?php foreach ($imagenes as $img): ?>
                    <?php
                    // Construimos la ruta pública completa correcta, agregando '/Admin/' solo una vez
                    $ruta_completa = '/Admin/' . ltrim($img['ruta_imagen'], '/');
                    // Comparamos con la imagen guardada para marcar el radio seleccionado
                    $checked = ($ruta_completa === $oferta['imagen']) ? 'checked' : '';
                    ?>
                    <div class="col-3 text-center">
                      <label>
                        <input type="radio" name="imagen_existente" value="<?php echo htmlspecialchars($ruta_completa); ?>"
                          style="margin-bottom: 5px;" <?php echo $checked; ?>>
                        <img src="<?php echo htmlspecialchars($ruta_completa); ?>"
                          style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #ccc;" />
                      </label>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>


            <label class="form-label" for="imagen_actual" style="color: rgb(0, 0, 0)">Imagen Actual:</label>
            <div>
              <?php
              $imagen_relativa = isset($oferta['imagen']) ? ltrim($oferta['imagen'], '/') : '';
              $absolute_image_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $imagen_relativa;
              $web_image_path = '/' . $imagen_relativa;

              if (!file_exists($absolute_image_path) || empty($oferta['imagen'])) {
                $web_image_path = '/assets/img/default-product.jpg';
              }
              ?>
              <img src="<?php echo htmlspecialchars($web_image_path); ?>" alt="Imagen de la oferta"
                style="width: 100px; height: 100px; object-fit: cover;" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="nueva_imagen" style="color: rgb(0, 0, 0)">Subir Nueva Imagen
              (opcional):</label>
            <input class="form-control" type="file" id="nueva_imagen" name="nueva_imagen" accept="image/*" />
          </div>
          <div class="mb-3"></div>
          <div class="d-flex justify-content-end gap-2">
            <button id="btn_agregar" class="btn btn-primary" type="submit" style="
              background: var(--bs-info);
              font-weight: bold;
              margin-top: 10px;
              ">Guardar</button>
            <a class="btn btn-secondary" role="button" style="
              background: var(--bs-success);
              font-weight: bold;
              margin-top: 10px;
              " href="../Ofertas/view_ofer_produc.php">Cancelar</a>
          </div>
        </form>
        <!-- termina formulario -->

      </div>
    </div>
    <!-- inicia footer -->
    <?php include dirname(__DIR__, 2) . '/Admin/Menú/footer.php'; ?>
    <!-- termina footer -->
  </div>
  <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
  </div>

  <script>
    const precioInput = document.getElementById('precioNew');
    const errorPrecio = document.getElementById('errorPrecioNew');

    precioInput.addEventListener('input', () => {
      let valor = precioInput.value;

      // Validar que no tenga más de dos decimales
      if (valor.includes('.')) {
        const partes = valor.split('.');
        if (partes[1].length > 2) {
          // Recorta a dos decimales
          precioInput.value = partes[0] + '.' + partes[1].slice(0, 2);
          errorPrecio.textContent = 'Solo se permiten hasta 2 decimales.';
        } else {
          errorPrecio.textContent = '';
        }
      } else {
        errorPrecio.textContent = '';
      }
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

</body>

</html>