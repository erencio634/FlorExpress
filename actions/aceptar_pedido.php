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

if (!$id_pedido) {
    echo json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado']);
    exit();
}

// Obtener ID de la florería
$id_usuario = $_SESSION['id_usuario'];
$sql_floreria = "SELECT id_floreria FROM florerias WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_floreria);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$floreria = $result->fetch_assoc();

if (!$floreria) {
    echo json_encode(['success' => false, 'message' => 'Florería no encontrada']);
    exit();
}

$id_floreria = $floreria['id_floreria'];

// Actualizar el pedido
$sql_update = "UPDATE detalles_pedido SET id_floreria = ? WHERE id_pedido = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("ii", $id_floreria, $id_pedido);

if ($stmt->execute()) {
    // Actualizar estado del pedido principal
    $sql_estado = "UPDATE pedidos SET estado = 'confirmado', estado_rastreo = 'Preparando' WHERE id_pedido = ?";
    $stmt = $conn->prepare($sql_estado);
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Pedido aceptado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al aceptar el pedido']);
}

$conn->close();
?>
