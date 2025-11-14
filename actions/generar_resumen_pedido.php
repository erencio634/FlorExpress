<?php
include('../conexion.php');
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "<p class='text-red-500'>Sesión no válida.</p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener id_cliente
$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmt = $conn->prepare($sqlCliente);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();

if (!$cliente) {
    echo "<p class='text-red-500'>Cliente no encontrado.</p>";
    exit;
}

$id_cliente = $cliente['id_cliente'];

// ==============================
// DIRECCIONES
// ==============================
$sqlDir = "SELECT * FROM direcciones WHERE id_cliente = ?";
$stmt = $conn->prepare($sqlDir);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resDir = $stmt->get_result();

$direccionesHTML = "";
if ($resDir->num_rows > 0) {
    while ($d = $resDir->fetch_assoc()) {
        $direccionesHTML .= "
            <label class='flex items-start space-x-3 border p-3 rounded-lg cursor-pointer hover:bg-gray-50'>
                <input type='radio' name='direccion_predeterminada' value='{$d['id_direccion']}' class='mt-1'>
                <div>
                    <p class='font-semibold'>{$d['nombre_receptor']} {$d['apellidos_receptor']}</p>
                    <p class='text-sm text-gray-600'>{$d['calle']}, {$d['colonia']}, {$d['municipio']}, {$d['estado']} ({$d['codigo_postal']})</p>
                    <p class='text-xs text-gray-500'>Tel: {$d['telefono_receptor']}</p>
                </div>
            </label>
        ";
    }
} else {
    $direccionesHTML = "<p class='text-gray-500 text-sm'>No tienes direcciones guardadas.</p>";
}

// ==============================
// MÉTODOS DE PAGO
// ==============================
$sqlPago = "SELECT * FROM metodos_pago_cliente WHERE id_cliente = ?";
$stmt = $conn->prepare($sqlPago);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resPago = $stmt->get_result();

$pagosHTML = "";
if ($resPago->num_rows > 0) {
    while ($p = $resPago->fetch_assoc()) {
        $detalle = "";
        if ($p['tipo'] === 'tarjeta') {
            $detalle = "•••• {$p['ultimos4']} (Expira {$p['expiracion']})";
        } elseif ($p['tipo'] === 'paypal') {
            $detalle = "Cuenta PayPal vinculada";
        } elseif ($p['tipo'] === 'oxxo' || $p['tipo'] === 'efectivo') {
            $detalle = "Pago en tienda / efectivo";
        }

        $pagosHTML .= "
            <label class='flex items-start space-x-3 border p-3 rounded-lg cursor-pointer hover:bg-gray-50'>
                <input type='radio' name='metodo_predeterminado' value='{$p['id_metodo']}' class='mt-1'>
                <div>
                    <p class='font-semibold capitalize'>{$p['tipo']} - {$p['alias']}</p>
                    <p class='text-sm text-gray-600'>{$detalle}</p>
                </div>
            </label>
        ";
    }
} else {
    $pagosHTML = "<p class='text-gray-500 text-sm'>No tienes métodos de pago guardados.</p>";
}

// ==============================
// PRODUCTOS EN CARRITO
// ==============================
$sqlCarrito = "SELECT c2.nombre_articulo, c2.precio, car.cantidad
               FROM carrito car
               JOIN catalogo c2 ON car.id_articulo = c2.id_articulo
               WHERE car.id_cliente = ?";
$stmt = $conn->prepare($sqlCarrito);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$resCar = $stmt->get_result();

$total = 0;
$productosHTML = "";
while ($row = $resCar->fetch_assoc()) {
    $subtotal = $row['precio'] * $row['cantidad'];
    $total += $subtotal;
    $productosHTML .= "<li>{$row['nombre_articulo']} (x{$row['cantidad']}) - $" . number_format($subtotal, 2) . "</li>";
}

if (!$productosHTML) {
    echo "<p class='text-gray-500'>Tu carrito está vacío.</p>";
    exit;
}

// ==============================
// OUTPUT FINAL
// ==============================
echo "
<h4 class='font-semibold text-lg mb-2'>Productos del Pedido</h4>
<ul class='list-disc pl-6 mb-4'>$productosHTML</ul>
<p class='font-semibold text-right'>Total: $" . number_format($total, 2) . " MXN</p>

<hr class='my-4'>

<h4 class='font-semibold mb-2'>Selecciona Dirección de Envío</h4>
<div class='space-y-2 mb-4'>$direccionesHTML</div>

<hr class='my-4'>

<h4 class='font-semibold mb-2'>Selecciona Método de Pago</h4>
<div class='space-y-2'>$pagosHTML</div>
";
?>
