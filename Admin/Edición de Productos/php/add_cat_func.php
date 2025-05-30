<?php
//Recibe datos del formulario
include_once realpath(__DIR__ . '/../../../init.php');
$nombre = $_POST['nombre'];

// Verifica si el valor ya existe en la base de datos
$sql_check = "SELECT * FROM categorias WHERE nombre = '$nombre'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Si el valor ya existe, muestra un modal de advertencia
    echo "<!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
        <title>Advertencia</title>
    </head>
    <body>
        <div class='modal fade' id='warningModal' tabindex='-1' aria-labelledby='warningModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='warningModalLabel'>Advertencia</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        El valor ya existe en la base de datos.
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' onclick=\"window.location.href='../../categories.php'\">Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>
        <script>
            var warningModal = new bootstrap.Modal(document.getElementById('warningModal'));
            warningModal.show();
        </script>
    </body>
    </html>";
} else {
    // Inserta los datos en la tabla si no existe
    $sql = "INSERT INTO categorias (nombre) VALUES ('$nombre')";
    if ($conn->query($sql) === TRUE) {
        // Si los datos se insertaron correctamente, muestra un modal de confirmación
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
            <title>Confirmación</title>
        </head>
        <body>
            <div class='modal fade' id='successModal' tabindex='-1' aria-labelledby='successModalLabel' aria-hidden='true'>
                <div class='modal-dialog'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <h5 class='modal-title' id='successModalLabel'>Confirmación</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>
                            Los valores se ingresaron correctamente.
                        </div>
                        <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' onclick=\"window.location.href='../../categories.php'\">Aceptar</button>
                        </div>
                    </div>
                </div>
            </div>
            <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>
            <script>
                var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            </script>
        </body>
        </html>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Cierra la conexión
$conn->close();
?>