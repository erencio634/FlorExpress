<?php
session_start();
require_once '../conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'floreria') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$id_pedido = $data['id_pedido'] ?? null;
$nuevo_estado = $data['estado'] ?? null;

if (!$id_pedido || !$nuevo_estado) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit();
}

// Mapear estados
$estado_rastreo_map = [
    'preparando' => 'Preparando',
    'enviado' => 'En camino',
    'entregado' => 'Entregado'
];

$estado_rastreo = $estado_rastreo_map[$nuevo_estado] ?? 'Preparando';

// Actualizar estado del pedido
$sql_update = "UPDATE pedidos SET estado = ?, estado_rastreo = ? WHERE id_pedido = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("ssi", $nuevo_estado, $estado_rastreo, $id_pedido);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Estado actualizado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
}

$conn->close();
?>
