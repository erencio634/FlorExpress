<?php
// Configuración de la base de datos
define('DB_HOST', 'srv734.hstgr.io');
define('DB_NAME', 'u593063890_flor_express');
define('DB_USER', 'u593063890_flor_express'); // Cambia esto según tu configuración
define('DB_PASS', 'F!0rExpr3ss#2025$Db'); // Cambia esto según tu configuración

// Crear conexión con mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8mb4");
?>
