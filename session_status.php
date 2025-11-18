<?php
session_start();

// Siempre devolver JSON
header('Content-Type: application/json');

echo json_encode([
    "logged_in" => isset($_SESSION['id_usuario']),
    "rol" => $_SESSION['rol'] ?? null
]);
