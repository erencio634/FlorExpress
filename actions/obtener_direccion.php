<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
    exit;
}

$id = $_GET['id'] ?? 0;
if (!$id) {
    echo json_encode(['status' => 'error', 'message' => 'ID inválido']);
    exit;
}

// Verificamos que la dirección pertenece al cliente actual
$id_usuario = $_SESSION['id_usuario'];
$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmt = $conn->prepare($sqlCliente);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();
$id_cliente = $cliente['id_cliente'] ?? 0;

if (!$id_cliente) {
    echo json_encode(['status' => 'error', 'message' => 'Cliente no encontrado']);
    exit;
}

$sql = "SELECT * FROM direcciones WHERE id_direccion = ? AND id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id, $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Dirección no encontrada']);
    exit;
}

$data = $res->fetch_assoc();
echo json_encode(['status' => 'ok', 'data' => $data]);
