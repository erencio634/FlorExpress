<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Sesión no válida']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_direccion = $_POST['id_direccion'] ?? null;
$id_pago = $_POST['id_metodo'] ?? null;

if (!$id_direccion || !$id_pago) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos del pedido']);
    exit;
}

// Obtener id_cliente
$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmt = $conn->prepare($sqlCliente);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();

if (!$cliente) {
    echo json_encode(['status' => 'error', 'message' => 'Cliente no encontrado']);
    exit;
}

$id_cliente = $cliente['id_cliente'];

// Productos del carrito
$sqlCarrito = "SELECT id_articulo, cantidad FROM carrito WHERE id_cliente = ?";
$stmt = $conn->prepare($sqlCarrito);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resCar = $stmt->get_result();

if ($resCar->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Tu carrito está vacío.']);
    exit;
}

$conn->begin_transaction();

try {
    $total = 0;
    $productos = [];

    while ($row = $resCar->fetch_assoc()) {
        $id_art = $row['id_articulo'];
        $cant = $row['cantidad'];
        $precioRes = $conn->query("SELECT precio FROM catalogo WHERE id_articulo = $id_art")->fetch_assoc();
        $subtotal = $precioRes['precio'] * $cant;
        $total += $subtotal;
        $productos[] = ['id' => $id_art, 'cantidad' => $cant];
    }

    // Insertar pedido
    $sqlPedido = "INSERT INTO pedidos (fecha_pedido, estado, estado_rastreo, total, id_cliente, id_direccion, id_pago)
                  VALUES (NOW(), 'confirmado', 'Preparando', ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlPedido);
    $stmt->bind_param("diii", $total, $id_cliente, $id_direccion, $id_pago);
    $stmt->execute();
    $id_pedido = $conn->insert_id;

    // =========================================
// NOTIFICACIÓN AUTOMÁTICA AL CONFIRMAR PEDIDO
// =========================================
if (isset($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $titulo_notif = "Pedido confirmado";
    $mensaje_notif = "Tu pedido #FE-$id_pedido ha sido registrado exitosamente y está siendo preparado para envío.";
    $tipo_notif = "pedido";

    $stmtNotif = $conn->prepare("
        INSERT INTO notificaciones (id_usuario, tipo, titulo, mensaje)
        VALUES (?, ?, ?, ?)
    ");
    $stmtNotif->bind_param("isss", $id_usuario, $tipo_notif, $titulo_notif, $mensaje_notif);
    $stmtNotif->execute();
}


// ============================================
// Insertar detalles del pedido
// ============================================
$stmtDet = $conn->prepare("
    INSERT INTO detalles_pedido (id_pedido, id_articulo, cantidad, precio_unitario)
    VALUES (?, ?, ?, ?)
");


foreach ($productos as $p) {
    // obtener precio actual del artículo
    $precioRes = $conn->query("SELECT precio, id_floreria FROM catalogo WHERE id_articulo = {$p['id']}")->fetch_assoc();
    $precio = $precioRes ? $precioRes['precio'] : 0;
    $id_floreria = $precioRes ? $precioRes['id_floreria'] : null;

    // ahora solo mandamos lo que sí se puede insertar
    $stmtDet->bind_param("iiid", $id_pedido, $p['id'], $p['cantidad'], $precio);
    $stmtDet->execute();

    // opcionalmente puedes actualizar después el id_floreria
    if ($id_floreria) {
        $id_detalle = $conn->insert_id;
        $conn->query("UPDATE detalles_pedido SET id_floreria = $id_floreria WHERE id_detalle = $id_detalle");
    }
}


    // Vaciar carrito
    $conn->query("DELETE FROM carrito WHERE id_cliente = $id_cliente");

    $conn->commit();
    echo json_encode(['status' => 'ok', 'message' => 'Pedido generado con éxito']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Error al registrar el pedido: ' . $e->getMessage()]);
}
?>
