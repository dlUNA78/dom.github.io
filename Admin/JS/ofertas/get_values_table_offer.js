// get_values_table_offer.js
function getTableValues(button) {
    // Obtener la fila en la que se hizo clic
    const row = button.closest('tr');
  
    // Obtener los valores de las celdas
    const nombre = row.cells[0].innerText;
    const descripcion = row.cells[1].innerText;
    const precio = row.cells[2].innerText;
    const precioDescuento = row.cells[3].innerText;
    const imagen = row.cells[4].querySelector('img').src;
  
    // Guardar los valores en localStorage para usarlos en la siguiente página
    localStorage.setItem('nombre', nombre);
    localStorage.setItem('descripcion', descripcion);
    localStorage.setItem('precio', precio);
    localStorage.setItem('precioDescuento', precioDescuento);
    localStorage.setItem('imagen', imagen);
  
    // Redirigir a la página de modificación
    window.location.href = button.getAttribute('href');
  }
