<?php
// ARCHIVO TEMPORAL SOLO PARA PRUEBAS
// Eliminar en producción

session_start();

// Simular login de una florería
// Usa el id_usuario = 3 que corresponde a "rosabella@floreria.mx" en tu base de datos
$_SESSION['id_usuario'] = 3;
$_SESSION['rol'] = 'floreria';

// Redirigir al dashboard
header("Location: dashboard_floreria.php");
exit();
?>
