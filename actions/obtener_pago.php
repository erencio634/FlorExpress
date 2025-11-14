<?php
session_start();
include('../conexion.php');

$id = $_GET['id'] ?? 0;

$sql = "SELECT * FROM metodos_pago_cliente WHERE id_metodo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(['status'=>'error','message'=>'Método no encontrado']);
    exit;
}

$data = $res->fetch_assoc();
echo json_encode(['status'=>'ok','data'=>$data]);
