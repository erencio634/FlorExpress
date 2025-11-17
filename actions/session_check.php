<?php
session_start();

// Verificar si el usuario está logueado y es una florería
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'floreria') {
    header("Location: login.php");
    exit();
}

// Obtener información de la florería
require_once 'conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$stmt = $conexion->prepare("SELECT * FROM florerias WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);
$floreria = $stmt->fetch();

if (!$floreria) {
    die("Error: No se encontró información de la florería");
}

$id_floreria = $floreria['id_floreria'];
$nombre_floreria = $floreria['nombre_floreria'];
?>
