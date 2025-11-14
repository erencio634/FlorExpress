<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo 'sin_sesion';
    exit;
}

$id_direccion = $_POST['id_direccion'] ?? null;
$nombre = $_POST['nombre_receptor'] ?? '';
$apellidos = $_POST['apellidos_receptor'] ?? '';
$telefono = $_POST['telefono_receptor'] ?? '';
$calle = $_POST['calle'] ?? '';
$colonia = $_POST['colonia'] ?? '';
$municipio = $_POST['municipio'] ?? '';
$estado = $_POST['estado'] ?? '';
$codigo_postal = $_POST['codigo_postal'] ?? '';

if (!$id_direccion || empty($nombre) || empty($telefono) || empty($calle)) {
    echo 'faltan_datos';
    exit;
}

$sql = "UPDATE direcciones 
        SET nombre_receptor=?, apellidos_receptor=?, telefono_receptor=?, 
            calle=?, colonia=?, municipio=?, estado=?, codigo_postal=? 
        WHERE id_direccion=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssssi",
    $nombre,
    $apellidos,
    $telefono,
    $calle,
    $colonia,
    $municipio,
    $estado,
    $codigo_postal,
    $id_direccion
);

echo $stmt->execute() ? 'ok' : 'error';
$conn->close();
