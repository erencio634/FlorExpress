<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

$id = $_POST['id_metodo'] ?? 0;
if (!$id) {
    echo json_encode(['status'=>'error','message'=>'ID no válido']);
    exit;
}

$sql = "DELETE FROM metodos_pago_cliente WHERE id_metodo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Método eliminado correctamente']);
} else {
    echo json_encode(['status'=>'error','message'=>'No se pudo eliminar el método']);
}
