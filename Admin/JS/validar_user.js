//Este script es para buscar los usuarios o nombres en la tabla de usuarios principal
    //Inicia la función de búsqueda de usuarios
    
    function searchUsers() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.querySelector('table'); // Asegúrate de que selecciona la tabla correcta
        const tr = table.getElementsByTagName('tr');

        // Convertimos las filas a un array para poder ordenarlas
        const rowsArray = Array.from(tr).slice(1); // Excluimos el encabezado

        // Ordenamos las filas según la coincidencia
        rowsArray.sort((a, b) => {
            const aUser = a.getElementsByTagName('td')[0].textContent.toUpperCase();
            const aName = a.getElementsByTagName('td')[1].textContent.toUpperCase();
            const bUser = b.getElementsByTagName('td')[0].textContent.toUpperCase();
            const bName = b.getElementsByTagName('td')[1].textContent.toUpperCase();

            // Calculamos puntajes de coincidencia
            const aUserScore = calculateMatchScore(aUser, filter);
            const aNameScore = calculateMatchScore(aName, filter);
            const bUserScore = calculateMatchScore(bUser, filter);
            const bNameScore = calculateMatchScore(bName, filter);

            // Tomamos el mejor puntaje para cada fila
            const aMaxScore = Math.max(aUserScore, aNameScore);
            const bMaxScore = Math.max(bUserScore, bNameScore);

            // Ordenamos de mayor a menor puntaje
            return bMaxScore - aMaxScore;
        });

        // Mostramos/ocultamos filas según si coinciden
        rowsArray.forEach(row => {
            const user = row.getElementsByTagName('td')[0].textContent.toUpperCase();
            const name = row.getElementsByTagName('td')[1].textContent.toUpperCase();

            if (user.includes(filter) || name.includes(filter) || filter === '') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Reinsertamos las filas ordenadas
        const tbody = table.querySelector('tbody');
        rowsArray.forEach(row => tbody.appendChild(row));
    }

    function calculateMatchScore(text, filter) {
        if (filter === '') return 0;

        // Puntaje más alto si coincide desde el inicio
        if (text.startsWith(filter)) return 3;

        // Puntaje medio si contiene el filtro
        if (text.includes(filter)) return 2;

        // Puntaje bajo si coincide parcialmente (solo algunas letras)
        const filterLetters = filter.split('');
        const matches = filterLetters.filter(letter => text.includes(letter)).length;
        return matches / filterLetters.length;
    }
    //Termina la función de búsqueda por usuarios o nombres en la tabla de usuarios principal


document.addEventListener('DOMContentLoaded', function () {
    // Toggle para mostrar contraseñas
    const showPasswords = document.getElementById('showPasswords');
    const passwordField = document.getElementById('contraseña');
    const confirmPasswordField = document.getElementById('contraseñaConf');

    if (showPasswords) {
        showPasswords.addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            passwordField.type = type;
            confirmPasswordField.type = type;
        });
    }

    // Validación del formulario
    const form = document.getElementById('formulario');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (validateForm()) {
                this.submit();

            }
        });
    }



    // Validación en tiempo real
    document.getElementById('nombre')?.addEventListener('input', validateNombre);
    document.getElementById('usuario')?.addEventListener('input', validateEmail);
    document.getElementById('contraseña')?.addEventListener('input', validatePassword);
    document.getElementById('contraseñaConf')?.addEventListener('input', validatePasswordMatch);

    // Funciones de validación
    function validateForm() {
        let isValid = true;

        if (!validateNombre()) isValid = false;
        if (!validateEmail()) isValid = false;
        if (!validatePassword()) isValid = false;
        if (!validatePasswordMatch()) isValid = false;
        if (!validateImage()) isValid = false;

        return isValid;

    }

    function validateNombre() {
        const value = document.getElementById('nombre').value.trim();
        const errorElement = document.getElementById('errorNombre');
        const nameRegex = /^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ\s]+$/;

        if (!value) {
            showError(errorElement, 'El nombre es obligatorio');
            return false;
        }

        if (!nameRegex.test(value)) {
            showError(errorElement, 'Solo letras y espacios (sin números ni símbolos)');
            return false;
        }

        clearError(errorElement);
        return true;
    }

    function validateEmail() {
        const value = document.getElementById('usuario').value.trim();
        const errorElement = document.getElementById('errorUsuario');
        const usernameRegex = /^(?=[a-zA-Z0-9_.]{4,20}$)(?!.*\.\.)(?!\.)(?!.*\.$).*$/;


        if (!value) {
            showError(errorElement, 'El usuario es obligatorio');
            return false;
        }

        if (!usernameRegex.test(value)) {
            showError(errorElement, 'Usuario no válido');
            return false;
        }

        clearError(errorElement);
        return true;
    }

    function validatePassword() {
        const value = document.getElementById('contraseña').value;
        const errorElement = document.getElementById('errorContraseña');

        if (!value) {
            showError(errorElement, 'La contraseña es obligatoria');
            return false;
        }

        if (value.length < 8) {
            showError(errorElement, 'Mínimo 8 caracteres');
            return false;
        }

        if (!/[A-Z]/.test(value)) {
            showError(errorElement, 'Al menos una mayúscula');
            return false;
        }

        if (!/[0-9]/.test(value)) {
            showError(errorElement, 'Al menos un número');
            return false;
        }

        if (!/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
            showError(errorElement, 'Al menos un símbolo');
            return false;
        }

        clearError(errorElement);
        return true;
    }

    function validatePasswordMatch() {
        const password = document.getElementById('contraseña').value;
        const confirmPassword = document.getElementById('contraseñaConf').value;
        const errorElement = document.getElementById('errorContraseñaConf');

        if (!confirmPassword) {
            showError(errorElement, 'Confirme su contraseña');
            return false;
        }

        if (password !== confirmPassword) {
            showError(errorElement, 'Las contraseñas no coinciden');
            return false;
        }

        clearError(errorElement);
        return true;
    }

    function validateImage() {
        const fileInput = document.getElementById('imagen');
        const errorElement = document.getElementById('errorImagen');

        if (!fileInput.files || fileInput.files.length === 0) {
            showError(errorElement, 'La imagen es obligatoria');
            return false;
        }

        const file = fileInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 2 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            showError(errorElement, 'Formato no válido (JPG, PNG, WEBP)');
            return false;
        }

        if (file.size > maxSize) {
            showError(errorElement, 'La imagen no debe superar 2MB');
            return false;
        }

        clearError(errorElement);
        return true;
    }

    function showError(element, message) {
        if (element) {
            element.textContent = message;
            element.style.display = 'block';
        }
    }

    function clearError(element) {
        if (element) {
            element.textContent = '';
            element.style.display = 'none';
        }
    }
    // Redirigir a la página de usuario después de la modificación

});
