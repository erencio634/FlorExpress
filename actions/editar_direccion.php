<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

$id = $_POST['id_direccion'] ?? 0;

$sql = "UPDATE direcciones SET 
nombre_receptor=?, apellidos_receptor=?, telefono_receptor=?, codigo_postal=?, 
estado=?, municipio=?, colonia=?, calle=?, referencias=? 
WHERE id_direccion=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssisssssi", 
    $_POST['nombre_receptor'], $_POST['apellidos_receptor'], $_POST['telefono_receptor'], 
    $_POST['codigo_postal'], $_POST['estado'], $_POST['municipio'], $_POST['colonia'], 
    $_POST['calle'], $_POST['referencias'], $id
);
if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Dirección actualizada']);
} else {
    echo json_encode(['status'=>'error','message'=>'No se pudo actualizar']);
}
