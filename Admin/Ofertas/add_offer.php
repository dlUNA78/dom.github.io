<?php
// Nombre del archivo: add_offer.php

session_start();

if (!isset($_SESSION['user'])) {
  header("Location:/Admin/Menú/login.php");
  exit();
}

// Configuración de la conexión mysqli
include __DIR__ . '/../../config/database.php';// Asegúrate que $conn es mysqli

// Lógica AJAX para búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query']) && !isset($_POST['Nombre_b'])) {
  $busqueda = $_POST['query'];

  $stmt = $conn->prepare("
    SELECT p.id AS id_producto, p.nombre, p.precio, p.descripcion
    FROM productos p
    WHERE p.nombre LIKE ?
    GROUP BY p.id
  ");
  $like = "%$busqueda%";
  $stmt->bind_param("s", $like);
  $stmt->execute();
  $result = $stmt->get_result();

  $datos = [];
  while ($row = $result->fetch_assoc()) {
    $productoId = $row['id_producto'];
    $stmtImagenes = $conn->prepare("SELECT id, ruta_imagen FROM imagenes_producto WHERE id_producto = ?");
    $stmtImagenes->bind_param("i", $productoId);
    $stmtImagenes->execute();
    $resultImagenes = $stmtImagenes->get_result();

    $imagenes = [];
    while ($fila = $resultImagenes->fetch_assoc()) {
      // Normalizar las barras para URL
      $fila['ruta_imagen'] = str_replace('\\', '/', $fila['ruta_imagen']);
      $imagenes[] = $fila;
    }
    $row['imagenes'] = $imagenes;

    $datos[] = $row;
    $stmtImagenes->close();
  }
  $stmt->close();

  echo json_encode($datos);
  exit();
}

