<?php
// Recibe el ID de la categoría a eliminar
$id = $_POST['id'];

// Conexión a la base de datos
include dirname(__DIR__, 3) . '/config/database.php';

// Verificar si hay productos que usan esta categoría
$query = "SELECT COUNT(*) AS total FROM productos WHERE id_categoria = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

// Si hay productos asociados
if ($result['total'] > 0 && !isset($_POST['nueva_categoria'])) {
    // Obtener otras categorías para mostrar como opciones de reasignación
    $categorias = $conn->query("SELECT id, nombre FROM categorias WHERE id != $id");

    echo "<div id='modal' style='display: block; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;'>";
    echo "<div style='font-family: Arial, sans-serif; max-width: 500px; margin: 100px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #fff;'>";
    echo "<h3 style='color: #333;'>No se puede eliminar la categoría porque tiene productos asociados.</h3>";
    echo "<form method='post' action='eliminar_cat.php' style='margin-top: 15px;'>";
    echo "<input type='hidden' name='id' value='$id'>";
    echo "<label style='display: block; margin-bottom: 10px; font-weight: bold;'>Reasignar productos a otra categoría:</label>";
    echo "<select name='nueva_categoria' required style='width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;'>";
    while ($row = $categorias->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['nombre']}</option>";
    }
    echo "</select>";
    echo "<button type='submit' style='background-color: #007bff; color: #fff; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Reasignar y eliminar</button>";
    echo "</form>";
    echo "<button onclick='window.location.href=\"../../categories.php\"' style='margin-top: 10px; background-color: #dc3545; color: #fff; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>Cancelar</button>";
    echo "</div>";
    echo "</div>";
    echo "<script>
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
        }
    </script>";
} elseif (isset($_POST['nueva_categoria'])) {
    // Reasignar productos a la nueva categoría seleccionada
    $nueva_cat = $_POST['nueva_categoria'];

    $update = $conn->prepare("UPDATE productos SET id_categoria = ? WHERE id_categoria = ?");
    $update->bind_param("ii", $nueva_cat, $id);
    $update->execute();

    // Luego eliminar la categoría
    $delete = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        echo "<script>
            alert('Productos reasignados y categoría eliminada exitosamente.');
            window.location.href = '../../categories.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al eliminar la categoría: " . $conn->error . "');
            window.location.href = '../../categories.php';
        </script>";
    }
} else {
    // Si no hay productos, se elimina directamente
    $delete = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        echo "<script>
            alert('Categoría eliminada correctamente.');
            window.location.href = '../../categories.php';
        </script>";
    } else {
        echo "<script>
            alert('Error al eliminar la categoría: " . $conn->error . "');
            window.location.href = '../../categories.php';
        </script>";
    }
}

$conn->close();
