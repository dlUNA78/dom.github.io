document.getElementById('nombre').addEventListener('keypress', function(event) {
    const charCode = event.charCode || event.keyCode;
    const charStr = String.fromCharCode(charCode);

    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]$/.test(charStr)) {
        event.preventDefault(); // Evita que se ingrese el carácter no válido
    }
});

document.getElementById('btnAgregar').addEventListener('click', function(event) {
    const nombre = document.getElementById('nombre').value.trim();
    const errorCategoria = document.getElementById('errorCategoria');

    errorCategoria.innerText = "";

    if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/.test(nombre)) {
        errorCategoria.innerText = "El nombre solo puede contener letras.";
        event.preventDefault(); // Evita que se procese el formulario si no es válido
    }
});



