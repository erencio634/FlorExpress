<?php
// Datos de conexión (ajústalos si tu base se llama diferente)
$servername = "srv734.hstgr.io";
$username = "u593063890_flor_express";
$password = "F!0rExpr3ss#2025\$Db"; // En MAMP el usuario y contraseña por defecto son root / root
$database = "u593063890_flor_express";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer codificación de caracteres
$conn->set_charset("utf8mb4");

// Si quieres confirmar en consola del servidor que todo va bien, descomenta esta línea:
// echo "Conexión exitosa a la base de datos";
?>
