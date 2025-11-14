<?php
// actions/gestionar_usuarios.php
require_once("../conexion.php");

// Seguridad básica
function v($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

$accion = v('accion');

if ($accion === 'agregar') {
    $correo     = v('correo');
    $contrasena = v('contrasena'); // Nota: por ahora plano para compatibilidad con tus datos
    $rol        = v('rol', 'cliente');
    $activo     = isset($_POST['activo']) ? 1 : 0;

    $sql = "INSERT INTO usuarios_globales (correo, contrasena, rol, fecha_registro, activo)
            VALUES (?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $correo, $contrasena, $rol, $activo);
    $ok = $stmt->execute();
    $stmt->close();

    header("Location: ../dashboard_admin.php?sec=usuarios&msg=" . ($ok ? "creado" : "error"));
    exit;
}

if ($accion === 'editar') {
    $id_usuario = intval(v('id_usuario'));
    $correo     = v('correo');
    $rol        = v('rol', 'cliente');
    $activo     = isset($_POST['activo']) ? 1 : 0;
    $contrasena = v('contrasena'); // si viene vacío, no se actualiza

    if ($contrasena === '' || $contrasena === null) {
        $sql = "UPDATE usuarios_globales
                SET correo = ?, rol = ?, activo = ?
                WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $correo, $rol, $activo, $id_usuario);
    } else {
        $sql = "UPDATE usuarios_globales
                SET correo = ?, contrasena = ?, rol = ?, activo = ?
                WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssii", $correo, $contrasena, $rol, $activo, $id_usuario);
    }

    $ok = $stmt->execute();
    $stmt->close();

    header("Location: ../dashboard_admin.php?sec=usuarios&msg=" . ($ok ? "actualizado" : "error"));
    exit;
}

if ($accion === 'eliminar') {
    $id_usuario = intval(v('id_usuario'));
    $sql = "DELETE FROM usuarios_globales WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $ok = $stmt->execute();
    $stmt->close();

    header("Location: ../dashboard_admin.php?sec=usuarios&msg=" . ($ok ? "eliminado" : "error"));
    exit;
}

// Si llega algo no esperado
header("Location: ../dashboard_admin.php?sec=usuarios&msg=accion_no_valida");
exit;
