document.addEventListener('DOMContentLoaded', function () {
    // Definir patrones regex
    const REGEX_NOMBRE = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/; // Solo letras y espacios
    const REGEX_DESCRIPCION = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.,\-/()]+$/; // Solo permite .,-/ ()
    const REGEX_PRECIO = /^\d+(\.\d{1,2})?$/;
    const REGEX_IMAGEN = /\.(jpe?g|png|gif|webp)$/i;
    const SIMBOLOS_NO_PERMITIDOS_DESCRIPCION = /[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\.,\-/()]/;

    // Elementos del formulario
    const form = document.getElementById('formModificarProducto');
    const nombreInput = document.getElementById('nombre');
    const descripcionInput = document.getElementById('descripcion');
    const precioInput = document.getElementById('precio');
    const categoriaInput = document.getElementById('categoria');
    const imagenesInput = document.querySelector('input[type="file"]');

    // Validación en tiempo real para nombre (solo letras)
    if (nombreInput) {
        nombreInput.addEventListener('input', function (e) {
            const input = e.target.value;
            const errorElement = document.getElementById('errorNombre');

            // Eliminar cualquier carácter que no sea letra o espacio
            e.target.value = input.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');

            if (input !== e.target.value) {
                errorElement.innerText = "Solo se permiten letras y espacios";
            } else {
                errorElement.innerText = "";
            }

            // Validar longitud
            if (e.target.value.length > 50) {
                errorElement.innerText = "Máximo 50 caracteres";
                e.target.value = e.target.value.substring(0, 50);
            }
        });
    }

    // Validación en tiempo real para descripción (solo símbolos permitidos)
    if (descripcionInput) {
        descripcionInput.addEventListener('input', function (e) {
            const input = e.target.value;
            const errorElement = document.getElementById('errorDescripcion');

            // Eliminar símbolos no permitidos
            e.target.value = input.replace(SIMBOLOS_NO_PERMITIDOS_DESCRIPCION, '');

            if (input !== e.target.value) {
                errorElement.innerText = "Solo se permiten letras, números, espacios y los símbolos .,-/()";
            } else {
                errorElement.innerText = "";
            }

            // Validar longitud
            if (e.target.value.length > 500) {
                errorElement.innerText = "Máximo 500 caracteres";
                e.target.value = e.target.value.substring(0, 500);
            }
        });
    }


    // Validación de precio mientras se escribe
    if (precioInput) {
        precioInput.addEventListener('input', function (e) {
            const input = e.target.value;
            const errorElement = document.getElementById('errorPrecio');

            // Solo permite números y un punto decimal
            e.target.value = input.replace(/[^0-9.]/g, '');

            // Elimina puntos adicionales
            const dotCount = (e.target.value.match(/\./g) || []).length;
            if (dotCount > 1) {
                e.target.value = e.target.value.substring(0, e.target.value.lastIndexOf('.'));
            }

            // Limita a 2 decimales
            if (dotCount === 1) {
                const parts = e.target.value.split('.');
                if (parts[1] && parts[1].length > 2) {
                    e.target.value = parts[0] + '.' + parts[1].substring(0, 2);
                }
            }

            // Limita la longitud total
            if (e.target.value.length > 10) {
                e.target.value = e.target.value.substring(0, 10);
                errorElement.innerText = "Máximo 10 caracteres";
            } else {
                errorElement.innerText = "";
            }
        });
    }

    // Validación del formulario al enviar
    if (form) {
        form.addEventListener('submit', function (e) {
            // Obtén los valores de los campos
            const nombre = nombreInput ? nombreInput.value.trim() : '';
            const descripcion = descripcionInput ? descripcionInput.value.trim() : '';
            const precio = precioInput ? precioInput.value.trim() : '';
            const categoria = categoriaInput ? categoriaInput.value : '';
            const imagenes = imagenesInput ? imagenesInput.files : [];

            // Limpia los mensajes de error previos
            document.getElementById('errorNombre').innerText = "";
            document.getElementById('errorDescripcion').innerText = "";
            document.getElementById('errorPrecio').innerText = "";
            document.getElementById('errorCategoria').innerText = "";
            document.getElementById('errorImagenes').innerText = "";

            // Validación de campos
            let isValid = true;

            // Validar nombre (sin números)
            if (!nombre) {
                document.getElementById('errorNombre').innerText = "El nombre es obligatorio";
                isValid = false;
            } else if (nombre.length < 3 || nombre.length > 50) {
                document.getElementById('errorNombre').innerText = "El nombre debe tener entre 3 y 50 caracteres";
                isValid = false;
            } else if (!REGEX_NOMBRE.test(nombre)) {
                document.getElementById('errorNombre').innerText = "El nombre solo debe contener letras y algunos símbolos básicos (.,-/), no números";
                isValid = false;
            } else if (SIMBOLOS_NO_PERMITIDOS.test(nombre)) {
                document.getElementById('errorNombre').innerText = "El nombre contiene símbolos no permitidos";
                isValid = false;
            }

            // Validar descripción (permite números pero no símbolos especiales)
            if (!descripcion) {
                document.getElementById('errorDescripcion').innerText = "La descripción es obligatoria";
                isValid = false;
            } else if (descripcion.length < 10 || descripcion.length > 500) {
                document.getElementById('errorDescripcion').innerText = "La descripción debe tener entre 10 y 500 caracteres";
                isValid = false;
            } else if (SIMBOLOS_NO_PERMITIDOS.test(descripcion)) {
                document.getElementById('errorDescripcion').innerText = "La descripción contiene símbolos no permitidos";
                isValid = false;
            } else if (!REGEX_DESCRIPCION.test(descripcion)) {
                document.getElementById('errorDescripcion').innerText = "La descripción solo puede contener letras, números y algunos símbolos básicos (.,-/())";
                isValid = false;
            }

            // Validar precio
            if (!precio) {
                document.getElementById('errorPrecio').innerText = "El precio es obligatorio";
                isValid = false;
            } else if (!REGEX_PRECIO.test(precio)) {
                document.getElementById('errorPrecio').innerText = "El precio debe ser un número positivo con hasta 2 decimales";
                isValid = false;
            } else if (parseFloat(precio) <= 0) {
                document.getElementById('errorPrecio').innerText = "El precio debe ser mayor que 0";
                isValid = false;
            } else if (precio.length > 10) {
                document.getElementById('errorPrecio').innerText = "El precio no puede exceder 10 caracteres";
                isValid = false;
            }

            // Validar categoría
            if (!categoria || isNaN(categoria)) {
                document.getElementById('errorCategoria').innerText = "Seleccione una categoría válida";
                isValid = false;
            }

            // Validar imágenes (si se subieron)
            if (imagenes.length > 0) {
                if (imagenes.length > 5) {
                    document.getElementById('errorImagenes').innerText = "No se pueden subir más de 5 imágenes";
                    isValid = false;
                } else {
                    for (let i = 0; i < imagenes.length; i++) {
                        if (!REGEX_IMAGEN.test(imagenes[i].name)) {
                            document.getElementById('errorImagenes').innerText = "Solo se permiten imágenes JPG, PNG, GIF o WEBP";
                            isValid = false;
                            break;
                        }

                        if (imagenes[i].size > 5242880) { // 5MB
                            document.getElementById('errorImagenes').innerText = "Las imágenes no deben exceder 5MB";
                            isValid = false;
                            break;
                        }
                    }
                }
            }

            if (!isValid) {
                e.preventDefault();
            } else {
                // Si todo es válido, mostrar el modal de confirmación
                const modal = new bootstrap.Modal(document.getElementById('modal_confirm'));
                modal.show();

                // Prevenir el envío del formulario hasta que se confirme en el modal
                e.preventDefault();

                // Configurar el botón de confirmación del modal para enviar el formulario
                document.getElementById('confirmarModificacion').addEventListener('click', function () {
                    form.submit();
                });
            }
        });
    }
});   