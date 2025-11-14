<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo '<div class="text-gray-500 text-sm">Inicia sesión para ver tu carrito.</div>';
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$sqlCliente = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmtCliente = $conn->prepare($sqlCliente);
$stmtCliente->bind_param("i", $id_usuario);
$stmtCliente->execute();
$resCliente = $stmtCliente->get_result();
$id_cliente = $resCliente->fetch_assoc()['id_cliente'] ?? 0;

if (!$id_cliente) {
    echo '<div class="text-gray-500 text-sm">No se encontró el cliente.</div>';
    exit;
}

$sql = "
    SELECT car.id_carrito, car.id_articulo, car.cantidad,
           c.nombre_articulo, c.precio, c.imagen_principal
    FROM carrito car
    LEFT JOIN catalogo c ON car.id_articulo = c.id_articulo
    WHERE car.id_cliente = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo '<div class="text-gray-500 text-sm">Tu carrito está vacío.</div>';
    exit;
}

$total = 0;
$baseUrl = "http://localhost:8888/flor_express";

while ($row = $res->fetch_assoc()) {
    $total += $row['precio'] * $row['cantidad'];
    $imgSrc = !empty($row['imagen_principal']) ? $baseUrl . $row['imagen_principal'] : "img/placeholder-flor.jpg";
    ?>
    <div class="flex items-center justify-between p-4 border rounded-lg mb-3 bg-white">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($row['nombre_articulo']); ?>" class="w-full h-full object-cover">
            </div>
            <div>
                <h3 class="font-semibold"><?php echo htmlspecialchars($row['nombre_articulo']); ?></h3>
                <p class="text-gray-600">Cantidad: <?php echo $row['cantidad']; ?></p>
            </div>
        </div>
        <div class="text-right">
            <div class="font-bold text-magenta-flor">$<?php echo number_format($row['precio'], 2); ?> MXN</div>
            <button class="btn-eliminar-carrito bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 smooth-transition"
                    data-id="<?php echo $row['id_articulo']; ?>">
                Eliminar
            </button>
        </div>
    </div>
    <?php
}
?>
<div class="border-t pt-4 mt-4">
    <div class="flex justify-between items-center mb-4">
        <span class="text-xl font-bold">Total:</span>
        <span class="text-2xl font-bold text-magenta-flor">$<?php echo number_format($total, 2); ?> MXN</span>
    </div>
    <div class="mt-8 flex justify-end">
        <button id="btn-ir-pago" class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">
            Proceder al Pago
        </button>
    </div>
</div>
<?php
$conn->close();
?>
