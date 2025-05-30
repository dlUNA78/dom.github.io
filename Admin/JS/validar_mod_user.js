document.addEventListener('DOMContentLoaded', function () {
    // Toggle para mostrar contraseñas
    const showPasswords = document.getElementById('showPasswords');
    const currentPassInput = document.getElementById('contraseñaAct');
    const newPassInput = document.getElementById('contraseña');
    const confirmPassInput = document.getElementById('contraseñaConf');

    if (showPasswords) {
        showPasswords.addEventListener('change', function () {
            const type = this.checked ? 'text' : 'password';
            if (currentPassInput) currentPassInput.type = type;
            if (newPassInput) newPassInput.type = type;
            if (confirmPassInput) confirmPassInput.type = type;
        });
    }

    // Validación del formulario
    const form = document.getElementById('formularioModificacion');
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
    document.getElementById('contraseñaAct')?.addEventListener('input', validateCurrentPassword);
    document.getElementById('contraseña')?.addEventListener('input', validatePassword);
    document.getElementById('contraseñaConf')?.addEventListener('input', validatePasswordMatch);
    document.getElementById('imagen')?.addEventListener('change', validateImage);

    // Mostrar errores existentes al cargar
    showExistingErrors();

    // Funciones de validación
    function validateForm() {
        let isValid = true;

        if (!validateNombre()) isValid = false;
        if (!validateCurrentPassword()) isValid = false;

        // Validar nueva contraseña solo si se proporciona
        if (newPassInput?.value) {
            if (!validatePassword()) isValid = false;
            if (!validatePasswordMatch()) isValid = false;
        }

        if (!validateImage()) isValid = false;

        if (!isValid) {
            alert('Por favor corrija los errores en el formulario');
        }

        return isValid;
    }

    //Validación de nombre
    function validateNombre() {
        const nameRegex = /^[A-Za-zÁÉÍÓÚáéíóúÜüÑñ]+( [A-Za-zÁÉÍÓÚáéíóúÜüÑñ]+)*$/;
        const value = document.getElementById('nombre').value.trim();
        const errorElement = document.getElementById('errorNombre');

        if (!value) {
            showError(errorElement, 'El nombre es obligatorio');
            return false;
        }

        if (!nameRegex.test(value)) {
            showError(errorElement, 'Solo letras y espacios');
            return false;
        }

        clearError(errorElement);
        return true;
    }



    // Validación de contraseña actual
    function validateCurrentPassword() {
        const value = document.getElementById('contraseñaAct').value;
        const errorElement = document.getElementById('errorContraseñaAct');

        if (!value) {
            showError(errorElement, 'La contraseña actual es obligatoria');
            return false;
        }

        if (value.length < 8) {
            showError(errorElement, 'Mínimo 8 caracteres');
            return false;
        }

        clearError(errorElement);
        return true;
    }


    //validacion de contraseña
    function validatePassword() {
        const value = document.getElementById('contraseña').value;
        const errorElement = document.getElementById('errorContraseña');

        // Si no hay valor, no es obligatorio
        if (!value) {
            clearError(errorElement);
            return true;
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

        // Solo validar si hay contraseña nueva
        if (!password) {
            clearError(errorElement);
            return true;
        }

        if (!confirmPassword) {
            showError(errorElement, 'Confirme su nueva contraseña');
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

        // La imagen es opcional en modificación
        if (!fileInput.files || fileInput.files.length === 0) {
            clearError(errorElement);
            return true;
        }

        const file = fileInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!validTypes.includes(file.type)) {
            showError(errorElement, 'Formato no válido (solo JPG, PNG, WEBP)');
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
            // Añadir clase de error al input correspondiente
            const input = element.previousElementSibling;
            if (input && input.classList) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            }
        }
    }

    function clearError(element) {
        if (element) {
            element.textContent = '';
            element.style.display = 'none';
            // Remover clase de error del input correspondiente
            const input = element.previousElementSibling;
            if (input && input.classList) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        }
    }

    function showExistingErrors() {
        // Mostrar errores que puedan venir del servidor
        const urlParams = new URLSearchParams(window.location.search);
        const errorParam = urlParams.get('error');

        if (errorParam) {
            const errors = errorParam.split(', ');
            errors.forEach(error => {
                if (error.includes('nombre')) {
                    showError(document.getElementById('errorNombre'), error);
                } else if (error.includes('usuario')) {
                    showError(document.getElementById('errorUsuario'), error);
                } else if (error.includes('contraseña actual')) {
                    showError(document.getElementById('errorContraseñaAct'), error);
                } else if (error.includes('contraseñas no coinciden')) {
                    showError(document.getElementById('errorContraseñaConf'), error);
                } else if (error.includes('imagen')) {
                    showError(document.getElementById('errorImagen'), error);
                }
            });
        }

        // Mostrar modal de éxito si existe
        if (urlParams.get('success')) {
            const modal = new bootstrap.Modal(document.getElementById('modal-1'));
            modal.show();

        }
    }
});
