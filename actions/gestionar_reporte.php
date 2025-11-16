<?php
require_once "../conexion.php";

$id_reporte = $_POST['id_reporte'];
$accion = $_POST['accion'];

if ($accion === "revisar") {
    $conn->query("UPDATE reportes SET estatus = 'Revisado' WHERE id_reporte = $id_reporte");
} else if ($accion === "descartar") {
    $conn->query("UPDATE reportes SET estatus = 'Descartado' WHERE id_reporte = $id_reporte");
}

header("Location: ../dashboard_admin.php?msg=reporte_ok");
exit;
