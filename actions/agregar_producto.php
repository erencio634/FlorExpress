<?php
session_start();
require_once("../conexion.php");

if (!isset($_SESSION["id_floreria"])) {
    die("no_sesion");
}

$id_floreria = $_SESSION["id_floreria"];
$nombre = $_POST["nombre_articulo"] ?? '';
$precio = $_POST["precio"] ?? '';
$descripcion = $_POST["descripcion"] ?? '';
$categoria = $_POST["categoria"] ?? '';

if (empty($nombre) || empty($precio) || empty($descripcion) || empty($categoria)) {
    die("campos_vacios");
}

// Asegura que la carpeta exista
$carpeta = "../uploads/catalogo/";
if (!file_exists($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Verifica imagen
if (!empty($_FILES["imagen_principal"]["name"])) {
    $nombreImagen = time() . "_" . basename($_FILES["imagen_principal"]["name"]);
    $rutaDestino = $carpeta . $nombreImagen;

    if (move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $rutaDestino)) {
        $rutaRelativa = "uploads/catalogo/" . $nombreImagen;
    } else {
        die("error_subida_imagen");
    }
} else {
    die("imagen_vacia");
}

// Inserta producto básico
$sql = "INSERT INTO catalogo (id_floreria, nombre_articulo, descripcion, categoria, precio, imagen_principal, fecha_agregado)
        VALUES (?, ?, ?, ?, ?, ?, NOW())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isssds", $id_floreria, $nombre, $descripcion, $categoria, $precio, $rutaRelativa);

if ($stmt->execute()) {
    header("Location: ../dashboard_floreria.php");
    exit();
} else {
    echo "error_db";
}
?>
