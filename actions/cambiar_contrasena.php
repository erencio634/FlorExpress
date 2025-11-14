<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$actual = $_POST['actual'] ?? '';
$nueva = $_POST['nueva'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';

if (empty($actual) || empty($nueva) || empty($confirmar)) {
    echo json_encode(['status'=>'error','message'=>'Todos los campos son obligatorios']);
    exit;
}

if ($nueva !== $confirmar) {
    echo json_encode(['status'=>'error','message'=>'Las contraseñas no coinciden']);
    exit;
}

// Verificar contraseña actual
$stmt = $conn->prepare("SELECT contrasena FROM usuarios_globales WHERE id_usuario=?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user || $user['contrasena'] !== $actual) { // Cambia esto a password_verify() si luego usas hash
    echo json_encode(['status'=>'error','message'=>'Contraseña actual incorrecta']);
    exit;
}

// Actualizar contraseña
$stmt = $conn->prepare("UPDATE usuarios_globales SET contrasena=? WHERE id_usuario=?");
$stmt->bind_param("si", $nueva, $id_usuario);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Contraseña actualizada correctamente']);
} else {
    echo json_encode(['status'=>'error','message'=>'Error al cambiar contraseña']);
}
