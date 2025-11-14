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
$id_cliente = $cliente['id_cliente'] ?? null;

if (!$id_cliente) {
    echo 'no_cliente';
    exit;
}

// Eliminar el artículo del carrito
$sql = "DELETE FROM carrito WHERE id_cliente = ? AND id_articulo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_cliente, $id_articulo);

echo $stmt->execute() ? 'ok' : 'error';
$conn->close();
