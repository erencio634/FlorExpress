<?php
session_start();
require_once 'conexion.php';

// Verificar que el usuario esté logueado
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
$sql_pedido = "SELECT p.*, c.nombre as cliente_nombre, c.apellidos as cliente_apellidos, 
               c.telefono as cliente_telefono, c.correo as cliente_correo,
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

// Obtener correo del cliente
$sql_correo = "SELECT correo FROM usuarios_globales 
               WHERE id_usuario = (SELECT id_usuario FROM clientes WHERE id_cliente = ?)";
$stmt = $conn->prepare($sql_correo);
$stmt->bind_param("i", $pedido['id_cliente']);
$stmt->execute();
$result = $stmt->get_result();
$cliente_data = $result->fetch_assoc();
$pedido['cliente_correo'] = $cliente_data['correo'] ?? 'No disponible';

// Obtener detalles de productos
$sql_detalles = "SELECT dp.*, cat.nombre_articulo, cat.imagen_principal
                 FROM detalles_pedido dp
                 LEFT JOIN catalogo cat ON dp.id_articulo = cat.id_articulo
                 WHERE dp.id_pedido = ?";
$stmt = $conn->prepare($sql_detalles);
$stmt->bind_param("i", $id_pedido);
$stmt->execute();
$detalles = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido #FE-<?php echo str_pad($id_pedido, 3, '0', STR_PAD_LEFT); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'verde-hoja': '#C3D600',
                        'magenta-flor': '#C33A94'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <a href="dashboard_floreria.php" class="text-verde-hoja hover:underline">← Volver al Dashboard</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-magenta-flor mb-2">Pedido #FE-<?php echo str_pad($id_pedido, 3, '0', STR_PAD_LEFT); ?></h1>
                    <p class="text-gray-600">Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                </div>
                <div class="text-right">
                    <?php
                    $estado_class = '';
                    $estado_text = '';
                    switch($pedido['estado']) {
                        case 'entregado':
                            $estado_class = 'bg-green-100 text-green-800';
                            $estado_text = 'Entregado';
                            break;
                        case 'preparando':
                        case 'confirmado':
                            $estado_class = 'bg-yellow-100 text-yellow-800';
                            $estado_text = 'En Proceso';
                            break;
                        case 'cancelado':
                            $estado_class = 'bg-red-100 text-red-800';
                            $estado_text = 'Cancelado';
                            break;
                        default:
                            $estado_class = 'bg-blue-100 text-blue-800';
                            $estado_text = 'Pendiente';
                    }
                    ?>
                    <span class="<?php echo $estado_class; ?> px-4 py-2 rounded-full text-sm font-semibold"><?php echo $estado_text; ?></span>
                    <div class="mt-2 text-2xl font-bold">$<?php echo number_format($pedido['total'], 2); ?></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="border rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Información del Cliente</h3>
                    <div class="space-y-2">
                        <p><span class="font-semibold">Nombre:</span> <?php echo htmlspecialchars($pedido['cliente_nombre'] . ' ' . $pedido['cliente_apellidos']); ?></p>
                        <p><span class="font-semibold">Teléfono:</span> <?php echo htmlspecialchars($pedido['cliente_telefono'] ?? 'No disponible'); ?></p>
                        <p><span class="font-semibold">Correo:</span> <?php echo htmlspecialchars($pedido['cliente_correo']); ?></p>
                    </div>
                </div>
                
                <div class="border rounded-lg p-6">
                    <h3 class="text-xl font-bold mb-4">Dirección de Entrega</h3>
                    <div class="space-y-2">
                        <p><span class="font-semibold">Receptor:</span> <?php echo htmlspecialchars($pedido['nombre_receptor'] . ' ' . $pedido['apellidos_receptor']); ?></p>
                        <p><span class="font-semibold">Teléfono:</span> <?php echo htmlspecialchars($pedido['telefono_receptor']); ?></p>
                        <p><span class="font-semibold">Dirección:</span> <?php echo htmlspecialchars($pedido['calle'] . ', ' . $pedido['colonia']); ?></p>
                        <p><?php echo htmlspecialchars($pedido['municipio'] . ', ' . $pedido['estado'] . ' CP: ' . $pedido['codigo_postal']); ?></p>
                        <?php if ($pedido['referencias']): ?>
                        <p><span class="font-semibold">Referencias:</span> <?php echo htmlspecialchars($pedido['referencias']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-8">
                <h3 class="text-xl font-bold mb-4">Productos del Pedido</h3>
                <div class="border rounded-lg overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while ($detalle = $detalles->fetch_assoc()): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <?php if ($detalle['imagen_principal']): ?>
                                        <img src="<?php echo htmlspecialchars($detalle['imagen_principal']); ?>" alt="" class="w-12 h-12 rounded object-cover mr-3">
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($detalle['nombre_articulo'] ?? 'Producto personalizado'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><?php echo $detalle['cantidad']; ?></td>
                                <td class="px-6 py-4">$<?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                                <td class="px-6 py-4 font-semibold">$<?php echo number_format($detalle['subtotal'], 2); ?></td>
                            </tr>
                            <?php if ($detalle['mensaje_tarjeta']): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-2 bg-yellow-50 text-sm">
                                    <span class="font-semibold">Mensaje de tarjeta:</span> <?php echo htmlspecialchars($detalle['mensaje_tarjeta']); ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right font-bold">Total:</td>
                                <td class="px-6 py-4 font-bold text-xl text-verde-hoja">$<?php echo number_format($pedido['total'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-bold mb-4">Información de Pago</h3>
                <div class="space-y-2">
                    <p><span class="font-semibold">Método:</span> <?php echo strtoupper($pedido['metodo_pago']); ?></p>
                    <p><span class="font-semibold">Monto:</span> $<?php echo number_format($pedido['monto'], 2); ?></p>
                    <p><span class="font-semibold">Estado:</span> 
                        <span class="<?php echo $pedido['estado_pago'] == 'pagado' ? 'text-green-600' : 'text-yellow-600'; ?> font-semibold">
                            <?php echo ucfirst($pedido['estado_pago']); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <?php if ($pedido['estado'] != 'entregado' && $pedido['estado'] != 'cancelado'): ?>
            <div class="mt-8 flex justify-end space-x-4">
                <button onclick="actualizarEstado()" class="bg-verde-hoja text-white px-6 py-3 rounded-lg hover:bg-green-600 transition">
                    Actualizar Estado
                </button>
                <button onclick="imprimirPedido()" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition">
                    Imprimir
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function actualizarEstado() {
            const nuevoEstado = prompt('Ingresa el nuevo estado:\n- preparando\n- enviado\n- entregado');
            if (nuevoEstado && ['preparando', 'enviado', 'entregado'].includes(nuevoEstado.toLowerCase())) {
                fetch('actions/actualizar_estado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        id_pedido: <?php echo $id_pedido; ?>, 
                        estado: nuevoEstado.toLowerCase() 
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Estado actualizado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al actualizar el estado');
                });
            } else if (nuevoEstado) {
                alert('Estado no válido');
            }
        }
        
        function imprimirPedido() {
            window.print();
        }
    </script>
</body>
</html>
