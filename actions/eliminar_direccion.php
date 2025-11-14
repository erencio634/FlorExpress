<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

$id = $_POST['id_direccion'] ?? 0;
$sql = "DELETE FROM direcciones WHERE id_direccion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Dirección eliminada']);
} else {
    echo json_encode(['status'=>'error','message'=>'No se pudo eliminar']);
}
