<?php
session_start();
require_once '../conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'floreria') {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_floreria = $_POST['nombre_floreria'] ?? '';
$telefono = $_POST['telefono'] ?? '';
$direccion = $_POST['direccion'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';
$capacidad_diaria = $_POST['capacidad_diaria'] ?? 10;
$estatus = $_POST['estatus'] ?? 'activa';

$sql_update = "UPDATE florerias 
               SET nombre_floreria = ?, telefono = ?, direccion_floreria = ?, 
                   descripcion = ?, capacidad_diaria = ?, estatus = ?
               WHERE id_usuario = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("ssssisi", $nombre_floreria, $telefono, $direccion, $descripcion, $capacidad_diaria, $estatus, $id_usuario);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Información actualizada exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la información']);
}

$conn->close();
?>
