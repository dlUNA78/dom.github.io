

// Simulación de la lista de ofertas
const ofertas = [
  {
    id: 1,
    nombre: "Molino",
    descripcion: "Prepara masa fresca y de calidad con nuestro molino para masa...",
    precio: 500,
    precioDescuento: 250,
    imagen: "../assets/img/molino.png"
  },
  {
    id: 2,
    nombre: "Plaguicida",
    descripcion: "Mantén tus plantas libres de plagas con nuestro plaguicida...",
    precio: 300,
    precioDescuento: 175,
    imagen: "../assets/img/clipboard-image-2.png"
  }
];

// Obtener el elemento <select> de la lista desplegable
const listaDesplegable = document.getElementById("lista-desplegable");

// Llenar la lista desplegable con los nombres de las ofertas
ofertas.forEach(oferta => {
  const option = document.createElement("option");
  option.value = oferta.id; // El valor de la opción será el ID de la oferta
  option.textContent = oferta.nombre; // El texto visible será el nombre de la oferta
  listaDesplegable.appendChild(option);
});

// Manejar el evento "change" de la lista desplegable
listaDesplegable.addEventListener("change", function (event) {
  const selectedOfertaId = event.target.value; // Obtener el ID de la oferta seleccionada
  const selectedOferta = ofertas.find(o => o.id == selectedOfertaId); // Buscar la oferta en la lista

  if (selectedOferta) {
    // Rellenar el formulario con los detalles de la oferta seleccionada
    document.getElementById("nombre").value = selectedOferta.nombre;
    document.getElementById("descripcion").value = selectedOferta.descripcion;
    document.getElementById("precioBe").value = selectedOferta.precio;
    document.getElementById("precioNew").value = selectedOferta.precioDescuento;
  }
});

// Manejar el evento "click" del botón "Modificar"
document.getElementById("btn_agregar").addEventListener("click", function () {
  // Obtener los valores del formulario
  const nombre = document.getElementById("nombre").value;
  const descripcion = document.getElementById("descripcion").value;
  const precioBe = document.getElementById("precioBe").value;
  const precioNew = document.getElementById("precioNew").value;

  // Validar los campos
  let isValid = true;

  if (nombre === "") {
    document.getElementById("errorNombre").innerText = "Por favor, completa este campo.";
    isValid = false;
  }
  if (descripcion === "") {
    document.getElementById("errorDescripcion").innerText = "Por favor, completa este campo.";
    isValid = false;
  }
  if (precioNew === "") {
    document.getElementById("errorPrecioNew").innerText = "El campo no puede estar vacío";
    isValid = false;
  } else if (isNaN(precioNew) || precioNew <= 0) {
    document.getElementById("errorPrecioNew").innerText = "El nuevo precio debe ser un número válido";
    isValid = false;
  }

  if (isValid) {
    // Obtener el ID de la oferta seleccionada
    const selectedOfertaId = listaDesplegable.value;

    // Buscar la oferta en la lista
    const ofertaIndex = ofertas.findIndex(o => o.id == selectedOfertaId);

    if (ofertaIndex !== -1) {
      // Actualizar la oferta con los nuevos valores
      ofertas[ofertaIndex] = {
        id: ofertas[ofertaIndex].id, // Mantener el mismo ID
        nombre: nombre,
        descripcion: descripcion,
        precio: parseFloat(precioBe), // Convertir el precio anterior a número
        precioDescuento: parseFloat(precioNew), // Convertir el nuevo precio a número
        imagen: ofertas[ofertaIndex].imagen // Mantener la misma imagen
      };

      // Mostrar el modal de confirmación
      const modal = new bootstrap.Modal(document.getElementById("modal_confirm"));
      modal.show();

      // Limpiar el formulario
      document.getElementById("nombre").value = "";
      document.getElementById("descripcion").value = "";
      document.getElementById("precioBe").value = "";
      document.getElementById("precioNew").value = "";

      // Redirigir al usuario a la página de ofertas después de cerrar el modal
      document.querySelector("#modal_confirm .btn-light").addEventListener("click", function () {
        window.location.href = "../Ofertas/view_produc.html";
      });
    } else {
      console.error("Oferta no encontrada");
    }
  }
});