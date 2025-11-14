<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo 'sin_sesion';
    exit;
}

$id_pago = $_POST['id_pago'] ?? null;
$alias = $_POST['alias'] ?? '';
$titular = $_POST['titular'] ?? '';
$expiracion = $_POST['expiracion'] ?? '';
$principal = isset($_POST['principal']) ? 1 : 0;

if (!$id_pago) {
    echo 'faltan_datos';
    exit;
}

$sql = "UPDATE metodos_pago_cliente SET alias=?, titular=?, expiracion=?, es_principal=? WHERE id_pago=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $alias, $titular, $expiracion, $principal, $id_pago);
echo $stmt->execute() ? 'ok' : 'error';
$conn->close();
