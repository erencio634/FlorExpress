<?php
require_once("../conexion.php");

$accion = $_POST['accion'] ?? '';

if ($accion === 'agregar') {
    $descripcion = $_POST['descripcion'];

    // Validar carpeta de destino
    $dir = "../uploads/ofertas/";
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $nombreArchivo = basename($_FILES['imagen']['name']);
    $rutaImagen = $dir . $nombreArchivo;

    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
        $imagenRuta = "uploads/ofertas/" . $nombreArchivo;
        $stmt = $conn->prepare("INSERT INTO ofertas (descripcion, imagen, fecha_creacion) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $descripcion, $imagenRuta);
        $stmt->execute();
    }

    header("Location: ../dashboard.php");
    exit;
}

if ($accion === 'eliminar') {
    $id = $_POST['id'];
    $conn->query("DELETE FROM ofertas WHERE id = $id");
    header("Location: ../dashboard.php");
    exit;
}
?>
