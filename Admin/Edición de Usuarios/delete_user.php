<?php
include __DIR__ . '/../../config/database.php';
session_start();

// Tomar el valor del campo 'usuario' del formulario
$usuario = $_POST['usuario'];

// Primero consultamos la imagen asociada al usuario
$stmt = $conn->prepare("SELECT imagen FROM Usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->bind_result($imagen);
$stmt->fetch();
$stmt->close();

// Si existe una imagen, la eliminamos
if (!empty($imagen)) {
    $ruta_imagen = __DIR__ . '/../../Admin/assets/img/avatars/' . $imagen;
    if (file_exists($ruta_imagen)) {
        unlink($ruta_imagen); // Elimina el archivo físico
    }
}

// Ahora eliminamos el usuario
$stmt = $conn->prepare("DELETE FROM Usuarios WHERE usuario = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$stmt->close();
$conn->close();

// Redirigir con un parámetro indicando éxito
header("Location: ../Menú/user.php?mensaje=Usuario eliminado correctamente");
exit();
?>