<?php
session_start();
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit; // evitar carga directa
}


$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo 'campos_vacios';
    exit;
}

// Buscar usuario sin importar el rol
$sql = "SELECT id_usuario, correo, contrasena, rol, activo 
        FROM usuarios_globales
        WHERE correo = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo 'error_sql_prepare';
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si existe el usuario
if ($result->num_rows === 0) {
    echo 'usuario_no_existe';
    $stmt->close();
    $conn->close();
    exit;
}

$usuario = $result->fetch_assoc();

// Verificar si está activo
if ((int)$usuario['activo'] !== 1) {
    echo 'usuario_inactivo';
    $stmt->close();
    $conn->close();
    exit;
}

// Comparar contraseñas (sin hash)
if ($password !== $usuario['contrasena']) {
    echo 'password_incorrecta';
    $stmt->close();
    $conn->close();
    exit;
}

// Guardar sesión
$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['correo'] = $usuario['correo'];
$_SESSION['rol'] = $usuario['rol'];
// Buscar el id_cliente vinculado a este usuario (solo si es cliente)
if ($usuario['rol'] === 'cliente') {
    $sqlCli = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
    $stmtCli = $conn->prepare($sqlCli);
    $stmtCli->bind_param("i", $usuario['id_usuario']);
    $stmtCli->execute();
    $resCli = $stmtCli->get_result();
    $cli = $resCli->fetch_assoc();

    if ($cli) {
        $_SESSION['id_cliente'] = $cli['id_cliente'];
    }
    $stmtCli->close();
}


// Redirigir según el rol detectado
switch ($usuario['rol']) {
    case 'cliente':
        echo 'dashboard_cliente';
        break;
    case 'floreria':
        echo 'dashboard_floreria';
        break;
    case 'admin':
    case 'superadmin':
        echo 'dashboard_admin';
        break;
    default:
        echo 'rol_desconocido';
        break;
}

$stmt->close();
$conn->close();
?>