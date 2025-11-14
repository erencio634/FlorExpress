<?php
require_once("../conexion.php");

$accion = $_POST["accion"] ?? '';

// ==========================
// AGREGAR PRODUCTO
// ==========================
if ($accion === "agregar") {
    $nombre = $_POST["nombre_articulo"] ?? '';
    $descripcion = $_POST["descripcion"] ?? '';
    $categoria = $_POST["categoria"] ?? '';
    $precio = $_POST["precio"] ?? 0;
    $id_floreria = $_POST["id_floreria"] ?? 1; // valor por defecto si no hay sesión

    if (empty($nombre) || empty($descripcion) || empty($categoria) || empty($precio)) {
        die("campos_vacios");
    }

    // Crear carpeta si no existe
    $carpeta = "../uploads/catalogo/";
    if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

    $rutaRelativa = "";
    if (!empty($_FILES["imagen_principal"]["name"])) {
        $nombreImagen = time() . "_" . basename($_FILES["imagen_principal"]["name"]);
        $rutaDestino = $carpeta . $nombreImagen;
        if (move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $rutaDestino)) {
            $rutaRelativa = "uploads/catalogo/" . $nombreImagen;
        }
    }

    $sql = "INSERT INTO catalogo (id_floreria, nombre_articulo, descripcion, categoria, precio, imagen_principal, estado, visible, fecha_creacion)
            VALUES (?, ?, ?, ?, ?, ?, 'activo', 1, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssds", $id_floreria, $nombre, $descripcion, $categoria, $precio, $rutaRelativa);
    $stmt->execute();

    header("Location: ../dashboard_admin.php?msg=agregado");
    exit();
}

// ==========================
// ELIMINAR PRODUCTO
// ==========================
if ($accion === "eliminar") {
    $id = $_POST["id_articulo"] ?? 0;
    if (empty($id)) die("id_vacio");

    $sql = "DELETE FROM catalogo WHERE id_articulo=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: ../dashboard_admin.php?msg=eliminado");
    exit();
}

// ==========================
// EDITAR PRODUCTO
// ==========================
if ($accion === "editar") {
    $id = $_POST["id_articulo"] ?? 0;
    $nombre = $_POST["nombre_articulo"] ?? '';
    $descripcion = $_POST["descripcion"] ?? '';
    $categoria = $_POST["categoria"] ?? '';
    $precio = $_POST["precio"] ?? 0;

    if (empty($id) || empty($nombre) || empty($descripcion) || empty($categoria) || empty($precio)) {
        die("campos_vacios");
    }

    $rutaRelativa = null;
    if (!empty($_FILES["imagen_principal"]["name"])) {
        $carpeta = "../uploads/catalogo/";
        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

        $nombreImagen = time() . "_" . basename($_FILES["imagen_principal"]["name"]);
        $rutaDestino = $carpeta . $nombreImagen;
        if (move_uploaded_file($_FILES["imagen_principal"]["tmp_name"], $rutaDestino)) {
            $rutaRelativa = "uploads/catalogo/" . $nombreImagen;
        }
    }

    // Si se subió una nueva imagen
    if ($rutaRelativa) {
        $sql = "UPDATE catalogo 
                SET nombre_articulo=?, descripcion=?, categoria=?, precio=?, imagen_principal=? 
                WHERE id_articulo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdsi", $nombre, $descripcion, $categoria, $precio, $rutaRelativa, $id);
    } 
    // Si no se cambió la imagen
    else {
        $sql = "UPDATE catalogo 
                SET nombre_articulo=?, descripcion=?, categoria=?, precio=? 
                WHERE id_articulo=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $nombre, $descripcion, $categoria, $precio, $id);
    }

    $stmt->execute();
    header("Location: ../dashboard_admin.php?msg=editado");
    exit();
}
?>
