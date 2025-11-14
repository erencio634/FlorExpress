<?php
session_start();
require_once "../conexion.php";

header('Content-Type: application/json');

// verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no activa.']);
    exit;
}

// obtener id_cliente a partir del usuario logueado
$id_usuario = $_SESSION['id_usuario'];
$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmtC = $conn->prepare($sqlCliente);
$stmtC->bind_param("i", $id_usuario);
$stmtC->execute();
$resC = $stmtC->get_result();
$cliente = $resC->fetch_assoc();

if (!$cliente) {
    echo json_encode(['status' => 'error', 'message' => 'Cliente no encontrado.']);
    exit;
}

$id_cliente = $cliente['id_cliente'];

// recibir datos del formulario
$id_pedido   = $_POST['id_pedido'] ?? null;
$id_articulo = $_POST['id_articulo'] ?? null;
$calificacion = $_POST['calificacion'] ?? null;
$comentario  = $_POST['comentario'] ?? null;

// validación básica
if (!$id_pedido || !$id_articulo || !$calificacion) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos obligatorios.']);
    exit;
}

// evitar reseñas duplicadas para el mismo pedido/artículo/cliente
$sqlCheck = "SELECT id_resena FROM resenas 
             WHERE id_pedido = ? AND id_articulo = ? AND id_cliente = ?";
$stmtChk = $conn->prepare($sqlCheck);
$stmtChk->bind_param("iii", $id_pedido, $id_articulo, $id_cliente);
$stmtChk->execute();
$resChk = $stmtChk->get_result();

if ($resChk->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Ya has reseñado este producto.']);
    exit;
}

// insertar reseña
$sqlInsert = "INSERT INTO resenas (id_pedido, id_cliente, id_articulo, calificacion, comentario)
              VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param("iiiis", $id_pedido, $id_cliente, $id_articulo, $calificacion, $comentario);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Reseña guardada correctamente.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error al guardar reseña.']);
}
?>
