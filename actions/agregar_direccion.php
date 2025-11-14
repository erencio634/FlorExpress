<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status'=>'error','message'=>'Sesión no válida']);
    exit;
}

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

$campos = [
    'nombre_receptor','apellidos_receptor','telefono_receptor','codigo_postal',
    'estado','municipio','colonia','calle','referencias'
];
$data = [];
foreach($campos as $c) {
    $data[$c] = $_POST[$c] ?? '';
}

$sql = "INSERT INTO direcciones 
(id_cliente, nombre_receptor, apellidos_receptor, telefono_receptor, codigo_postal, estado, municipio, colonia, calle, referencias)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssisssss", $id_cliente, 
    $data['nombre_receptor'], $data['apellidos_receptor'], $data['telefono_receptor'],
    $data['codigo_postal'], $data['estado'], $data['municipio'], $data['colonia'], 
    $data['calle'], $data['referencias']
);

if ($stmt->execute()) {
    echo json_encode(['status'=>'ok','message'=>'Dirección agregada correctamente']);
} else {
    echo json_encode(['status'=>'error','message'=>'Error al agregar']);
}
