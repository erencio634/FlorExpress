<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
    exit;
}

$numero = $_POST['numero_pedido'] ?? '';
$numero = trim($numero);

if ($numero === '' || !preg_match('/^FE-(\d+)$/i', $numero, $m)) {
    echo json_encode(['status' => 'error', 'message' => 'Formato de pedido inválido.']);
    exit;
}

$id_pedido = (int) $m[1];

$sql = "SELECT id_pedido, estado, estado_rastreo, total, fecha_pedido
        FROM pedidos
        WHERE id_pedido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $pedido = $res->fetch_assoc();
    echo json_encode(['status' => 'ok'] + $pedido);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró ningún pedido con ese número.']);
}
?>
