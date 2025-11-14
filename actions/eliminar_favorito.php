<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo 'sin_sesion';
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_articulo = $_POST['id_articulo'] ?? null;

if (!$id_articulo) {
    echo 'faltan_datos';
    exit;
}

// Obtener id_cliente
$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmt = $conn->prepare($sqlCliente);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();

if (!$cliente) {
    echo 'no_cliente';
    exit;
}

$id_cliente = $cliente['id_cliente'];

// Eliminar de favoritos
$sqlDel = "DELETE FROM favoritos WHERE id_cliente = ? AND id_articulo = ?";
$stmtD = $conn->prepare($sqlDel);
$stmtD->bind_param("ii", $id_cliente, $id_articulo);
echo $stmtD->execute() ? 'ok' : 'error';
$conn->close();
