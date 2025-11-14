<?php
session_start();
require_once "../conexion.php";

// validar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    echo '<div class="text-gray-500 text-sm">Sesión no activa.</div>';
    exit;
}

// obtener id_cliente desde usuarios_globales
$id_usuario = $_SESSION['id_usuario'];
$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmtC = $conn->prepare($sqlCliente);
$stmtC->bind_param("i", $id_usuario);
$stmtC->execute();
$resC = $stmtC->get_result();
$cliente = $resC->fetch_assoc();

if (!$cliente) {
    echo '<div class="text-gray-500 text-sm">Cliente no encontrado.</div>';
    exit;
}

$id_cliente = $cliente['id_cliente'];

// ===============================
// pedidos entregados sin reseña
// ===============================
$sql = "
SELECT 
    p.id_pedido,
    dp.id_articulo,
    c.nombre_articulo,
    p.fecha_pedido,
    p.total
FROM pedidos p
JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
JOIN catalogo c ON dp.id_articulo = c.id_articulo
LEFT JOIN resenas r 
    ON r.id_pedido = p.id_pedido 
    AND r.id_articulo = dp.id_articulo
    AND r.id_cliente = p.id_cliente
WHERE 
    p.id_cliente = ?
    AND p.estado = 'entregado'
    AND r.id_resena IS NULL
ORDER BY p.fecha_pedido DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo '<div class="text-gray-500 text-sm">No tienes pedidos pendientes de reseña.</div>';
    exit;
}

while ($row = $res->fetch_assoc()) {
    echo '
    <div class="bg-white rounded-lg shadow-md p-4 mb-4 border border-gray-100">
        <div class="flex justify-between items-center mb-2">
            <h4 class="font-semibold text-verde-hoja">Pedido #FE-' . htmlspecialchars($row['id_pedido']) . '</h4>
            <span class="text-sm text-gray-500">' . htmlspecialchars($row['fecha_pedido']) . '</span>
        </div>
        <p class="text-gray-700 text-sm mb-2">
            Producto: <strong>' . htmlspecialchars($row['nombre_articulo']) . '</strong><br>
            Total: $' . htmlspecialchars(number_format($row['total'], 2)) . ' MXN
        </p>
        <button 
            class="btn-agregar-reseña bg-verde-hoja text-white px-3 py-1 rounded-md text-sm hover:bg-green-600"
            data-idpedido="' . $row['id_pedido'] . '" 
            data-idarticulo="' . $row['id_articulo'] . '">
            Agregar reseña
        </button>
    </div>';
}
?>
