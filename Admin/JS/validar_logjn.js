document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('loginForm');
    const usuarioInput = form.usuario;
    const passInput = form.contraseña;
  
    form.addEventListener('submit', function (e) {
      let valid = true;
      document.getElementById('errorUsuario').textContent = '';
      document.getElementById('errorContraseña').textContent = '';

    const usernameRegex = /^(?=[a-zA-Z0-9_.]{4,20}$)(?!.*\.\.)(?!\.)(?!.*\.$).*$/;
    if (!usernameRegex.test(usuarioInput.value.trim())) {
      document.getElementById('errorUsuario').textContent = 'Ingrese un usuario válido.';
      valid = false;
    }
  
      if (usuarioInput.value.trim() === '') {
        document.getElementById('errorUsuario').textContent = 'El usuario es requerido.';
        valid = false;
      }
  
      if (passInput.value.trim() === '') {
        document.getElementById('errorContraseña').textContent = 'La contraseña es requerida.';
        valid = false;
      }
  
      if (!valid) {
        e.preventDefault(); // Previene envío si hay errores
      }
    });
  });
  