<?php
require_once "../conexion.php";

$comision = $_POST['comision'];
$tiempo_maximo = $_POST['tiempo_maximo'];
$radio_cobertura = $_POST['radio_cobertura'];
$modo_mantenimiento = isset($_POST['modo_mantenimiento']) ? 1 : 0;

$conn->query("UPDATE configuraciones SET 
    comision = $comision,
    tiempo_maximo = $tiempo_maximo,
    radio_cobertura = $radio_cobertura,
    modo_mantenimiento = $modo_mantenimiento,
    actualizado_en = NOW()
    WHERE id = 1
");

header("Location: ../dashboard_admin.php?config=ok");
exit;
?>
