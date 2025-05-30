<?php
// Configuración de la conexión PDO
include __DIR__ . '/../../config/database.php';

session_start();

if (!isset($_SESSION['user'])) {
    header("Location:/Admin/Menú/login.php");
    die();
}

try {
  // Crear conexión PDO
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y limpiar datos
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = strtolower(trim($_POST['usuario'] ?? ''));
    $rol = $_POST['rol'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';
    $confirmacion = $_POST['contraseñaConf'] ?? '';

    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);

    if ($stmt->rowCount() > 0) {
      header("Location: add_user.php?usuario=$usuario&error=" . urlencode("El usuario ya existe"));
      exit();
    }

    // Validaciones
    $errores = [];
    if (empty($nombre)) $errores[] = "El nombre es obligatorio";
    if (empty($usuario)) $errores[] = "El usuario es obligatorio";
    if (empty($rol)) $errores[] = "Debe seleccionar un rol";
    if (empty($contraseña)) $errores[] = "La contraseña es obligatoria";
    if ($contraseña !== $confirmacion) $errores[] = "Las contraseñas no coinciden";

    // Validación de imagen
    $imagen_nombre = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
      $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
      $tamaño_max = 2 * 1024 * 1024; // 2 MB

      $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
      $tamaño_archivo = $_FILES['imagen']['size'];

      if (!in_array($extension, $permitidos)) {
        $errores[] = "Formato de imagen no permitido.";
      } elseif ($tamaño_archivo > $tamaño_max) {
        $errores[] = "La imagen excede el tamaño máximo permitido (2MB).";
      }
    }

    if (!empty($errores)) {
      header("Location: add_user.php?error=" . urlencode(implode(", ", $errores)));
      exit();
    }

    // Procesar y mover imagen
    if (!empty($_FILES['imagen']['tmp_name'])) {
      $directorio = dirname(__DIR__,2).'/Admin/assets/img/avatars/';
      if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
      }
      $imagen_nombre = uniqid('img_') . '.' . $extension;
      move_uploaded_file($_FILES['imagen']['tmp_name'], $directorio . $imagen_nombre);
    }

    // Hash de contraseña
    $hashed_password = password_hash($contraseña, PASSWORD_BCRYPT);

    // Insertar en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre, usuario, contraseña, rol, imagen) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $usuario, $hashed_password, $rol, $imagen_nombre]);

    if ($stmt && $stmt->rowCount() > 0) {
      header("Location: ../Menú/user.php?success=Usuario agregado correctamente");
      exit();
    } else {
      $error = $stmt ? $stmt->errorInfo()[2] : "Error al ejecutar la consulta";
      header("Location: add_user.php?error=" . urlencode($error));
      exit();
    }
  }
} catch (PDOException $e) {
  die("Error en la conexión o consulta: " . $e->getMessage());
}
?>



<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>

  <script src="..\JS\validar_user.js" defer></script>

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
      <div class="container d-flex justify-content-center align-items-center"
        style="max-width: 500px; margin: 40px auto">
        <div class="card shadow-sm p-4 w-100">
          <h2 class="text-center mb-4" style="color: #000; font-weight: bold">
            Agregar Nuevo Usuario
          </h2>
          <!-- Inicio del formulario -->
          <form method="POST" action="add_user.php" enctype="multipart/form-data" id="formulario">
            <!-- Campo Nombre -->
            <div class="mb-3">
              <label class="form-label" for="nombre" style="color: #000">Nombre:</label>
              <input class="form-control" type="text" id="nombre" name="nombre" required
                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '')"
                placeholder="Ingrese el nombre completo">
              <div id="errorNombre" class="text-danger small mt-1"></div>
            </div>

            <!-- Campo Usuario/Email -->
            <div class="mb-3">
              <label class="form-label" for="usuario" style="color: #000">Usuario:</label>
              <input class="form-control" type="text" required id="usuario" name="usuario" placeholder="usuario123"
                value="<?php echo isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : ''; ?>">

              <div id="errorUsuario" class="text-danger small mt-1">
                <?php
                if (isset($_GET['error']) && strpos($_GET['error'], 'usuario ya existe') !== false) {
                  echo htmlspecialchars($_GET['error']);
                }
                ?>
              </div>
            </div>
            <!-- Campo rol -->
            <div class="mb-3">
              <label class="form-label" for="rol" style="color: #000">Rol:</label>
              <select class="form-control" id="rol" name="rol" required>
                <option value="">Seleccione un rol</option>
                <option value="admin">Administrador</option>
                <option value="user">Usuario</option>
              </select>
              <div id="errorRol" class="text-danger small mt-1"></div>
            </div>

            <!-- Campo Contraseña -->
            <div class="mb-3 position-relative">
              <label class="form-label" for="contraseña" style="color: #000">Contraseña:</label>
              <input class="form-control" type="password" required id="contraseña" name="contraseña"
                placeholder="Mínimo 8 caracteres">
              <div id="errorContraseña" class="text-danger small mt-1"></div>
            </div>

            <!-- Campo Confirmar Contraseña -->
            <div class="mb-3 position-relative">
              <label class="form-label" for="contraseñaConf" style="color: #000">Confirmar Contraseña:</label>
              <input class="form-control" type="password" required id="contraseñaConf" name="contraseñaConf"
                placeholder="Repita la contraseña">
              <div id="errorContraseñaConf" class="text-danger small mt-1"></div>
            </div>
            <!-- Checkbox Mostrar Contraseñas -->
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="showPasswords">
              <label class="form-check-label" for="showPasswords" style="color: #000">Mostrar Contraseñas</label>
            </div>
            <!-- Campo Imagen -->
            <div class="mb-3">
              <label class="form-label" for="imagen" style="color: #000">Imagen de Perfil:</label>
              <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*">
              <div class="form-text">Formatos aceptados: JPG, PNG, WEBP (Max. 2MB)</div>
              <div id="errorImagen" class="text-danger small mt-1"></div>
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-end gap-2 mt-4">
              <button type="submit" class="btn btn-primary" id="agregar" href="../Menú/user.php" style="
              background: var(--bs-info);
              font-weight: bold;
              margin-top: 10px;">
                Agregar Usuario
              </button>
              <a class="btn btn-secondary" href="../Menú/user.php" style="
              background: var(--bs-success);
              font-weight: bold;
              margin-top: 10px;">Cancelar</a>
            </div>
          </form>
          <!-- Fin del formulario -->

        </div>
      </div>
      <script src="..\JS\validar_user.js" defer></script>
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


</body>

</html>