// Procesar formulario de oferta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Nombre_b'])) {
  $id_producto = $_POST['Nombre_b'];
  $precio_normal = $_POST['precio_normal'];
  $precio_oferta = $_POST['precio_oferta'];
  $fecha_inicio = $_POST['Fecha_inicio'];
  $fecha_expirada = $_POST['Fecha_expirada'];
  $descripcion = $_POST['descripcion'];
  $imagen_seleccionada = $_POST['imagen_seleccionada'] ?? '';

  $errores = [];

  if (empty($id_producto)) {
    $errores[] = "Debe seleccionar un producto.";
  }
  if (!is_numeric($precio_normal) || $precio_normal <= 0) {
    $errores[] = "El precio normal debe ser un número mayor a cero.";
  }
  if (!is_numeric($precio_oferta) || $precio_oferta <= 0 || $precio_oferta >= $precio_normal) {
    $errores[] = "El precio de oferta debe ser un número mayor a cero y menor al precio normal.";
  }
  if (empty($fecha_inicio)) {
    $errores[] = "La fecha de inicio es obligatoria.";
  }
  if (empty($fecha_expirada)) {
    $errores[] = "La fecha de expiración es obligatoria.";
  } elseif (strtotime($fecha_expirada) <= strtotime($fecha_inicio)) {
    $errores[] = "La fecha de expiración debe ser posterior a la fecha de inicio.";
  }
  if (empty($imagen_seleccionada)) {
    $errores[] = "Debe seleccionar una imagen del producto.";
  }

  // Verificar que la imagen seleccionada existe para el producto
  if (empty($errores)) {
    $stmt = $conn->prepare("SELECT ruta_imagen FROM imagenes_producto WHERE id_producto = ? AND ruta_imagen = ?");
    $stmt->bind_param("is", $id_producto, $imagen_seleccionada);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
      $errores[] = "La imagen seleccionada no existe para el producto.";
    } else {
      $imagen_data = $result->fetch_assoc();
      $ruta_imagen_relativa = $imagen_data['ruta_imagen'];
    }
    $stmt->close();
  }

  if (!empty($errores)) {
    $_SESSION['error'] = implode("<br>", $errores);
    header("Location: add_offer.php");
    exit();
  }

  // Ruta absoluta de la imagen original
  $ruta_imagen_original = dirname(__DIR__, 2) . '/Admin/' . ltrim($ruta_imagen_relativa, '/');

  if (!file_exists($ruta_imagen_original)) {
    $_SESSION['error'] = "El archivo de imagen no se encuentra en: $ruta_imagen_original";
    header("Location: add_offer.php");
    exit();
  }

  // Directorio destino para ofertas
  $directorio_ofertas = dirname(__DIR__, 2) . '/Admin/assets/img/ofertas/';
  if (!is_dir($directorio_ofertas)) {
    if (!mkdir($directorio_ofertas, 0755, true)) {
      $_SESSION['error'] = "No se pudo crear el directorio de ofertas.";
      header("Location: add_offer.php");
      exit();
    }
  }

  // Generar nombre único para imagen de oferta
  $extension = pathinfo($ruta_imagen_original, PATHINFO_EXTENSION);
  $nombre_archivo = 'oferta_' . uniqid() . '.' . $extension;
  $ruta_destino = $directorio_ofertas . $nombre_archivo;

  if (!copy($ruta_imagen_original, $ruta_destino)) {
    $_SESSION['error'] = "Error al copiar la imagen a la carpeta de ofertas.";
    header("Location: add_offer.php");
    exit();
  }

  // Ruta relativa para guardar en BD (sin barra inicial para mantener consistencia)
  $ruta_imagen_oferta_db = 'p/Admin/assets/img/ofertas/' . $nombre_archivo;

  // Obtener nombre del producto para nombre de oferta
  $stmt_producto = $conn->prepare("SELECT nombre FROM productos WHERE id = ?");
  $stmt_producto->bind_param("i", $id_producto);
  $stmt_producto->execute();
  $result_producto = $stmt_producto->get_result();
  $producto = $result_producto->fetch_assoc();
  $nombre_producto = $producto['nombre'];
  $stmt_producto->close();

  // Insertar oferta en BD

  // Verificar si ya existe una oferta para este producto (puedes agregar condición para ofertas activas si quieres)
  $stmt_check = $conn->prepare("SELECT COUNT(*) AS total FROM ofertas WHERE id_producto = ?");
  $stmt_check->bind_param("i", $id_producto);
  $stmt_check->execute();
  $result_check = $stmt_check->get_result();
  $row_check = $result_check->fetch_assoc();
  $stmt_check->close();

  if ($row_check['total'] > 0) {
    $_SESSION['error'] = "Ya existe una oferta para este producto. No se puede agregar otra.";
    header("Location: add_offer.php");
    exit();
  }

  $sql = "INSERT INTO ofertas (Nombre_oferta, precio, precio_oferta, Fecha_inicio, Fecha_expirada, imagen, id_producto, descripcion) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sddsssis", $nombre_producto, $precio_normal, $precio_oferta, $fecha_inicio, $fecha_expirada, $ruta_imagen_oferta_db, $id_producto, $descripcion);

  if ($stmt->execute()) {
    $_SESSION['success'] = "¡Oferta agregada correctamente!";
  } else {
    $_SESSION['error'] = "Error al agregar la oferta: " . $stmt->error;
  }

  $stmt->close();
  $conn->close();

  header("Location: view_ofer_produc.php");
  exit();
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
  <style>
    .imagen-opcion {
      width: 100px;
      height: 100px;
      object-fit: cover;
      margin: 5px;
      cursor: pointer;
      border: 2px solid transparent;
    }

    .imagen-opcion:hover {
      border-color: #0d6efd;
    }

    .imagen-seleccionada {
      border-color: #0d6efd !important;
      box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
    }

    #contenedor-imagenes {
      display: none;
      margin-top: 10px;
    }

    #imagen-preview {
      max-width: 200px;
      max-height: 200px;
      display: none;
      margin-top: 10px;
      border: 1px solid #ddd;
      padding: 5px;
      border-radius: 4px;
    }
  </style>
</head>

