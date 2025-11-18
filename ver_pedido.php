<?php
session_start();
require_once 'conexion.php';

// Verificar sesión y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'floreria') {
    header("Location: login.php");
    exit();
}

$id_pedido = $_GET['id'] ?? null;

if (!$id_pedido) {
    header("Location: dashboard_floreria.php");
    exit();
}

// Obtener información completa del pedido
$sql_pedido = "SELECT p.*, 
               c.nombre AS cliente_nombre, 
               c.apellidos AS cliente_apellidos, 
               c.telefono AS cliente_telefono,
               ug.correo AS cliente_correo,
               d.nombre_receptor, d.apellidos_receptor, d.telefono_receptor,
               d.calle, d.colonia, d.municipio, d.estado, d.codigo_postal, d.referencias,
               pag.metodo_pago, pag.monto, pag.estado_pago
               FROM pedidos p
               JOIN clientes c ON p.id_cliente = c.id_cliente
               JOIN usuarios_globales ug ON c.id_usuario = ug.id_usuario
               LEFT JOIN direcciones d ON p.id_direccion = d.id_direccion
               LEFT JOIN pagos pag ON p.id_pago = pag.id_pago
               WHERE p.id_pedido = ?";

$stmt = $conn->prepare($sql_pedido);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$pedido = $stmt->get_result()->fetch_assoc();

if (!$pedido) {
    header("Location: dashboard_floreria.php");
    exit();
}

// Obtener detalles del pedido
$sql_detalles = "SELECT dp.*, cat.nombre_articulo, cat.imagen_principal
                 FROM detalles_pedido dp
                 LEFT JOIN catalogo cat ON dp.id_articulo = cat.id_articulo
                 WHERE dp.id_pedido = ?";

$stmt = $conn->prepare($sql_detalles);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$detalles = $stmt->get_result();

// Función formato
function f($num) {
    return "$" . number_format($num, 2);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido #FE-<?php echo str_pad($id_pedido, 3, '0', STR_PAD_LEFT); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                'verde-hoja': '#C3D600', 'magenta-flor': '#C33A94'
            }}}
        }
    </script>
</head>
<body class="bg-gray-50">

<div class="container mx-auto px-4 py-8">
    
    <a href="dashboard_floreria.php" class="text-verde-hoja hover:underline">← Volver</a>

    <div class="bg-white rounded-lg shadow-lg p-8 mt-4">

        <!-- Encabezado -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-3xl font-bold text-magenta-flor">Pedido #FE-<?php echo str_pad($id_pedido, 3, '0', STR_PAD_LEFT); ?></h1>
                <p class="text-gray-600">Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
            </div>

            <div class="text-right">
                <?php
                $estado = $pedido['estado'];
                $estadoClass = [
                    'entregado' => 'bg-green-100 text-green-800',
                    'preparando' => 'bg-yellow-100 text-yellow-800',
                    'confirmado' => 'bg-yellow-100 text-yellow-800',
                    'cancelado' => 'bg-red-100 text-red-800',
                    'pendiente' => 'bg-blue-100 text-blue-800'
                ][$estado] ?? 'bg-gray-200';

                $estadoTexto = [
                    'entregado' => 'Entregado',
                    'preparando' => 'En Proceso',
                    'confirmado' => 'En Proceso',
                    'cancelado' => 'Cancelado',
                    'pendiente' => 'Pendiente'
                ][$estado] ?? '---';
                ?>
                <span class="<?php echo $estadoClass; ?> px-4 py-2 rounded-full font-semibold"><?php echo $estadoTexto; ?></span>
                <div class="mt-2 text-2xl font-bold"><?php echo f($pedido['total']); ?></div>
            </div>
        </div>

        <!-- Información del Cliente -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Cliente</h3>
                <p><b>Nombre:</b> <?php echo $pedido['cliente_nombre'] . ' ' . $pedido['cliente_apellidos']; ?></p>
                <p><b>Teléfono:</b> <?php echo $pedido['cliente_telefono'] ?? 'No disponible'; ?></p>
                <p><b>Correo:</b> <?php echo $pedido['cliente_correo']; ?></p>
            </div>

            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Entrega</h3>
                <p><b>Receptor:</b> <?php echo $pedido['nombre_receptor'] . ' ' . $pedido['apellidos_receptor']; ?></p>
                <p><b>Teléfono:</b> <?php echo $pedido['telefono_receptor']; ?></p>
                <p><b>Dirección:</b> <?php echo $pedido['calle'] . ', ' . $pedido['colonia']; ?></p>
                <p><?php echo $pedido['municipio'] . ', ' . $pedido['estado'] . ', CP ' . $pedido['codigo_postal']; ?></p>
                <?php if ($pedido['referencias']): ?>
                <p><b>Referencia:</b> <?php echo $pedido['referencias']; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Productos -->
        <h3 class="text-xl font-bold mb-3">Productos</h3>
        <table class="w-full border rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-3">Producto</th>
                    <th class="p-3">Cant.</th>
                    <th class="p-3">Precio</th>
                    <th class="p-3">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y">
            <?php while ($d = $detalles->fetch_assoc()): ?>
                <tr>
                    <td class="p-3">
                        <?php echo $d['nombre_articulo'] ?? 'Producto personalizado'; ?>
                    </td>
                    <td class="p-3"><?php echo $d['cantidad']; ?></td>
                    <td class="p-3"><?php echo f($d['precio_unitario']); ?></td>
                    <td class="p-3 font-semibold"><?php echo f($d['subtotal']); ?></td>
                </tr>
                <?php if ($d['mensaje_tarjeta']): ?>
                <tr>
                    <td colspan="4" class="bg-yellow-50 text-sm p-2">
                        <b>Mensaje:</b> <?php echo $d['mensaje_tarjeta']; ?>
                    </td>
                </tr>
                <?php endif; ?>
            <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Pago -->
        <div class="border rounded-lg p-6 mt-8">
            <h3 class="text-xl font-bold mb-4">Pago</h3>
            <p><b>Método:</b> <?php echo strtoupper($pedido['metodo_pago']); ?></p>
            <p><b>Monto:</b> <?php echo f($pedido['monto']); ?></p>
            <p><b>Estado:</b> 
                <span class="<?php echo $pedido['estado_pago'] == 'pagado' ? 'text-green-600' : 'text-yellow-600'; ?>">
                    <?php echo ucfirst($pedido['estado_pago']); ?>
                </span>
            </p>
        </div>

        <!-- Botones de acción -->
        <?php if (!in_array($pedido['estado'], ['entregado', 'cancelado'])): ?>
        <div class="mt-8 flex justify-end gap-4">
            <button onclick="actualizarEstado()" class="bg-verde-hoja text-white px-5 py-2 rounded-lg">Actualizar Estado</button>
            <button onclick="window.print()" class="bg-gray-200 px-5 py-2 rounded-lg">Imprimir</button>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function actualizarEstado() {
    const nuevoEstado = prompt("Nuevo estado:\npreparando\nenviado\nentregado");
    if (!nuevoEstado) return;

    if (!['preparando', 'enviado', 'entregado'].includes(nuevoEstado)) {
        return alert("Estado no válido");
    }

    fetch("actions/actualizar_estado.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id_pedido: <?= $id_pedido ?>,
            estado: nuevoEstado
        })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
        else alert(d.message);
    });
}
</script>
</body>
</html>
