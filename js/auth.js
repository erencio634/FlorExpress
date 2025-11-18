// Global variables
let currentUserType = 'cliente';
let currentRegisterType = 'cliente';

// Modal functions (a prueba de fallos)
function hideModals() {
    const modals = [
        'login-modal',
        'register-modal',
        'password-recovery-modal'
    ];

    modals.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
}

function showLogin() {
    hideModals();
    const m = document.getElementById('login-modal');
    if (m) m.classList.remove('hidden');
}

function showRegister() {
    hideModals();
    const m = document.getElementById('register-modal');
    if (m) m.classList.remove('hidden');
}

function showPasswordRecovery() {
    hideModals();
    const m = document.getElementById('password-recovery-modal');
    if (m) m.classList.remove('hidden');
}


// Fix: event is now safely passed
function setUserType(type, event) {
    currentUserType = type;
    const buttons = document.querySelectorAll('.user-type-btn');
    buttons.forEach(btn => {
        btn.classList.remove('bg-verde-hoja', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });

    if (event && event.target) {
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-verde-hoja', 'text-white');
    }
}

function setRegisterType(type, event) {
    currentRegisterType = type;
    const buttons = document.querySelectorAll('.register-type-btn');
    const floreriaFields = document.getElementById('floreria-fields');
    
    buttons.forEach(btn => {
        btn.classList.remove('bg-verde-hoja', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });

    if (event && event.target) {
        event.target.classList.remove('bg-gray-200', 'text-gray-700');
        event.target.classList.add('bg-verde-hoja', 'text-white');
    }
    
    if (type === 'floreria') {
        if (floreriaFields) floreriaFields.classList.remove('hidden');
    } else {
        if (floreriaFields) floreriaFields.classList.add('hidden');
    }
}


// LOGIN
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


// mensaje genérico
function showSuccessMessage(message) {
    Swal.fire({
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1500
    });
    hideModals();
}


// cerrar modal al hacer click fuera
document.addEventListener('click', (e) => {
    const modals = ['login-modal', 'register-modal', 'password-recovery-modal'];
    modals.forEach(id => {
        const modal = document.getElementById(id);
        if (!modal.classList.contains('hidden') && e.target === modal) {
            hideModals();
        }
    });
});
