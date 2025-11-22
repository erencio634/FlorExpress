<?php
require_once '../conexion.php';

$sql = "SELECT id_articulo, nombre_articulo, precio, imagen_principal 
        FROM catalogo";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {

    if (!$row['imagen_principal'] || $row['imagen_principal'] == "") {
        $row['imagen_principal'] = 'img/fallback.jpg';
    }

    // ❗ NO modificar rutas aquí
    // La ruta ya es correcta (uploads/catalogo/nombre.png)

    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);