<body>
  <!-- Inicia Modal -->
  <div class="modal fade" role="dialog" tabindex="-1" id="Agregado">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="color: rgb(0, 0, 0)">
            Agregado Correctamente
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
  <!-- Termina Modal -->

  <div id="wrapper">
    <!-- inicia menu -->
    <?php include dirname(__DIR__, 2) . '/Admin/Menú/menu.php'; ?>
    <!-- termina menu -->

    <div class="d-flex justify-content-center align-items-center" id="content">
      <div class="container d-flex flex-row justify-content-center" style="
                    margin-left: 0px;
                    margin-right: 0px;
                    height: auto;
                    width: 500px;
                    margin-top: 0px;
                    margin-bottom: 40px;
                ">
        <div class="card shadow-sm p-4">
          <h2 class="text-center mb-4" style="color: rgb(0, 0, 0); font-weight: bold">
            Agregar Oferta
          </h2>

          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php echo $_SESSION['error'];
              unset($_SESSION['error']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <form id="form_oferta" method="POST" action="add_offer.php" enctype="multipart/form-data" novalidate
            class="container" style="max-width: 900px;">
            <!-- Campo oculto para el ID del producto -->
            <input type="hidden" id="id_producto" name="Nombre_b" value="">

            <!-- Buscador de producto -->
            <div class="mb-3 position-relative">
              <label class="form-label" for="search" style="color: rgb(0, 0, 0)">Nombre del Producto:</label>
              <input class="form-control" type="text" id="search" autocomplete="off" required />
              <div id="sugerencias" class="list-group position-absolute w-100" style="z-index: 1000; display: none;">
              </div>
              <div id="errorNombre" class="text-danger"></div>
            </div>

            <!-- Precio normal (auto) -->
            <div class="mb-3">
              <label class="form-label" style="color: rgb(0, 0, 0)">Precio Normal:</label>
              <input id="precio" class="form-control" type="text" readonly name="precio_normal" />
              <div id="errorsProduct" class="text-danger"></div>
            </div>

            <!-- Precio con descuento -->
            <div class="mb-3">
              <label class="form-label" style="color: rgb(0, 0, 0)">Precio con Descuento:</label>
              <input id="descuento" name="precio_oferta" class="form-control" type="number" step="0.01" required />
              <div id="errorDescuento" class="text-danger"></div>
            </div>

            <!-- Fecha inicio -->
            <div class="mb-3">
              <label style="color: rgb(0, 0, 0)">Fecha de inicio:</label>
              <input type="date" name="Fecha_inicio" class="form-control" value="<?php echo date('Y-m-d'); ?>"
                required />
              <div id="errorFechaInicio" class="text-danger"></div>
            </div>

            <!-- Fecha expiración -->
            <div class="mb-3">
              <label style="color: rgb(0, 0, 0)">Fecha de expiración:</label>
              <input type="date" name="Fecha_expirada" class="form-control" required />
              <div id="errorFechaExpiracion" class="text-danger"></div>
            </div>
            <!-- Selección de imagen -->
            <div class="mb-3">
              <label style="color: rgb(0, 0, 0)">Imagen del Producto, selecciona alguna:</label>
              <div id="contenedor-imagenes" class="d-flex flex-wrap"></div>
              <input type="hidden" id="imagen_seleccionada" name="imagen_seleccionada" value="">
            </div>

            <!-- Descripción -->
            <div class="mb-3">
              <label>Descripción:</label>
              <textarea id="descripcion" name="descripcion" class="form-control" rows="3" readonly></textarea>
            </div>

            <!-- Botones -->
            <div class="d-flex justify-content-end gap-2">
              <button type="submit" class="btn btn-primary"
                style="background: var(--bs-info);font-weight: bold; margin-top: 10px;" id="btn_agregar">Agregar
                Oferta</button>
              <a class="btn btn-secondary" role="button"
                style="background: var(--bs-success); font-weight: bold; margin-top: 10px;"
                href="../Ofertas/view_ofer_produc.php">Cancelar</a>
            </div>
          </form>
          <div id="message"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- inicia footer -->
  <?php include dirname(__DIR__, 2) . '/Admin/Menú/footer.php'; ?>
  <!-- termina footer -->

  <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#search').on('input', function () {
        let query = $(this).val();

        if (query.length >= 2) {
          $.ajax({
            url: 'add_offer.php',
            method: 'POST',
            data: { query: query },
            dataType: 'json',
            success: function (data) {
              $('#sugerencias').empty().show();

              data.forEach(producto => {
                const sugerencia = $('<a href="#" class="list-group-item list-group-item-action"></a>').text(producto.nombre);

                sugerencia.on('click', function (e) {
                  e.preventDefault();
                  $('#search').val(producto.nombre);
                  $('#id_producto').val(producto.id_producto);
                  $('#precio').val(producto.precio);
                  $('#descripcion').val(producto.descripcion);
                  $('#sugerencias').hide();

                  // Mostrar imágenes
                  const contenedor = $('#contenedor-imagenes');
                  contenedor.empty().show();

                  producto.imagenes.forEach(imagen => {
                    // Construir
                    //  ruta con '/Admin/' para mostrar en src
                    let ruta = '/p/Admin/' + imagen.ruta_imagen.replace(/\\/g, '/').replace(/^.*?(assets\/img\/productos\/)/, '$1');

                    // Eliminar '/Admin/' para enviar y usar internamente sin problema
                    let rutaSinAdmin = ruta.startsWith('/p/Admin/') ? ruta.substring('/p/Admin/'.length) : ruta;

                    const img = $('<img>')
                      .addClass('imagen-opcion')
                      .attr('src', ruta) // Mostrar la imagen con /Admin/ en la URL para que cargue bien
                      .attr('alt', 'Imagen producto')
                      .data('ruta', rutaSinAdmin) // Guardar sin /Admin/ para enviar
                      .on('click', function () {
                        $('.imagen-opcion').removeClass('imagen-seleccionada');
                        $(this).addClass('imagen-seleccionada');
                        $('#imagen_seleccionada').val($(this).data('ruta')); // Aquí sin /Admin/
                        $('#imagen-preview').attr('src', ruta).show(); // Mostrar preview con /Admin/
                      });

                    contenedor.append(img);
                  });

                  if (producto.imagenes.length > 0) {
                    contenedor.show();
                    $('#imagen-preview').hide();
                    $('#imagen_seleccionada').val('');
                  } else {
                    contenedor.html('<p>No hay imágenes para este producto.</p>');
                    $('#imagen-preview').hide();
                    $('#imagen_seleccionada').val('');
                  }
                });

                $('#sugerencias').append(sugerencia);
              });
            }
          });
        } else {
          $('#sugerencias').hide();
        }
      });

      // Ocultar sugerencias al hacer clic fuera
      $(document).on('click', function (e) {
        if (!$(e.target).closest('#search, #sugerencias').length) {
          $('#sugerencias').hide();
        }
      });

      // Seleccionar un producto de las sugerencias
      $(document).on("click", ".sugerencia-item", function () {
        let id = $(this).data("id");
        let nombre = $(this).data("nombre");
        let precio = $(this).data("precio");
        let descripcion = $(this).data("descripcion");

        $("#search").val(nombre);
        $("#id_producto").val(id);
        $("#precio").val(precio);
        $("#descripcion").val(descripcion);
        $("#sugerencias").hide();

        $.ajax({
          url: "add_offer.php",
          method: "POST",
          data: { query: nombre },
          success: function (data) {
            try {
              let productos = JSON.parse(data);
              let productoSeleccionado = productos.find(p => p.id_producto == id);

              if (productoSeleccionado && productoSeleccionado.imagenes && productoSeleccionado.imagenes.length > 0) {
                let contenedorImagenes = $("#contenedor-imagenes");
                contenedorImagenes.empty();

                productoSeleccionado.imagenes.forEach(function (imagen) {
                  let rutaImagen = '/' + imagen.ruta_imagen.replace(/\\/g, '/');
                  contenedorImagenes.append(`
              <img src="${rutaImagen}" class="imagen-opcion" 
                   data-ruta="${rutaImagen}" 
                   alt="Imagen del producto">
            `);
                });

                contenedorImagenes.show();
              } else {
                $("#contenedor-imagenes").hide().empty();
                $("#imagen-preview").hide().attr('src', '');
                $("#imagen_seleccionada").val('');
              }
            } catch (e) {
              console.error("Error parsing JSON:", e);
            }
          },
          error: function (xhr, status, error) {
            console.error("Error AJAX:", error);
          }
        });
      });

      // Seleccionar una imagen para la oferta
      $(document).on("click", ".imagen-opcion", function () {
        $(".imagen-opcion").removeClass("imagen-seleccionada");
        $(this).addClass("imagen-seleccionada");

        let rutaImagen = $(this).data("ruta");
        $("#imagen-preview").attr("src", rutaImagen).show();
        $("#imagen_seleccionada").val(rutaImagen);
        $("#errorImagen").text("");
      });

      // Validación del formulario
      $("#form_oferta").on("submit", function (e) {
        let isValid = true;

        const idProducto = $("#id_producto").val().trim();
        if (idProducto === "") {
          $("#errorNombre").text("Debe seleccionar un producto de la lista.");
          isValid = false;
        } else {
          $("#errorNombre").text("");
        }

        const precioNormal = $("#precio").val().trim();
        if (precioNormal === "" || isNaN(precioNormal)) {
          $("#errorsProduct").text("El precio normal no es válido.");
          isValid = false;
        } else if (parseFloat(precioNormal) <= 0) {
          $("#errorsProduct").text("El precio normal debe ser mayor a 0.");
          isValid = false;
        } else {
          $("#errorsProduct").text("");
        }

        const precioDescuento = $("#descuento").val().trim();
        if (precioDescuento === "" || isNaN(precioDescuento)) {
          $("#errorDescuento").text("El precio con descuento no es válido.");
          isValid = false;
        } else if (parseFloat(precioDescuento) <= 0) {
          $("#errorDescuento").text("El precio con descuento debe ser mayor a 0.");
          isValid = false;
        } else if (parseFloat(precioDescuento) >= parseFloat(precioNormal)) {
          $("#errorDescuento").text("El precio con descuento debe ser menor que el precio normal.");
          isValid = false;
        } else {
          $("#errorDescuento").text("");
        }

        const fechaInicio = $("input[name='Fecha_inicio']").val().trim();
        if (fechaInicio === "") {
          $("#errorFechaInicio").text("La fecha de inicio es obligatoria.");
          isValid = false;
        } else {
          $("#errorFechaInicio").text("");
        }

        const fechaExpiracion = $("input[name='Fecha_expirada']").val().trim();
        if (fechaExpiracion === "") {
          $("#errorFechaExpiracion").text("La fecha de expiración es obligatoria.");
          isValid = false;
        } else if (fechaInicio !== "" && new Date(fechaExpiracion) <= new Date(fechaInicio)) {
          $("#errorFechaExpiracion").text("La fecha de expiración debe ser posterior a la fecha de inicio.");
          isValid = false;
        } else {
          $("#errorFechaExpiracion").text("");
        }

        const imagenSeleccionada = $("#imagen_seleccionada").val().trim();
        if (imagenSeleccionada === "") {
          $("#errorImagen").text("Debe seleccionar una imagen para la oferta.");
          isValid = false;
        } else {
          $("#errorImagen").text("");
        }

        if (!isValid) {
          e.preventDefault();
          $('html, body').animate({
            scrollTop: $(".text-danger:visible:first").offset().top - 100
          }, 500);
        }
      });

      let ultimoDescuentoValido = "";

      $("#descuento").on("input", function () {
        const precioDescuento = $(this).val().trim();
        const precioNormal = $("#precio").val().trim();

        if (precioDescuento === "" || isNaN(precioDescuento)) {
          $("#errorDescuento").text("El precio con descuento no es válido.");
          $("#btnGuardar").prop("disabled", true);
          ultimoDescuentoValido = "";
        } else if (parseFloat(precioDescuento) <= 0) {
          $("#errorDescuento").text("El precio con descuento debe ser mayor a 0.");
          $("#btnGuardar").prop("disabled", true);
          ultimoDescuentoValido = "";
        } else if (
          precioNormal !== "" &&
          !isNaN(precioNormal) &&
          parseFloat(precioDescuento) >= parseFloat(precioNormal)
        ) {
          $("#errorDescuento").text("El precio con descuento debe ser menor que el precio normal.");
          $(this).val(ultimoDescuentoValido); // Restaurar último valor válido
          $("#btnGuardar").prop("disabled", true);
        } else {
          $("#errorDescuento").text("");
          $("#btnGuardar").prop("disabled", false);
          ultimoDescuentoValido = precioDescuento; // Guardar como último valor válido
        }
      });



      $("#descuento").on("keypress", function (e) {
        const charCode = e.which ? e.which : e.keyCode;
        const inputValue = $(this).val();
        const decimalIndex = inputValue.indexOf(".");

        if ((charCode < 48 || charCode > 57) && charCode !== 46) {
          e.preventDefault();
        } else if (charCode === 46 && decimalIndex !== -1) {
          e.preventDefault();
        } else if (decimalIndex !== -1 && inputValue.length - decimalIndex > 2) {
          e.preventDefault();
        }
      });
    });
  </script>

  <script>
    const inputNombre = document.getElementById('search');

    inputNombre.addEventListener('input', () => {
      // Filtrar valor para que solo queden letras y espacios
      inputNombre.value = inputNombre.value.replace(/[^a-zA-Z\s]/g, '');
    });
  </script>

</body>

</html>