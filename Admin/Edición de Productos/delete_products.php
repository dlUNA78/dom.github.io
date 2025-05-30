<?php
include __DIR__ . '/../../config/database.php';

// Comprobamos si se ha enviado el ID del producto a eliminar
if (isset($_POST['producto'])) {
    $productoId = (int)$_POST['producto']; // Forzamos tipo entero para seguridad

    // Iniciamos transacción para operaciones atómicas
    $conn->begin_transaction();

    try {
        // 1. Primero obtenemos las rutas de las imágenes asociadas al producto
        $sqlImagenes = "SELECT ruta_imagen FROM imagenes_producto WHERE id_producto = ?";
        $stmtImagenes = $conn->prepare($sqlImagenes);
        
        if (!$stmtImagenes) {
            throw new Exception("Error al preparar consulta de imágenes: " . $conn->error);
        }
        
        $stmtImagenes->bind_param("i", $productoId);
        
        if (!$stmtImagenes->execute()) {
            throw new Exception("Error al ejecutar consulta de imágenes: " . $stmtImagenes->error);
        }
        
        $resultImagenes = $stmtImagenes->get_result();
        
        // 2. Eliminamos las imágenes del servidor
        $basePath = realpath('../../Admin/assets/img/productos/'); // Ruta base segura
        
        while ($imagen = $resultImagenes->fetch_assoc()) {
            $rutaRelativa = $imagen['ruta_imagen'];
            
            // Extraemos solo el nombre del archivo por seguridad
            $nombreArchivo = basename($rutaRelativa);
            $rutaCompleta = $basePath . DIRECTORY_SEPARATOR . $nombreArchivo;
            
            // Verificación adicional de seguridad
            if (strpos(realpath($rutaCompleta), $basePath) === 0 && file_exists($rutaCompleta)) {
                if (!unlink($rutaCompleta)) {
                    error_log("No se pudo eliminar la imagen: " . $rutaCompleta);
                }
            } else {
                error_log("Ruta de imagen no válida o no existe: " . $rutaCompleta);
            }
        }

        // 3. Eliminamos los registros de imágenes de la base de datos
        $sqlEliminarImagenes = "DELETE FROM imagenes_producto WHERE id_producto = ?";
        $stmtEliminarImagenes = $conn->prepare($sqlEliminarImagenes);
        
        if (!$stmtEliminarImagenes) {
            throw new Exception("Error al preparar eliminación de imágenes: " . $conn->error);
        }
        
        $stmtEliminarImagenes->bind_param("i", $productoId);
        
        if (!$stmtEliminarImagenes->execute()) {
            throw new Exception("Error al eliminar imágenes: " . $stmtEliminarImagenes->error);
        }

        // 4. Finalmente eliminamos el producto
        $sqlEliminarProducto = "DELETE FROM productos WHERE id = ?";
        $stmtEliminarProducto = $conn->prepare($sqlEliminarProducto);
        
        if (!$stmtEliminarProducto) {
            throw new Exception("Error al preparar eliminación de producto: " . $conn->error);
        }
        
        $stmtEliminarProducto->bind_param("i", $productoId);
        
        if (!$stmtEliminarProducto->execute()) {
            throw new Exception("Error al eliminar producto: " . $stmtEliminarProducto->error);
        }

        // Confirmamos la transacción si todo fue bien
        $conn->commit();
        
        // Redirección con éxito
        header("Location: ../Menú/products.php?eliminado=exito");
        exit();
        
    } catch (Exception $e) {
        // Si hay algún error, hacemos rollback
        $conn->rollback();
        error_log("Error al eliminar producto: " . $e->getMessage());
        
        // Redirección con error
        header("Location: ../Menú/products.php?eliminado=error&mensaje=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Si no se ha enviado el ID, redirigimos con mensaje de error
    header("Location: ../Menú/products.php?eliminado=falta-id");
    exit();
}
?>