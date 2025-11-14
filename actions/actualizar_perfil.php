<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$ruta_foto = null;

// Obtener id_cliente
$stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();
$id_cliente = $cliente['id_cliente'] ?? 0;

if (!$id_cliente) {
    echo json_encode(['status'=>'error','message'=>'Cliente no encontrado']);
    exit;
}

// Subida de imagen (si existe)
if (!empty($_FILES['foto']['name'])) {
    $nombreArchivo = uniqid('perfil_') . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $rutaDestino = '../uploads/clientes/' . $nombreArchivo;

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
        $ruta_foto = '/uploads/clientes/' . $nombreArchivo;
    } else {
        echo json_encode(['status'=>'error','message'=>'Error al subir imagen']);
        exit;
    }
}

// Actualizar datos
if ($ruta_foto) {
    $sql = "UPDATE clientes SET nombre=?, apellidos=?, telefono=?, foto_perfil=? WHERE id_cliente=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $apellidos, $telefono, $ruta_foto, $id_cliente);
} else {
    $sql = "UPDATE clientes SET nombre=?, apellidos=?, telefono=? WHERE id_cliente=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $apellidos, $telefono, $id_cliente);
}

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Perfil actualizado correctamente']);
} else {
    echo json_encode(['status'=>'error','message'=>'Error al actualizar perfil']);
}
