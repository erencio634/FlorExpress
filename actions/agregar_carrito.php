<?php
// actions/agregar_carrito.php
declare(strict_types=1);
ob_start();
session_start();
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');

function json_out(array $payload) {
    if (ob_get_length()) { ob_clean(); }
    echo json_encode($payload);
    exit;
}

// 1) Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    json_out(['status' => 'not_logged', 'message' => 'No hay sesión activa.']);
}

$id_usuario = (int)$_SESSION['id_usuario'];

// 2) Validar entrada
$id_articulo = filter_input(INPUT_POST, 'id_articulo', FILTER_VALIDATE_INT);
if (!$id_articulo || $id_articulo <= 0) {
    json_out(['status' => 'invalid', 'message' => 'Artículo inválido.']);
}

// 3) Resolver id_cliente
$sqlCli = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmtCli = $conn->prepare($sqlCli);
if (!$stmtCli) {
    json_out(['status' => 'db_error', 'message' => 'Error preparando consulta de cliente.']);
}
$stmtCli->bind_param('i', $id_usuario);
$stmtCli->execute();
$resCli = $stmtCli->get_result();
$cli = $resCli->fetch_assoc();
if (!$cli) {
    json_out(['status' => 'invalid', 'message' => 'No existe perfil de cliente.']);
}
$id_cliente = (int)$cli['id_cliente'];

// 4) Verificar si ya existe el producto en carrito
$sqlCheck = "SELECT id_carrito, cantidad FROM carrito WHERE id_cliente = ? AND id_articulo = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param('ii', $id_cliente, $id_articulo);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck && $resCheck->num_rows > 0) {
    // Ya existe → aumentar cantidad
    $row = $resCheck->fetch_assoc();
    $id_carrito = (int)$row['id_carrito'];
    $nuevaCantidad = (int)$row['cantidad'] + 1;

    $sqlUpdate = "UPDATE carrito SET cantidad = ? WHERE id_carrito = ?";
    $stmtUpd = $conn->prepare($sqlUpdate);
    $stmtUpd->bind_param('ii', $nuevaCantidad, $id_carrito);
    $stmtUpd->execute();

    if ($stmtUpd->affected_rows >= 0) {
        json_out(['status' => 'updated', 'message' => 'Cantidad aumentada (+1).']);
    } else {
        json_out(['status' => 'db_error', 'message' => 'No se pudo actualizar la cantidad.']);
    }
} else {
    // No existe → insertar nuevo
    $sqlIns = "INSERT INTO carrito (id_cliente, id_articulo, cantidad, fecha_agregado)
               VALUES (?, ?, 1, NOW())";
    $stmtIns = $conn->prepare($sqlIns);
    $stmtIns->bind_param('ii', $id_cliente, $id_articulo);
    $ok = $stmtIns->execute();

    if ($ok && $stmtIns->affected_rows === 1) {
        json_out(['status' => 'ok', 'message' => 'Producto agregado al carrito.']);
    } else {
        json_out(['status' => 'db_error', 'message' => 'No se pudo agregar el producto.']);
    }
}
?>
