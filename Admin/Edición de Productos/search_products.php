<?php
include __DIR__ . '/../../config/database.php';

$search = isset($_POST['search']) ? $_POST['search'] : '';

$sql = "SELECT p.id, p.nombre, p.precio, c.nombre AS categoria, p.descripcion, 
        GROUP_CONCAT(i.ruta_imagen SEPARATOR ',') AS rutas_imagen
        FROM productos p
        LEFT JOIN categorias c ON p.id_categoria = c.id
        LEFT JOIN imagenes_producto i ON p.id = i.id_producto
        WHERE p.nombre LIKE ? OR c.nombre LIKE ?
        GROUP BY p.id";

$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($producto = $result->fetch_assoc()) {
        // Mismo código que en tu tabla original para generar las filas
        // ...
    }
} else {
    echo '<tr><td colspan="6" style="text-align: center;">No se encontraron productos.</td></tr>';
}
?>