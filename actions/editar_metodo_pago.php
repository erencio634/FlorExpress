<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

$id_metodo = $_POST['id_metodo'] ?? 0;
$tipo = $_POST['tipo'] ?? '';
$alias = $_POST['alias'] ?? '';
$titular = $_POST['titular'] ?? '';
$ultimos4 = $_POST['ultimos4'] ?? '';
$expiracion = $_POST['expiracion'] ?? '';
$es_principal = isset($_POST['es_principal']) ? 1 : 0;

if (!$id_metodo) {
    echo json_encode(['status'=>'error','message'=>'ID de método no válido']);
    exit;
}

// Obtener el id_cliente del usuario actual
$id_usuario = $_SESSION['id_usuario'];
$stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();
$id_cliente = $cliente['id_cliente'] ?? 0;

if (!$id_cliente) {
    echo json_encode(['status'=>'error','message'=>'Cliente no encontrado']);
    exit;
}

// Si lo marcó como principal, desactivar los demás
if ($es_principal) {
    $conn->query("UPDATE metodos_pago_cliente SET es_principal = 0 WHERE id_cliente = $id_cliente");
}

// Actualizar el método
$sql = "UPDATE metodos_pago_cliente 
        SET tipo = ?, alias = ?, titular = ?, ultimos4 = ?, expiracion = ?, es_principal = ?
        WHERE id_metodo = ? AND id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssiii", $tipo, $alias, $titular, $ultimos4, $expiracion, $es_principal, $id_metodo, $id_cliente);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Método actualizado correctamente']);
} else {
    echo json_encode(['status'=>'error','message'=>'Error al actualizar método']);
}
