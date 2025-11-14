// Global variables
let currentUserType = 'cliente';
let currentRegisterType = 'cliente';

// Modal functions
function hideModals() {
    document.getElementById('login-modal').classList.add('hidden');
    document.getElementById('register-modal').classList.add('hidden');
    document.getElementById('password-recovery-modal').classList.add('hidden');
}

function showLogin() {
    hideModals();
    document.getElementById('login-modal').classList.remove('hidden');
}

function showRegister() {
    hideModals();
    document.getElementById('register-modal').classList.remove('hidden');
}

function showPasswordRecovery() {
    hideModals();
    document.getElementById('password-recovery-modal').classList.remove('hidden');
}

function setUserType(type) {
    currentUserType = type;
    const buttons = document.querySelectorAll('.user-type-btn');
    buttons.forEach(btn => {
        btn.classList.remove('bg-verde-hoja', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-verde-hoja', 'text-white');
}

function setRegisterType(type) {
    currentRegisterType = type;
    const buttons = document.querySelectorAll('.register-type-btn');
    const floreriaFields = document.getElementById('floreria-fields');
    
    buttons.forEach(btn => {
        btn.classList.remove('bg-verde-hoja', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-200', 'text-gray-700');
    event.target.classList.add('bg-verde-hoja', 'text-white');
    
    if (type === 'floreria') {
        floreriaFields.classList.remove('hidden');
    } else {
        floreriaFields.classList.add('hidden');
    }
}

// FUNCIONALIDAD DE LOGIN CON BASE DE DATOS
function showDashboard() {
    const email = document.querySelector('#login-modal input[type="email"]').value;
    const password = document.querySelector('#login-modal input[type="password"]').value;

    if (!email || !password) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos vacíos',
            text: 'Por favor completa todos los campos.'
        });
        return;
    }

    $.ajax({
        url: 'login.php',
        type: 'POST',
        data: { email: email, password: password },
        success: function(respuesta) {
            if (
                respuesta === 'dashboard_cliente' ||
                respuesta === 'dashboard_floreria' ||
                respuesta === 'dashboard_admin'
            ) {
                Swal.fire({
                    icon: 'success',
                    title: 'Inicio de sesión exitoso',
                    text: 'Bienvenido a Flor Express',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = respuesta + '.php';
                });
            } else if (respuesta === 'password_incorrecta') {
                Swal.fire({
                    icon: 'error',
                    title: 'Contraseña incorrecta',
                    text: 'Verifica tus datos e inténtalo de nuevo.'
                });
            } else if (respuesta === 'usuario_no_existe') {
                Swal.fire({
                    icon: 'error',
                    title: 'Usuario no encontrado',
                    text: 'El correo ingresado no está registrado.'
                });
            } else if (respuesta === 'usuario_inactivo') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cuenta inactiva',
                    text: 'Tu cuenta está desactivada.'
                });
            } else if (respuesta === 'campos_vacios') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos vacíos',
                    text: 'Por favor completa todos los campos.'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error del servidor',
                    text: 'Ocurrió un problema inesperado.'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo contactar con el servidor.'
            });
        }
    });
}


// Mensaje genérico de éxito (para el registro o recuperación)
function showSuccessMessage(message) {
    Swal.fire({
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1500
    });
    hideModals();
}
