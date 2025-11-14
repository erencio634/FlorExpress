<?php
require_once("../conexion.php");

// Función auxiliar para validar y limpiar datos
function v($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// Función para subir imágenes
function subirImagen($fileInputName, $carpetaDestino = '../uploads/florerias/') {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    
    if ($_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Crear carpeta si no existe
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0755, true);
    }
    
    $extension = pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
    $rutaCompleta = $carpetaDestino . $nombreArchivo;
    
    if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $rutaCompleta)) {
        return '/uploads/florerias/' . $nombreArchivo;
    }
    
    return null;
}

$accion = v('accion');

// ========================================
// AGREGAR FLORERÍA
// ========================================
if ($accion === 'agregar') {
    $id_usuario = v('id_usuario') ?: null;
    $nombre_floreria = v('nombre_floreria');
    $descripcion = v('descripcion');
    $correo_contacto = v('correo_contacto');
    $telefono = v('telefono');
    $direccion_floreria = v('direccion_floreria');
    $estado = v('estado');
    $municipio = v('municipio');
    $longitud = v('longitud') ?: null;
    $latitud = v('latitud') ?: null;
    $estatus = v('estatus', 'activa');
    $capacidad_diaria = intval(v('capacidad_diaria', 25));
    
    // Subir imágenes
    $foto_perfil_f = subirImagen('foto_perfil_f');
    $foto_floreria = subirImagen('foto_floreria');
    
    $sql = "INSERT INTO florerias (
                id_usuario, nombre_floreria, foto_perfil_f, foto_floreria, 
                descripcion, correo_contacto, telefono, direccion_floreria, 
                estado, municipio, longitud, latitud, estatus, 
                capacidad_diaria, pedidos_actuales, fecha_creacion
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssssssssis",
        $id_usuario,
        $nombre_floreria,
        $foto_perfil_f,
        $foto_floreria,
        $descripcion,
        $correo_contacto,
        $telefono,
        $direccion_floreria,
        $estado,
        $municipio,
        $longitud,
        $latitud,
        $estatus,
        $capacidad_diaria
    );
    
    $ok = $stmt->execute();
    $stmt->close();
    
    header("Location: ../dashboard_admin.php?sec=florerias&msg=" . ($ok ? "floreria_creada" : "error"));
    exit;
}

// ========================================
// EDITAR FLORERÍA
// ========================================
if ($accion === 'editar') {
    $id_floreria = intval(v('id_floreria'));
    $id_usuario = v('id_usuario') ?: null;
    $nombre_floreria = v('nombre_floreria');
    $descripcion = v('descripcion');
    $correo_contacto = v('correo_contacto');
    $telefono = v('telefono');
    $direccion_floreria = v('direccion_floreria');
    $estado = v('estado');
    $municipio = v('municipio');
    $longitud = v('longitud') ?: null;
    $latitud = v('latitud') ?: null;
    $estatus = v('estatus', 'activa');
    $capacidad_diaria = intval(v('capacidad_diaria', 25));
    
    // Verificar si hay nuevas imágenes
    $foto_perfil_f = subirImagen('foto_perfil_f');
    $foto_floreria = subirImagen('foto_floreria');
    
    // Si no se subieron nuevas imágenes, mantener las actuales
    if ($foto_perfil_f === null && $foto_floreria === null) {
        $sql = "UPDATE florerias 
                SET id_usuario = ?, nombre_floreria = ?, descripcion = ?, 
                    correo_contacto = ?, telefono = ?, direccion_floreria = ?, 
                    estado = ?, municipio = ?, longitud = ?, latitud = ?, 
                    estatus = ?, capacidad_diaria = ?
                WHERE id_floreria = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "isssssssssisi",
            $id_usuario,
            $nombre_floreria,
            $descripcion,
            $correo_contacto,
            $telefono,
            $direccion_floreria,
            $estado,
            $municipio,
            $longitud,
            $latitud,
            $estatus,
            $capacidad_diaria,
            $id_floreria
        );
    } else {
        // Construir consulta dinámica según las imágenes subidas
        $camposUpdate = [
            "id_usuario = ?",
            "nombre_floreria = ?",
            "descripcion = ?",
            "correo_contacto = ?",
            "telefono = ?",
            "direccion_floreria = ?",
            "estado = ?",
            "municipio = ?",
            "longitud = ?",
            "latitud = ?",
            "estatus = ?",
            "capacidad_diaria = ?"
        ];
        
        $tipos = "issssssssssi";
        $valores = [
            $id_usuario,
            $nombre_floreria,
            $descripcion,
            $correo_contacto,
            $telefono,
            $direccion_floreria,
            $estado,
            $municipio,
            $longitud,
            $latitud,
            $estatus,
            $capacidad_diaria
        ];
        
        if ($foto_perfil_f !== null) {
            $camposUpdate[] = "foto_perfil_f = ?";
            $tipos .= "s";
            $valores[] = $foto_perfil_f;
        }
        
        if ($foto_floreria !== null) {
            $camposUpdate[] = "foto_floreria = ?";
            $tipos .= "s";
            $valores[] = $foto_floreria;
        }
        
        $tipos .= "i";
        $valores[] = $id_floreria;
        
        $sql = "UPDATE florerias SET " . implode(", ", $camposUpdate) . " WHERE id_floreria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($tipos, ...$valores);
    }
    
    $ok = $stmt->execute();
    $stmt->close();
    
    header("Location: ../dashboard_admin.php?sec=florerias&msg=" . ($ok ? "floreria_actualizada" : "error"));
    exit;
}

// ========================================
// ELIMINAR FLORERÍA
// ========================================
if ($accion === 'eliminar') {
    $id_floreria = intval(v('id_floreria'));
    
    // Opcionalmente eliminar las imágenes del servidor
    $sqlImg = "SELECT foto_perfil_f, foto_floreria FROM florerias WHERE id_floreria = ?";
    $stmtImg = $conn->prepare($sqlImg);
    $stmtImg->bind_param("i", $id_floreria);
    $stmtImg->execute();
    $resultImg = $stmtImg->get_result();
    
    if ($row = $resultImg->fetch_assoc()) {
        if (!empty($row['foto_perfil_f']) && file_exists('../' . $row['foto_perfil_f'])) {
            unlink('../' . $row['foto_perfil_f']);
        }
        if (!empty($row['foto_floreria']) && file_exists('../' . $row['foto_floreria'])) {
            unlink('../' . $row['foto_floreria']);
        }
    }
    $stmtImg->close();
    
    // Eliminar la florería
    $sql = "DELETE FROM florerias WHERE id_floreria = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_floreria);
    $ok = $stmt->execute();
    $stmt->close();
    
    header("Location: ../dashboard_admin.php?sec=florerias&msg=" . ($ok ? "floreria_eliminada" : "error"));
    exit;
}

// Si llega algo no esperado
header("Location: ../dashboard_admin.php?sec=florerias&msg=accion_no_valida");
exit;
?>
