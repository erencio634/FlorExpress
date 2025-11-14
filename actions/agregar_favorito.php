<?php
declare(strict_types=1);
ob_start();
session_start();
require_once __DIR__ . '/../conexion.php';
header('Content-Type: application/json; charset=utf-8');

function json_out(array $payload) {
    if (ob_get_length()) ob_clean();
    echo json_encode($payload);
    exit;
}

// Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    json_out(['status' => 'not_logged', 'message' => 'Inicia sesión para agregar favoritos.']);
}
$id_usuario = (int)$_SESSION['id_usuario'];

// Validar entrada
$id_articulo = filter_input(INPUT_POST, 'id_articulo', FILTER_VALIDATE_INT);
if (!$id_articulo || $id_articulo <= 0) {
    json_out(['status' => 'invalid', 'message' => 'Artículo inválido.']);
}

// Resolver id_cliente
$sqlCli = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmtCli = $conn->prepare($sqlCli);
$stmtCli->bind_param("i", $id_usuario);
$stmtCli->execute();
$resCli = $stmtCli->get_result();
$cli = $resCli->fetch_assoc();
if (!$cli) {
    json_out(['status' => 'invalid', 'message' => 'No existe perfil de cliente.']);
}
$id_cliente = (int)$cli['id_cliente'];

// Revisar si ya es favorito
$sqlCheck = "SELECT id_favorito FROM favoritos WHERE id_cliente = ? AND id_articulo = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $id_cliente, $id_articulo);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) {
    // Si ya está → eliminarlo (toggle)
    $sqlDel = "DELETE FROM favoritos WHERE id_cliente = ? AND id_articulo = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param("ii", $id_cliente, $id_articulo);
    $stmtDel->execute();
    json_out(['status' => 'removed', 'message' => 'Eliminado de favoritos.']);
} else {
    // Si no está → agregar
    $sqlAdd = "INSERT INTO favoritos (id_cliente, id_articulo, fecha_agregado) VALUES (?, ?, NOW())";
    $stmtAdd = $conn->prepare($sqlAdd);
    $stmtAdd->bind_param("ii", $id_cliente, $id_articulo);
    $ok = $stmtAdd->execute();
    if ($ok) {
        json_out(['status' => 'added', 'message' => 'Agregado a favoritos.']);
    } else {
        json_out(['status' => 'db_error', 'message' => 'No se pudo agregar a favoritos.']);
    }
}
