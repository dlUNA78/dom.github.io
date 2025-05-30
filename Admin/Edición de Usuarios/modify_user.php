<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location:/Admin/Menú/login.php");
    die();
}

// Configuración de la conexión PDO
include __DIR__ . '/../../config/database.php';

try {
    // Crear conexión PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $usuario = $_GET['usuario'] ?? '';
    $datos = null;

    // Seleccionar el usuario a modificar
    if ($usuario) {
        $stmt = $conn->prepare("SELECT usuario, nombre, contraseña, imagen, rol FROM Usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);

        // Construir la ruta completa de la imagen si existe
        if ($datos && !empty($datos['imagen'])) {
            $datos['imagen_completa'] = "../assets/img/avatars/" . $datos['imagen'];
        }
    }

    // Verificar si el usuario existe
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = $_POST['usuario'];
        $nombre = $_POST['nombre'];
        $contraseñaActual = $_POST['contraseñaAct'];
        $nuevaContraseña = $_POST['contraseña'];
        $rol = $_POST['rol'] ?? '';

        // Verificar contraseña actual
        $stmt = $conn->prepare("SELECT contraseña FROM Usuarios WHERE usuario = ?");
        $stmt->execute([$usuario]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE usuario = ?");
        $stmt->execute([$rol, $usuario]);

        // Verificar si la contraseña actual es correcta
        if (!$userData || !password_verify($contraseñaActual, $userData['contraseña'])) {
            header("Location: modify_user.php?usuario=$usuario&error=La contraseña actual es incorrecta");
            exit();
        }

        // Hash de la nueva contraseña si se proporciona
        if (!empty($nuevaContraseña)) {
            $nuevaContraseña = password_hash($nuevaContraseña, PASSWORD_DEFAULT);
        }

        // Manejo de imagen
        $imagen = $datos['imagen'] ?? '';

        if (!empty($_FILES['imagen']['name'])) {
            $targetDir = "../assets/img/avatars/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileExt = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExt;
            $targetFilePath = $targetDir . $newFileName;

            $allowedTypes = ['jpg', 'png', 'jpeg', 'webp'];

            if (in_array(strtolower($fileExt), $allowedTypes) && $_FILES['imagen']['size'] < 2097152) {
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFilePath)) {
                    // Eliminar la imagen anterior si existe
                    if (!empty($datos['imagen']) && file_exists($targetDir . $datos['imagen'])) {
                        unlink($targetDir . $datos['imagen']);
                    }
                    $imagen = $newFileName;
                }
            }
        }

        // Actualizar usuario
        if (!empty($nuevaContraseña)) {
            $stmt = $conn->prepare("UPDATE Usuarios SET nombre = ?, contraseña = ?, imagen = ? WHERE usuario = ?");
            $stmt->execute([$nombre, $nuevaContraseña, $imagen, $usuario]);
        } else {
            $stmt = $conn->prepare("UPDATE Usuarios SET nombre = ?, imagen = ? WHERE usuario = ?");
            $stmt->execute([$nombre, $imagen, $usuario]);
        }
        // Redirigir con un mensaje de éxito
        header("Location: ../Menú/user.php?success=Usuario modificado correctamente");
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datos = <?php echo json_encode($datos); ?>;
            if (datos) {
                document.getElementById('usuario').value = datos.usuario;
                document.getElementById('nombre').value = datos.nombre;

                // Precargar la imagen si existe
                if (datos.imagen_completa) {
                    const imgPreview = document.getElementById('imagen-preview');
                    imgPreview.src = datos.imagen_completa;
                    imgPreview.style.display = 'block';
                }
            }
        });

        function togglePasswords() {
            const show = document.getElementById('showPasswords').checked;
            const type = show ? 'text' : 'password';
            document.getElementById('contraseñaAct').type = type;
            document.getElementById('contraseña').type = type;
            document.getElementById('contraseñaConf').type = type;
        }

        function previewImage(input) {
            const preview = document.getElementById('imagen-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <!-- Modal -->
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

        <div id="content">
            <div class="container d-flex justify-content-center align-items-center"
                style="width: 500px; height: auto; margin-bottom: 40px">
                <div class="card shadow-sm p-4">
                    <h2 class="text-center mb-4" style="color: rgb(0, 0, 0); font-weight: bold">
                        Modificar Usuario
                    </h2>
                    <form id="formularioModificacion" method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label" for="nombre" style="color: rgb(0, 0, 0)">Nombre:</label>
                            <input class="form-control" type="text" id="nombre" name="nombre"
                                oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s]/g, '')"
                                value="<?php echo htmlspecialchars($datos['nombre'] ?? ''); ?>" required />
                            <div id="errorNombre" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="usuario" style="color: rgb(0, 0, 0)">Usuario:</label>
                            <input class="form-control" type="text" id="usuario" name="usuario"
                                value="<?php echo htmlspecialchars($datos['usuario'] ?? ''); ?>" readonly />
                            <div id="errorUsuario" class="text-danger"></div>
                        </div>
                        <!-- Campo rol -->

                        <div class="mb-3">
                            <label class="form-label" for="rol" style="color: #000">Rol:</label>
                            <select class="form-control" id="rol" name="rol" required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin" <?php echo (isset($datos['rol']) && $datos['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                                <option value="user" <?php echo (isset($datos['rol']) && $datos['rol'] === 'user') ? 'selected' : ''; ?>>Usuario</option>
                            </select>
                        </div>


                        <div class="mb-3">
                            <label class="form-label" for="contraseñaAct" style="color: rgb(0, 0, 0)">
                                Contraseña Actual:</label>
                            <input class="form-control" type="password" id="contraseñaAct" name="contraseñaAct" required
                                placeholder="Ingresa la contraseña actual" />
                            <div id="errorContraseñaAct" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contraseña" style="color: rgb(0, 0, 0)">
                                Nueva Contraseña:</label>
                            <input class="form-control" type="password" id="contraseña" name="contraseña"
                                placeholder="Dejar en blanco para mantener la actual" />
                            <div id="errorContraseña" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="contraseñaConf" style="color: rgb(0, 0, 0)">
                                Confirmar Nueva Contraseña:</label>
                            <input class="form-control" type="password" id="contraseñaConf" name="contraseñaConf"
                                placeholder="Confirma la contraseña" />
                            <div id="errorContraseñaConf" class="text-danger"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="imagen" style="color: rgb(0, 0, 0)">
                                Imagen de perfil:</label>
                            <!-- Elemento para mostrar la imagen precargada -->
                            <img id="imagen-preview" src="#" alt="Previsualización de imagen" style="max-width: 100px; max-height: 100px; display: none; margin-bottom: 10px;" class="img-thumbnail" />
                            <input class="form-control" type="file" id="imagen" name="imagen" accept="image/*" onchange="previewImage(this)" />
                            <div id="errorImagen" class="text-danger"></div>
                            <small class="text-muted">Formatos permitidos: JPG, PNG, WEBP. Máximo 2MB.</small>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="showPasswords"
                                onclick="togglePasswords()" />
                            <label class="form-check-label" for="showPasswords" style="color: rgb(0, 0, 0)">
                                Mostrar Contraseñas</label>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button class="btn btn-primary" type="submit" style="
                      background: var(--bs-info);
                      font-weight: bold;
                      margin-top: 10px;
                    ">
                                Modificar</button>
                            <a class="btn btn-secondary" role="button" style="
                      background: var(--bs-success);
                      font-weight: bold;
                      margin-top: 10px;
                    " href="../Menú/user.php">Cancelar</a>
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
    <script src="../JS/validar_mod_user.js"></script>
</body>

</html>