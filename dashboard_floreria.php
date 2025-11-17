<?php
session_start();
require_once 'conexion.php';

// Verificar que el usuario esté logueado y sea una florería
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'floreria') {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Obtener información de la florería
$sql_floreria = "SELECT f.*, u.correo 
                 FROM florerias f 
                 JOIN usuarios_globales u ON f.id_usuario = u.id_usuario 
                 WHERE f.id_usuario = ?";
$stmt = $conn->prepare($sql_floreria);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$floreria = $stmt->get_result()->fetch_assoc();
$id_floreria = $floreria['id_floreria'];

// Total de pedidos completados de esta florería
$sql_completados = "SELECT COUNT(*) as total FROM pedidos p
                    JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                    WHERE dp.id_floreria = ? AND p.estado = 'entregado'";
$stmt = $conn->prepare($sql_completados);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$pedidos_completados = $stmt->get_result()->fetch_assoc()['total'];

// Ingresos del mes actual
$sql_ingresos = "SELECT SUM(p.total) as ingresos FROM pedidos p
                 JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                 WHERE dp.id_floreria = ? 
                 AND MONTH(p.fecha_pedido) = MONTH(CURRENT_DATE())
                 AND YEAR(p.fecha_pedido) = YEAR(CURRENT_DATE())
                 AND p.estado != 'cancelado'";
$stmt = $conn->prepare($sql_ingresos);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$ingresos_mes = $stmt->get_result()->fetch_assoc()['ingresos'] ?? 0;

// Calificación promedio (simulada - necesitarías implementar un sistema de reseñas)
$calificacion = 4.8;

// Tiempo promedio de entrega (simulado)
$tiempo_promedio = 45;

// Pedidos completados hoy
$sql_hoy = "SELECT COUNT(*) as total FROM pedidos p
            JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
            WHERE dp.id_floreria = ? 
            AND DATE(p.fecha_pedido) = CURDATE()
            AND p.estado = 'entregado'";
$stmt = $conn->prepare($sql_hoy);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$pedidos_hoy = $stmt->get_result()->fetch_assoc()['total'];

// Ingresos del día
$sql_ingresos_hoy = "SELECT SUM(p.total) as ingresos FROM pedidos p
                     JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                     WHERE dp.id_floreria = ? 
                     AND DATE(p.fecha_pedido) = CURDATE()
                     AND p.estado != 'cancelado'";
$stmt = $conn->prepare($sql_ingresos_hoy);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$ingresos_hoy = $stmt->get_result()->fetch_assoc()['ingresos'] ?? 0;

$sql_pendientes = "SELECT p.*, c.nombre as cliente_nombre, c.apellidos as cliente_apellidos,
                   d.calle, d.colonia, d.municipio,
                   GROUP_CONCAT(CONCAT(cat.nombre_articulo, ' (', dp.cantidad, ')') SEPARATOR ', ') as productos
                   FROM pedidos p
                   JOIN clientes c ON p.id_cliente = c.id_cliente
                   LEFT JOIN direcciones d ON p.id_direccion = d.id_direccion
                   JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                   LEFT JOIN catalogo cat ON dp.id_articulo = cat.id_articulo
                   WHERE p.estado = 'pendiente' AND (dp.id_floreria IS NULL OR dp.id_floreria = ?)
                   GROUP BY p.id_pedido
                   ORDER BY p.fecha_pedido DESC
                   LIMIT 10";
$stmt = $conn->prepare($sql_pendientes);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$pedidos_pendientes = $stmt->get_result();

$sql_mis_pedidos = "SELECT DISTINCT p.*, c.nombre as cliente_nombre, c.apellidos as cliente_apellidos,
                    GROUP_CONCAT(CONCAT(cat.nombre_articulo, ' (', dp.cantidad, ')') SEPARATOR ', ') as productos
                    FROM pedidos p
                    JOIN clientes c ON p.id_cliente = c.id_cliente
                    JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                    LEFT JOIN catalogo cat ON dp.id_articulo = cat.id_articulo
                    WHERE dp.id_floreria = ?
                    GROUP BY p.id_pedido
                    ORDER BY p.fecha_pedido DESC";
$stmt = $conn->prepare($sql_mis_pedidos);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$mis_pedidos = $stmt->get_result();

$sql_ventas_semana = "SELECT DAYOFWEEK(p.fecha_pedido) as dia, COUNT(*) as cantidad, SUM(p.total) as monto
                      FROM pedidos p
                      JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                      WHERE dp.id_floreria = ? 
                      AND p.fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                      AND p.estado != 'cancelado'
                      GROUP BY DAYOFWEEK(p.fecha_pedido)
                      ORDER BY dia";
$stmt = $conn->prepare($sql_ventas_semana);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$ventas_semana = $stmt->get_result();

$sql_productos_top = "SELECT cat.nombre_articulo, COUNT(*) as ventas
                      FROM detalles_pedido dp
                      JOIN catalogo cat ON dp.id_articulo = cat.id_articulo
                      JOIN pedidos p ON dp.id_pedido = p.id_pedido
                      WHERE dp.id_floreria = ? AND p.estado = 'entregado'
                      GROUP BY cat.id_articulo
                      ORDER BY ventas DESC
                      LIMIT 3";
$stmt = $conn->prepare($sql_productos_top);
$stmt->bind_param("i", $id_floreria);
$stmt->execute();
$productos_top = $stmt->get_result();

// Formatear números para mostrar
function formatearPrecio($precio) {
    return '$' . number_format($precio, 2, '.', ',');
}

function formatearPrecioCol($precio) {
    return '$' . number_format($precio, 0, '', '.') . ' COP';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Florería - Flor Express</title>
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
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body class="bg-gray-50">
    <div class="flex">
        <!-- Sidebar Florería -->
        <div class="w-64 gradient-bg text-white h-screen sticky top-0 overflow-y-auto">
            <div class="p-6">
                <div class="text-2xl font-bold mb-2">🌸 Flor Express</div>
                <div class="text-sm opacity-75 mb-8"><?php echo htmlspecialchars($floreria['nombre_floreria']); ?></div>
                <nav class="space-y-2">
                    <button onclick="showFloreriaSection('floreria-dashboard')" class="floreria-nav-btn w-full text-left px-4 py-2 rounded-lg bg-white bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </button>
                    <button onclick="showFloreriaSection('pedidos-disponibles')" class="floreria-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📋</span>
                        <span>Pedidos Disponibles</span>
                    </button>
                    <button onclick="showFloreriaSection('mis-pedidos')" class="floreria-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📦</span>
                        <span>Mis Pedidos</span>
                    </button>
                    <button onclick="showFloreriaSection('mi-floreria')" class="floreria-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>🏪</span>
                        <span>Mi Florería</span>
                    </button>
                    <button onclick="showFloreriaSection('estadisticas')" class="floreria-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📈</span>
                        <span>Estadísticas</span>
                    </button>
                    <button onclick="showFloreriaSection('chat')" class="floreria-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>💬</span>
                        <span>Chat con Admin</span>
                    </button>
                </nav>
                <a href="logout.php" class="mt-8 bg-magenta-flor text-white px-4 py-2 rounded-lg hover:bg-pink-600 smooth-transition w-full block text-center">Cerrar Sesión</a>
            </div>
        </div>
        
        <!-- Main Content Florería -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div id="floreria-content">
                <!-- Dashboard Florería -->
                <div id="floreria-dashboard">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-2">Dashboard</h2>
                        <p class="text-gray-600">Bienvenido de vuelta, <?php echo htmlspecialchars($floreria['nombre_floreria']); ?></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-verde-hoja text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">📦</div>
                            <div class="text-2xl font-bold"><?php echo $pedidos_completados; ?></div>
                            <div class="text-sm opacity-90">Pedidos Completados</div>
                        </div>
                        <div class="bg-magenta-flor text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="text-2xl font-bold"><?php echo formatearPrecio($ingresos_mes); ?></div>
                            <div class="text-sm opacity-90">Ingresos del Mes</div>
                        </div>
                        <div class="bg-blue-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">⭐</div>
                            <div class="text-2xl font-bold"><?php echo $calificacion; ?></div>
                            <div class="text-sm opacity-90">Calificación</div>
                        </div>
                        <div class="bg-purple-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">⏱️</div>
                            <div class="text-2xl font-bold"><?php echo $tiempo_promedio; ?> min</div>
                            <div class="text-sm opacity-90">Tiempo Promedio</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Pedidos Pendientes</h3>
                            <div class="space-y-3">
                                <?php 
                                $count = 0;
                                while ($pedido = $pedidos_pendientes->fetch_assoc()): 
                                    if ($count >= 2) break;
                                    $count++;
                                ?>
                                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                    <div>
                                        <div class="font-semibold"><?php echo htmlspecialchars($pedido['productos']); ?></div>
                                        <div class="text-sm text-gray-600">Cliente: <?php echo htmlspecialchars($pedido['cliente_nombre'] . ' ' . $pedido['cliente_apellidos']); ?></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-magenta-flor"><?php echo formatearPrecio($pedido['total']); ?></div>
                                        <button onclick="aceptarPedido(<?php echo $pedido['id_pedido']; ?>)" class="bg-verde-hoja text-white px-3 py-1 rounded text-sm mt-1">Aceptar</button>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php if ($count == 0): ?>
                                <p class="text-gray-500 text-center py-4">No hay pedidos pendientes en este momento</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Estadísticas del Día</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Pedidos Completados:</span>
                                    <span class="font-bold text-verde-hoja"><?php echo $pedidos_hoy; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Ingresos del Día:</span>
                                    <span class="font-bold text-magenta-flor"><?php echo formatearPrecio($ingresos_hoy); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Calificación Promedio:</span>
                                    <span class="font-bold text-yellow-500"><?php echo $calificacion; ?> ⭐</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Tiempo Promedio:</span>
                                    <span class="font-bold text-blue-500"><?php echo $tiempo_promedio; ?> min</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pedidos Disponibles -->
                <div id="pedidos-disponibles" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Pedidos Disponibles</h2>
                    </div>
                    <div class="space-y-6">
                        <?php 
                        mysqli_data_seek($pedidos_pendientes, 0);
                        while ($pedido = $pedidos_pendientes->fetch_assoc()): 
                        ?>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-magenta-flor">#FE-<?php echo str_pad($pedido['id_pedido'], 3, '0', STR_PAD_LEFT); ?></h3>
                                    <p class="text-gray-600">Cliente: <?php echo htmlspecialchars($pedido['cliente_nombre'] . ' ' . $pedido['cliente_apellidos']); ?></p>
                                    <p class="text-gray-600">Entrega: <?php echo htmlspecialchars($pedido['calle'] . ', ' . $pedido['colonia'] . ', ' . $pedido['municipio']); ?></p>
                                    <p class="text-gray-600">Fecha: <?php echo date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])); ?></p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-verde-hoja"><?php echo formatearPrecio($pedido['total']); ?></div>
                                    <div class="text-sm text-gray-500"><?php 
                                        $tiempo = time() - strtotime($pedido['fecha_pedido']);
                                        $minutos = floor($tiempo / 60);
                                        echo "Hace " . ($minutos < 60 ? $minutos . " min" : floor($minutos/60) . " hora(s)");
                                    ?></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Productos:</h4>
                                <p class="text-gray-600"><?php echo htmlspecialchars($pedido['productos']); ?></p>
                            </div>
                            <div class="flex space-x-4">
                                <button onclick="aceptarPedido(<?php echo $pedido['id_pedido']; ?>)" class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Aceptar Pedido</button>
                                <button onclick="verDetalles(<?php echo $pedido['id_pedido']; ?>)" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 smooth-transition">Ver Detalles</button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        <?php if ($pedidos_pendientes->num_rows == 0): ?>
                        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                            <p class="text-gray-500 text-lg">No hay pedidos disponibles en este momento</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mis Pedidos -->
                <div id="mis-pedidos" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mis Pedidos</h2>
                        <div class="flex space-x-4 mb-6">
                            <button onclick="filtrarPedidos('todos')" class="filtro-btn bg-verde-hoja text-white px-4 py-2 rounded-lg" data-filtro="todos">Todos</button>
                            <button onclick="filtrarPedidos('pendiente')" class="filtro-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg" data-filtro="pendiente">Pendientes</button>
                            <button onclick="filtrarPedidos('preparando')" class="filtro-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg" data-filtro="preparando">En Proceso</button>
                            <button onclick="filtrarPedidos('entregado')" class="filtro-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg" data-filtro="entregado">Completados</button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php while ($pedido = $mis_pedidos->fetch_assoc()): ?>
                                <tr class="pedido-row" data-estado="<?php echo $pedido['estado']; ?>">
                                    <td class="px-6 py-4 font-medium">#FE-<?php echo str_pad($pedido['id_pedido'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($pedido['cliente_nombre'] . ' ' . $pedido['cliente_apellidos']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars(substr($pedido['productos'], 0, 30)); ?>...</td>
                                    <td class="px-6 py-4">
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
                                        <span class="<?php echo $estado_class; ?> px-2 py-1 rounded-full text-sm"><?php echo $estado_text; ?></span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold"><?php echo formatearPrecio($pedido['total']); ?></td>
                                    <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($pedido['fecha_pedido'])); ?></td>
                                    <td class="px-6 py-4">
                                        <button onclick="verDetalles(<?php echo $pedido['id_pedido']; ?>)" class="text-verde-hoja hover:underline mr-2">Ver</button>
                                        <?php if ($pedido['estado'] != 'entregado' && $pedido['estado'] != 'cancelado'): ?>
                                        <button onclick="actualizarEstado(<?php echo $pedido['id_pedido']; ?>)" class="text-magenta-flor hover:underline">Actualizar</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if ($mis_pedidos->num_rows == 0): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">No tienes pedidos asignados</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mi Florería -->
                <div id="mi-floreria" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mi Florería</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                                <h3 class="text-xl font-bold mb-4">Información de la Florería</h3>
                                <form id="form-floreria" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Florería</label>
                                            <input type="text" name="nombre_floreria" value="<?php echo htmlspecialchars($floreria['nombre_floreria']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                                            <input type="tel" name="telefono" value="<?php echo htmlspecialchars($floreria['telefono']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Dirección</label>
                                        <input type="text" name="direccion" value="<?php echo htmlspecialchars($floreria['direccion_floreria']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                                        <textarea name="descripcion" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"><?php echo htmlspecialchars($floreria['descripcion']); ?></textarea>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Capacidad Diaria</label>
                                            <input type="number" name="capacidad_diaria" value="<?php echo $floreria['capacidad_diaria']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Estado</label>
                                            <select name="estatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                                <option value="activa" <?php echo $floreria['estatus'] == 'activa' ? 'selected' : ''; ?>>Activa</option>
                                                <option value="inactiva" <?php echo $floreria['estatus'] == 'inactiva' ? 'selected' : ''; ?>>Inactiva</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Actualizar Información</button>
                                </form>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <h3 class="text-xl font-bold mb-4">Logo de la Florería</h3>
                                <div class="text-center">
                                    <?php if (!empty($floreria['foto_perfil_f'])): ?>
                                    <img src="<?php echo htmlspecialchars($floreria['foto_perfil_f']); ?>" alt="Logo" class="w-32 h-32 rounded-lg mx-auto mb-4 object-cover">
                                    <?php else: ?>
                                    <div class="w-32 h-32 bg-gradient-to-br from-verde-hoja to-green-600 rounded-lg mx-auto mb-4 flex items-center justify-center text-white text-4xl font-bold">
                                        <?php echo strtoupper(substr($floreria['nombre_floreria'], 0, 2)); ?>
                                    </div>
                                    <?php endif; ?>
                                    <button onclick="alert('Función de cambio de logo en desarrollo')" class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition text-sm">Cambiar Logo</button>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                                <h3 class="text-xl font-bold mb-4">Estadísticas Rápidas</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pedidos este mes:</span>
                                        <span class="font-semibold text-verde-hoja"><?php echo $pedidos_completados; ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ingresos del mes:</span>
                                        <span class="font-semibold text-magenta-flor"><?php echo formatearPrecio($ingresos_mes); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Calificación promedio:</span>
                                        <span class="font-semibold text-yellow-500"><?php echo $calificacion; ?> ⭐</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Capacidad diaria:</span>
                                        <span class="font-semibold text-blue-500"><?php echo $floreria['capacidad_diaria']; ?> pedidos</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div id="estadisticas" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Estadísticas</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Ventas de la Semana</h3>
                            <div class="h-64 flex items-end justify-between">
                                <?php
                                $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                                $ventas_array = array_fill(0, 7, 0);
                                while ($venta = $ventas_semana->fetch_assoc()) {
                                    $ventas_array[$venta['dia'] - 1] = $venta['cantidad'];
                                }
                                $max_ventas = max($ventas_array) ?: 1;
                                
                                for ($i = 1; $i <= 7; $i++):
                                    $altura = ($ventas_array[$i % 7] / $max_ventas) * 200;
                                ?>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2 relative group" style="height: <?php echo $altura; ?>px;">
                                        <span class="hidden group-hover:block absolute -top-6 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded"><?php echo $ventas_array[$i % 7]; ?></span>
                                    </div>
                                    <span class="text-sm"><?php echo $dias[$i % 7]; ?></span>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Productos Más Vendidos</h3>
                            <div class="space-y-4">
                                <?php 
                                $max_producto = 0;
                                $productos_array = [];
                                while ($producto = $productos_top->fetch_assoc()) {
                                    $productos_array[] = $producto;
                                    if ($producto['ventas'] > $max_producto) $max_producto = $producto['ventas'];
                                }
                                
                                foreach ($productos_array as $producto):
                                    $porcentaje = $max_producto > 0 ? ($producto['ventas'] / $max_producto) * 100 : 0;
                                ?>
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span><?php echo htmlspecialchars($producto['nombre_articulo']); ?></span>
                                        <span><?php echo $producto['ventas']; ?> ventas</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-verde-hoja h-2 rounded-full" style="width: <?php echo $porcentaje; ?>%"></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if (empty($productos_array)): ?>
                                <p class="text-gray-500 text-center py-4">No hay datos de ventas disponibles</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat con Admin -->
                <div id="chat" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Chat con Administrador</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Conversaciones</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg cursor-pointer">
                                    <div class="w-10 h-10 bg-verde-hoja rounded-full flex items-center justify-center text-white font-bold">A</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Administrador</div>
                                        <div class="text-sm text-gray-600">¿Necesitas ayuda?</div>
                                    </div>
                                    <span class="text-xs text-gray-500">Hoy</span>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Conversación con Administrador</h3>
                            <div class="h-96 overflow-y-auto mb-4 space-y-4 p-4 bg-gray-50 rounded-lg">
                                <div class="text-center text-gray-500 text-sm py-4">
                                    Inicia una conversación con el administrador
                                </div>
                            </div>
                            <form id="form-chat" class="flex space-x-4">
                                <input type="text" id="mensaje-chat" placeholder="Escribe tu mensaje..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja" required>
                                <button type="submit" class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Enviar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showFloreriaSection(sectionId) {
            // Hide all sections
            const sections = ['floreria-dashboard', 'pedidos-disponibles', 'mis-pedidos', 'mi-floreria', 'estadisticas', 'chat'];
            sections.forEach(section => {
                const element = document.getElementById(section);
                if (element) element.classList.add('hidden');
            });
            
            // Show selected section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
            
            // Update navigation
            const navButtons = document.querySelectorAll('.floreria-nav-btn');
            navButtons.forEach(btn => {
                btn.classList.remove('bg-white', 'bg-opacity-20');
            });
            event.target.closest('button').classList.add('bg-white', 'bg-opacity-20');
        }

        function aceptarPedido(idPedido) {
            if (confirm('¿Deseas aceptar este pedido?')) {
                fetch('actions/aceptar_pedido.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id_pedido: idPedido })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pedido aceptado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al aceptar el pedido');
                });
            }
        }

        function verDetalles(idPedido) {
            window.location.href = 'ver_pedido.php?id=' + idPedido;
        }

        function actualizarEstado(idPedido) {
            const nuevoEstado = prompt('Ingresa el nuevo estado:\n- preparando\n- enviado\n- entregado');
            if (nuevoEstado && ['preparando', 'enviado', 'entregado'].includes(nuevoEstado.toLowerCase())) {
                fetch('actions/actualizar_estado.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        id_pedido: idPedido, 
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

        function filtrarPedidos(filtro) {
            const rows = document.querySelectorAll('.pedido-row');
            const buttons = document.querySelectorAll('.filtro-btn');
            
            buttons.forEach(btn => {
                btn.classList.remove('bg-verde-hoja', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            
            event.target.classList.remove('bg-gray-200', 'text-gray-700');
            event.target.classList.add('bg-verde-hoja', 'text-white');
            
            rows.forEach(row => {
                if (filtro === 'todos') {
                    row.style.display = '';
                } else {
                    const estado = row.dataset.estado;
                    row.style.display = estado === filtro ? '' : 'none';
                }
            });
        }

        document.getElementById('form-floreria')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('actions/actualizar_floreria.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Información actualizada exitosamente');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la información');
            });
        });

        document.getElementById('form-chat')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const mensaje = document.getElementById('mensaje-chat').value;
            
            // Aquí se implementaría el envío real del mensaje
            alert('Función de chat en desarrollo. Mensaje: ' + mensaje);
            document.getElementById('mensaje-chat').value = '';
        });

        // Initialize dashboard with dashboard visible
        document.addEventListener('DOMContentLoaded', function() {
            showFloreriaSection('floreria-dashboard');
        });
    </script>
</body>
</html>
