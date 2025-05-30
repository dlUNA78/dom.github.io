<?php
include __DIR__ . '/../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_oferta'])) {
    $id_oferta = intval($_POST['id_oferta']);

    // Obtener el nombre de la imagen antes de eliminar la oferta
    $stmt = $conn->prepare("SELECT imagen FROM ofertas WHERE id_oferta = ?");
    $stmt->bind_param("i", $id_oferta);
    $stmt->execute();
    $stmt->bind_result($imagen);
    $stmt->fetch();
    $stmt->close();

    // Eliminar la oferta
    $stmt = $conn->prepare("DELETE FROM ofertas WHERE id_oferta = ?");
    $stmt->bind_param("i", $id_oferta);

    if ($stmt->execute()) {
        // Eliminar la imagen si existe
        if (!empty($imagen)) {
            $ruta_imagen = __DIR__ . "/../admin/assets/img/ofertas/" . $imagen;
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }
        header("Location: ../Ofertas/view_ofer_produc.php?success=1");
    } else {
        header("Location: ../Ofertas/view_ofer_produc.php?error=1");
    }

    $stmt->close();
    $conn->close();
    exit;
}
?>