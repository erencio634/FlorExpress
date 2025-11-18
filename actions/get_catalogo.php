<?php
require_once '../conexion.php';

$sql = "SELECT id_articulo, nombre_articulo, precio, imagen_principal 
        FROM catalogo";

$result = $conn->query($sql);
$data = [];

while($row = $result->fetch_assoc()){

    // Si no hay imagen, usa un placeholder
    if (!$row['imagen_principal'] || $row['imagen_principal'] == "") {
        $row['imagen_principal'] = 'img/fallback.jpg';
    }

    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
