<?php
session_start();
require_once("../conexion.php");

// Verificar que el usuario sea administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit();
}

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'cancelar':
        cancelarPedido($conn);
        break;
    
    case 'cambiar_estado':
        cambiarEstadoPedido($conn);
        break;
    
    default:
        header("Location: ../dashboard_admin.php");
        exit();
}

function cancelarPedido($conn) {
    $id_pedido = (int)($_POST['id_pedido'] ?? 0);
    
    if ($id_pedido <= 0) {
        $_SESSION['error'] = "ID de pedido inválido";
        header("Location: ../dashboard_admin.php");
        exit();
    }
    
    // Verificar que el pedido existe y no está cancelado
    $stmt = $conn->prepare("SELECT estado FROM pedidos WHERE id_pedido = ?");
    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Pedido no encontrado";
        header("Location: ../dashboard_admin.php");
        exit();
    }
    
    $pedido = $result->fetch_assoc();
    
    if ($pedido['estado'] === 'cancelado') {
        $_SESSION['error'] = "El pedido ya está cancelado";
        header("Location: ../dashboard_admin.php");
        exit();
    }
    
    if ($pedido['estado'] === 'completado') {
        $_SESSION['error'] = "No se puede cancelar un pedido completado";
        header("Location: ../dashboard_admin.php");
        exit();
    }
    
    // Actualizar el estado del pedido a cancelado
    $stmt = $conn->prepare("UPDATE pedidos SET estado = 'cancelado' WHERE id_pedido = ?");
    $stmt->bind_param("i", $id_pedido);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Pedido cancelado exitosamente";
    } else {
        $_SESSION['error'] = "Error al cancelar el pedido: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: ../dashboard_admin.php");
    exit();
}

function cambiarEstadoPedido($conn) {
    $id_pedido = (int)($_POST['id_pedido'] ?? 0);
    $nuevo_estado = $_POST['estado'] ?? '';
    
    if ($id_pedido <= 0) {
        $_SESSION['error'] = "ID de pedido inválido";
        header("Location: ../dashboard_admin.php");
        exit();
    }
    
    $estados_validos = ['pendiente', 'en_proceso', 'completado', 'cancelado'];
    if (!in_array($nuevo_estado, $estados_validos)) {
        $_SESSION['error'] = "Estado inválido";
        header("Location: ../dashboard_admin.php");
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE pedidos SET estado = ? WHERE id_pedido = ?");
    $stmt->bind_param("si", $nuevo_estado, $id_pedido);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Estado del pedido actualizado exitosamente";
    } else {
        $_SESSION['error'] = "Error al actualizar el estado: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: ../dashboard_admin.php");
    exit();
}
?>
