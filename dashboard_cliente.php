<?php
session_start();
include('conexion.php');

// 1. Validar que hay sesión
if (!isset($_SESSION['id_usuario'])) {
    // si no hay sesión, mándalo al inicio
    header("Location: index.html");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Conseguir datos básicos del usuario (rol, correo)
$sqlUsuario = "SELECT id_usuario, correo, rol, fecha_registro, ultimo_acceso 
               FROM usuarios_globales 
               WHERE id_usuario = ?";
$stmtUsuario = $conn->prepare($sqlUsuario);
$stmtUsuario->bind_param("i", $id_usuario);
$stmtUsuario->execute();
$resUsuario = $stmtUsuario->get_result();
$usuarioGlobal = $resUsuario->fetch_assoc();

// si por alguna razón no está, sacar a index
if (!$usuarioGlobal) {
    header("Location: index.html");
    exit;
}

// Solo permitimos acceso a clientes aquí
if ($usuarioGlobal['rol'] !== 'cliente') {
    // si quieres más estricto puedes mandar a su dashboard correspondiente
    header("Location: dashboard_admin.php");
    exit;
}

// 3. Conseguir datos del perfil de cliente (tabla clientes)
$sqlCliente = "SELECT id_cliente, nombre, apellidos, usuario, fecha_nacimiento, telefono, foto_perfil, fecha_creacion
               FROM clientes
               WHERE id_usuario = ?";
$stmtCliente = $conn->prepare($sqlCliente);
$stmtCliente->bind_param("i", $id_usuario);
$stmtCliente->execute();
$resCliente = $stmtCliente->get_result();
$cliente = $resCliente->fetch_assoc();

$id_cliente = $cliente ? $cliente['id_cliente'] : null;

// 4. Estadísticas rápidas para el dashboard
// 4.1 Pedidos realizados
$totalPedidos = 0;
$totalGastado = 0.00;

if ($id_cliente) {
    $sqlStats = "SELECT COUNT(*) AS pedidos_count,
                        COALESCE(SUM(total),0) AS gasto_total
                 FROM pedidos
                 WHERE id_cliente = ?";
    $stmtStats = $conn->prepare($sqlStats);
    $stmtStats->bind_param("i", $id_cliente);
    $stmtStats->execute();
    $resStats = $stmtStats->get_result();
    $stats = $resStats->fetch_assoc();

    $totalPedidos = $stats['pedidos_count'] ?? 0;
    $totalGastado = $stats['gasto_total'] ?? 0;
}

// 4.2 Favoritos count
$favoritosCount = 0;
if ($id_cliente) {
    $sqlFavCount = "SELECT COUNT(*) AS favs
                    FROM favoritos
                    WHERE id_cliente = ?";
    $stmtFavCount = $conn->prepare($sqlFavCount);
    $stmtFavCount->bind_param("i", $id_cliente);
    $stmtFavCount->execute();
    $resFavCount = $stmtFavCount->get_result();
    $favRow = $resFavCount->fetch_assoc();
    $favoritosCount = $favRow['favs'] ?? 0;
}

// 4.3 Calificación promedio de reseñas del cliente
$calificacionPromedio = 0;
if ($id_cliente) {
    $sqlCal = "SELECT AVG(calificacion) AS promedio
               FROM resenas
               WHERE id_cliente = ?";
    $stmtCal = $conn->prepare($sqlCal);
    $stmtCal->bind_param("i", $id_cliente);
    $stmtCal->execute();
    $resCal = $stmtCal->get_result();
    $rowCal = $resCal->fetch_assoc();
    $calificacionPromedio = $rowCal['promedio'] ? number_format($rowCal['promedio'], 1) : "0.0";
}

// 5. Pedidos recientes
$pedidosRecientes = [];
if ($id_cliente) {
    $sqlPedidosRecientes = "
        SELECT p.id_pedido,
               p.fecha_pedido,
               p.estado,
               c2.nombre_articulo,
               f.nombre_floreria
        FROM pedidos p
        LEFT JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
        LEFT JOIN catalogo c2 ON dp.id_articulo = c2.id_articulo
        LEFT JOIN florerias f ON dp.id_floreria = f.id_floreria
        WHERE p.id_cliente = ?
        ORDER BY p.fecha_pedido DESC
        LIMIT 5
    ";
    $stmtPedRec = $conn->prepare($sqlPedidosRecientes);
    $stmtPedRec->bind_param("i", $id_cliente);
    $stmtPedRec->execute();
    $resPedRec = $stmtPedRec->get_result();
    while ($row = $resPedRec->fetch_assoc()) {
        $pedidosRecientes[] = $row;
    }
}

// 6. Direcciones del cliente
$direcciones = [];
if ($id_cliente) {
    $sqlDir = "SELECT * FROM direcciones WHERE id_cliente = ?";
    $stmtDir = $conn->prepare($sqlDir);
    $stmtDir->bind_param("i", $id_cliente);
    $stmtDir->execute();
    $resDir = $stmtDir->get_result();
    while ($row = $resDir->fetch_assoc()) {
        $direcciones[] = $row;
    }
}

// 7. Carrito actual
$carritoItems = [];
if ($id_cliente) {
    $sqlCarrito = "
        SELECT car.id_carrito,
               car.id_articulo,          -- 🔹 AGREGADO AQUÍ
               car.cantidad,
               c2.nombre_articulo,
               c2.precio,
               c2.imagen_principal
        FROM carrito car
        LEFT JOIN catalogo c2 ON car.id_articulo = c2.id_articulo
        WHERE car.id_cliente = ?
    ";
    $stmtCar = $conn->prepare($sqlCarrito);
    $stmtCar->bind_param("i", $id_cliente);
    $stmtCar->execute();
    $resCar = $stmtCar->get_result();
    while ($row = $resCar->fetch_assoc()) {
        $carritoItems[] = $row;
    }
}


// calcular total carrito
$totalCarrito = 0;
foreach ($carritoItems as $it) {
    $totalCarrito += ($it['precio'] * $it['cantidad']);
}

// 8. Favoritos (wishlist)
$favoritosLista = [];
if ($id_cliente) {
    $sqlFav = "
        SELECT fav.id_favorito,
               fav.id_articulo,           -- 🔹 AGREGA ESTA LÍNEA
               c2.nombre_articulo,
               c2.precio,
               c2.imagen_principal
        FROM favoritos fav
        LEFT JOIN catalogo c2 ON fav.id_articulo = c2.id_articulo
        WHERE fav.id_cliente = ?
    ";
    $stmtFav = $conn->prepare($sqlFav);
    $stmtFav->bind_param("i", $id_cliente);
    $stmtFav->execute();
    $resFav = $stmtFav->get_result();
    while ($row = $resFav->fetch_assoc()) {
        $favoritosLista[] = $row;
    }
}


// 9. Reseñas del cliente
$resenasLista = [];
if ($id_cliente) {
    $sqlResenas = "
        SELECT r.id_resena,
               r.calificacion,
               r.comentario,
               r.fecha,
               c2.nombre_articulo
        FROM resenas r
        LEFT JOIN catalogo c2 ON r.id_articulo = c2.id_articulo
        WHERE r.id_cliente = ?
        ORDER BY r.fecha DESC
    ";
    $stmtRes = $conn->prepare($sqlResenas);
    $stmtRes->bind_param("i", $id_cliente);
    $stmtRes->execute();
    $resRes = $stmtRes->get_result();
    while ($row = $resRes->fetch_assoc()) {
        $resenasLista[] = $row;
    }
}

// 10. Notificaciones del usuario (por id_usuario global)
$notificacionesLista = [];
$sqlNotif = "
    SELECT tipo, titulo, mensaje, leido, fecha
    FROM notificaciones
    WHERE id_usuario = ?
    ORDER BY fecha DESC
    LIMIT 10
";
$stmtNotif = $conn->prepare($sqlNotif);
$stmtNotif->bind_param("i", $id_usuario);
$stmtNotif->execute();
$resNotif = $stmtNotif->get_result();
while ($row = $resNotif->fetch_assoc()) {
    $notificacionesLista[] = $row;
}

// 11. Métodos de pago guardados del cliente (tabla nueva metodos_pago_cliente)
$metodosPagoGuardados = [];
if ($id_cliente) {
    $sqlMetodosGuardados = "
        SELECT id_metodo,
               tipo,
               alias,
               titular,
               ultimos4,
               expiracion,
               es_principal,
               fecha_agregado
        FROM metodos_pago_cliente
        WHERE id_cliente = ?
        ORDER BY es_principal DESC, fecha_agregado DESC
    ";
    $stmtMetCli = $conn->prepare($sqlMetodosGuardados);
    $stmtMetCli->bind_param("i", $id_cliente);
    $stmtMetCli->execute();
    $resMetCli = $stmtMetCli->get_result();
    while ($row = $resMetCli->fetch_assoc()) {
        $metodosPagoGuardados[] = $row;
    }
}


// Helper para estado pedido → badge
function badgeEstado($estado)
{
    switch ($estado) {
        case 'confirmado':
        case 'entregado':
            return ['bg-green-100 text-green-800', ucfirst($estado)];
        case 'preparando':
        case 'pendiente':
            return ['bg-yellow-100 text-yellow-800', ucfirst($estado)];
        case 'enviado':
            return ['bg-blue-100 text-blue-800', 'Enviado'];
        case 'cancelado':
        case 'reembolsado':
            return ['bg-red-100 text-red-800', ucfirst($estado)];
        default:
            return ['bg-gray-200 text-gray-800', $estado];
    }
}




?>
<!DOCTYPE html>

<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cliente - Flor Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
        <!-- Sidebar -->
        <div class="w-64 gradient-bg text-white h-screen sticky top-0 overflow-y-auto">
            <div class="p-6">
                <div class="text-2xl font-bold mb-2">🌸 Flor Express</div>

                <!-- Nombre de usuario real -->
                <div id="user-info" class="text-sm opacity-75 mb-8">
                    <?php
                    echo htmlspecialchars(
                        ($cliente['nombre'] ?? 'Usuario') .
                        (isset($cliente['apellidos']) ? ' ' . $cliente['apellidos'] : '')
                    ) . " - Cliente";
                    ?>
                </div>

                <nav class="space-y-2">
                    <button onclick="showDashboardSection('dashboard-main',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg bg-white bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </button>

                    <div class="text-xs text-white opacity-60 px-4 py-2 uppercase tracking-wider">Compras</div>
                    <button onclick="showDashboardSection('catalogo')"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>🌸</span>
                        <span>Catálogo</span>
                    </button>
                    <button onclick="showDashboardSection('carrito',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>🛒</span>
                        <span>Mi Carrito</span>
                    </button>
                    <button onclick="showDashboardSection('favoritos',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>❤️</span>
                        <span>Favoritos</span>
                    </button>

                    <div class="text-xs text-white opacity-60 px-4 py-2 uppercase tracking-wider">Pedidos</div>
                    <button onclick="showDashboardSection('mis-pedidos',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📦</span>
                        <span>Mis Pedidos</span>
                    </button>
                    <button onclick="showDashboardSection('seguimiento',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>🗺️</span>
                        <span>Seguimiento</span>
                    </button>

                    <div class="text-xs text-white opacity-60 px-4 py-2 uppercase tracking-wider">Mi Perfil</div>
                    <button onclick="showDashboardSection('perfil',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>👤</span>
                        <span>Información Personal</span>
                    </button>
                    <button onclick="showDashboardSection('pagos',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>💳</span>
                        <span>Métodos de Pago</span>
                    </button>
                    <button onclick="showDashboardSection('direcciones',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>📍</span>
                        <span>Mis Direcciones</span>
                    </button>
                    <button onclick="showDashboardSection('reseñas',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>⭐</span>
                        <span>Mis Reseñas</span>
                    </button>
                    <button onclick="showDashboardSection('notificaciones',this)"
                        class="dashboard-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span>🔔</span>
                        <span>Notificaciones</span>
                    </button>
                </nav>

                <form action="logout.php" method="POST" class="mt-8">
                    <button
                        class="bg-magenta-flor text-white px-4 py-2 rounded-lg hover:bg-pink-600 smooth-transition w-full">Cerrar
                        Sesión</button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div id="dashboard-content">

                <!-- DASHBOARD PRINCIPAL -->
                <div id="dashboard-main">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-2">Dashboard</h2>
                        <p class="text-gray-600">
                            Bienvenido de vuelta,
                            <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-verde-hoja text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">📦</div>
                            <div class="text-2xl font-bold"><?php echo $totalPedidos; ?></div>
                            <div class="text-sm opacity-90">Pedidos Realizados</div>
                        </div>

                        <div class="bg-magenta-flor text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="text-2xl font-bold">$<?php echo number_format($totalGastado, 2); ?> MXN</div>
                            <div class="text-sm opacity-90">Total Gastado</div>
                        </div>

                        <div class="bg-blue-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">⭐</div>
                            <div class="text-2xl font-bold"><?php echo $calificacionPromedio; ?></div>
                            <div class="text-sm opacity-90">Calificación Promedio</div>
                        </div>

                        <div class="bg-purple-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">❤️</div>
                            <div class="text-2xl font-bold"><?php echo $favoritosCount; ?></div>
                            <div class="text-sm opacity-90">Productos Favoritos</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Pedidos Recientes -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Pedidos Recientes</h3>
                            <div class="space-y-3">
                                <?php if (count($pedidosRecientes) === 0): ?>
                                    <div class="text-gray-500 text-sm">Aún no tienes pedidos.</div>
                                <?php else: ?>
                                    <?php foreach ($pedidosRecientes as $p): ?>
                                        <?php
                                        [$badgeClass, $badgeText] = badgeEstado($p['estado']);
                                        ?>
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="font-semibold">
                                                    <?php echo htmlspecialchars($p['nombre_articulo'] ?? 'Pedido'); ?>
                                                </div>
                                                <div class="text-sm text-gray-600">#FE-<?php echo $p['id_pedido']; ?></div>
                                                <div class="text-xs text-gray-500"><?php echo $p['nombre_floreria'] ?? ''; ?>
                                                </div>
                                            </div>
                                            <span class="<?php echo $badgeClass; ?> px-2 py-1 rounded-full text-sm">
                                                <?php echo $badgeText; ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Productos Recomendados (por ahora, top visibles del catálogo) -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Productos Recomendados</h3>
                            <div class="space-y-3">
                                <?php
                                // mini recomendador: mostrar 2 productos activos del catálogo
                                $sqlRec = "SELECT nombre_articulo, precio 
                                           FROM catalogo
                                           WHERE estado = 'activo' AND visible = 1
                                           ORDER BY fecha_creacion DESC
                                           LIMIT 2";
                                $recRes = $conn->query($sqlRec);
                                if ($recRes && $recRes->num_rows > 0):
                                    while ($rec = $recRes->fetch_assoc()):
                                        ?>
                                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                            <span class="text-2xl">🌸</span>
                                            <div class="flex-1">
                                                <div class="font-semibold">
                                                    <?php echo htmlspecialchars($rec['nombre_articulo']); ?>
                                                </div>
                                                <div class="text-sm text-gray-600">
                                                    $<?php echo number_format($rec['precio'], 2); ?> MXN</div>
                                            </div>
                                            <button class="btn-agregar-carrito"
                                                data-id="<?php echo $art['id_articulo']; ?>">Agregar</button>

                                        </div>
                                        <?php
                                    endwhile;
                                else:
                                    ?>
                                    <div class="text-gray-500 text-sm">Pronto tendremos recomendaciones para ti.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /dashboard-main -->


                <!-- =============================
     CATÁLOGO DE PRODUCTOS
============================= -->
                <div id="catalogo" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Catálogo de Productos</h2>
                        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                            <!-- 🔍 Buscador y filtros -->
                            <form id="form-busqueda" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Buscar productos</label>
                                    <input type="text" name="busqueda" id="busqueda"
                                        placeholder="Buscar flores, ramos, eventos..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Categoría</label>
                                    <select name="categoria" id="categoria"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        <option value="">Todas las categorías</option>
                                        <option value="Amor y Romance">Amor y Romance</option>
                                        <option value="Felicitaciones">Felicitaciones</option>
                                        <option value="Agradecimiento y Amistad">Agradecimiento y Amistad</option>
                                        <option value="Condolencias">Condolencias</option>
                                        <option value="Temporadas Especiales">Temporadas Especiales</option>
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button type="submit"
                                        class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition w-full">
                                        Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 🔹 Resultados -->
                    <div id="contenedor-catalogo"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <!-- Aquí se cargan los productos vía AJAX -->
                    </div>
                </div>

                <!-- =============================
     SCRIPT: CARGAR CATÁLOGO
============================= -->
                <script>
                    function cargarCatalogo(filtros = {}) {
                        const contenedor = document.getElementById("contenedor-catalogo");
                        contenedor.innerHTML = "<div class='col-span-full text-gray-500 text-center p-6'>Cargando productos...</div>";

                        const formData = new FormData();
                        if (filtros.busqueda) formData.append("busqueda", filtros.busqueda);
                        if (filtros.categoria) formData.append("categoria", filtros.categoria);

                        fetch("php/listar_catalogo.php", {
                            method: "POST",
                            body: formData
                        })
                            .then(r => r.text())
                            .then(html => contenedor.innerHTML = html)
                            .catch(() => {
                                contenedor.innerHTML = "<div class='col-span-full text-red-500 text-center p-6'>Error al cargar el catálogo.</div>";
                            });
                    }

                    // ✅ Evento del buscador (manual)
                    document.getElementById("form-busqueda").addEventListener("submit", e => {
                        e.preventDefault();
                        const busqueda = document.getElementById("busqueda").value.trim();
                        const categoria = document.getElementById("categoria").value;
                        cargarCatalogo({ busqueda, categoria });
                    });

                    // ✅ Evento automático: cambiar categoría = actualizar catálogo
                    document.getElementById("categoria").addEventListener("change", e => {
                        const categoria = e.target.value;
                        const busqueda = document.getElementById("busqueda").value.trim();
                        cargarCatalogo({ busqueda, categoria });
                    });

                    // ✅ También buscar automáticamente mientras se escribe
                    document.getElementById("busqueda").addEventListener("input", e => {
                        const busqueda = e.target.value.trim();
                        const categoria = document.getElementById("categoria").value;
                        cargarCatalogo({ busqueda, categoria });
                    });

                    // ✅ Carga inicial
                    document.addEventListener("DOMContentLoaded", () => {
                        cargarCatalogo();
                    });
                </script>


                <!-- MIS PEDIDOS -->
                <div id="mis-pedidos" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mis Pedidos</h2>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Florería
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                if ($id_cliente) {
                                    $sqlPedidosTabla = "
                                        SELECT p.id_pedido,
                                               p.fecha_pedido,
                                               p.estado,
                                               p.total,
                                               c2.nombre_articulo,
                                               f.nombre_floreria
                                        FROM pedidos p
                                        LEFT JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
                                        LEFT JOIN catalogo c2 ON dp.id_articulo = c2.id_articulo
                                        LEFT JOIN florerias f ON dp.id_floreria = f.id_floreria
                                        WHERE p.id_cliente = ?
                                        ORDER BY p.fecha_pedido DESC
                                    ";
                                    $stmtTabla = $conn->prepare($sqlPedidosTabla);
                                    $stmtTabla->bind_param("i", $id_cliente);
                                    $stmtTabla->execute();
                                    $resTabla = $stmtTabla->get_result();

                                    if ($resTabla->num_rows === 0) {
                                        echo '<tr><td class="px-6 py-4 text-gray-500 text-sm" colspan="6">No tienes pedidos aún.</td></tr>';
                                    } else {
                                        while ($row = $resTabla->fetch_assoc()) {
                                            [$badgeClass, $badgeText] = badgeEstado($row['estado']);
                                            echo '<tr>';
                                            echo '<td class="px-6 py-4 font-medium">#FE-' . $row['id_pedido'] . '</td>';
                                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['nombre_articulo'] ?? 'Pedido') . '</td>';
                                            echo '<td class="px-6 py-4">' . htmlspecialchars($row['nombre_floreria'] ?? '') . '</td>';
                                            echo '<td class="px-6 py-4"><span class="' . $badgeClass . ' px-2 py-1 rounded-full text-sm">' . $badgeText . '</span></td>';
                                            echo '<td class="px-6 py-4 font-semibold">$' . number_format($row['total'], 2) . ' MXN</td>';
                                            echo '<td class="px-6 py-4">' . $row['fecha_pedido'] . '</td>';
                                            echo '</tr>';
                                        }
                                    }
                                } else {
                                    echo '<tr><td class="px-6 py-4 text-gray-500 text-sm" colspan="6">No se encontró perfil de cliente.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div><!-- /mis-pedidos -->


                <!-- SEGUIMIENTO -->
                <div id="seguimiento" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Seguimiento de Pedidos</h2>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Buscar Pedido</h3>

                        <!-- Formulario para búsqueda -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Buscar Pedido</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Número de Pedido</label>
                                    <input type="text" id="numero_pedido_live" name="numero_pedido_live"
                                        placeholder="Ej: FE-12"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                </div>
                            </div>

                            <!-- Aquí se mostrará el resultado -->
                            <div id="resultado-pedido" class="mt-6 text-sm text-gray-700"></div>
                        </div>



                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Último pedido tuyo</h3>
                            <?php
                            // último pedido del cliente
                            $sqlUltimo = "
                                SELECT p.id_pedido, p.estado_rastreo, p.estado, p.fecha_pedido
                                FROM pedidos p
                                WHERE p.id_cliente = ?
                                ORDER BY p.fecha_pedido DESC
                                LIMIT 1
                            ";
                            if ($id_cliente) {
                                $stmtUlt = $conn->prepare($sqlUltimo);
                                $stmtUlt->bind_param("i", $id_cliente);
                                $stmtUlt->execute();
                                $resUlt = $stmtUlt->get_result();
                                $ultimo = $resUlt->fetch_assoc();
                            } else {
                                $ultimo = null;
                            }

                            if (!$ultimo) {
                                echo '<div class="text-gray-500 text-sm">Aún no hay seguimiento disponible.</div>';
                            } else {
                                echo '<div class="space-y-4">';
                                echo '<div class="flex items-center">';
                                echo '<div class="w-4 h-4 bg-verde-hoja rounded-full mr-4"></div>';
                                echo '<div class="flex-1">';
                                echo '<div class="font-semibold text-verde-hoja">Estado actual</div>';
                                echo '<div class="text-sm text-gray-600">' . htmlspecialchars($ultimo['estado_rastreo']) . '</div>';
                                echo '</div></div>';

                                echo '<div class="mt-6 p-4 bg-gray-50 rounded-lg">';
                                echo '<div class="flex items-center justify-between">';
                                echo '<span class="font-semibold">Pedido #FE-' . $ultimo['id_pedido'] . '</span>';
                                echo '<span class="text-verde-hoja font-bold">' . $ultimo['fecha_pedido'] . '</span>';
                                echo '</div></div>';

                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div><!-- /seguimiento -->
                <!-- CARRITO -->
                <div id="carrito" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mi Carrito</h2>
                    </div>

                    <!-- 🔹 CONTENEDOR INTERNO DINÁMICO (este se recarga con listar_carrito.php) -->
                    <div class="contenido-carrito">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <?php if (count($carritoItems) === 0): ?>
                                <div class="text-gray-500 text-sm">Tu carrito está vacío.</div>
                            <?php else: ?>
                                <div class="space-y-4">
                                    <?php
                                    $baseUrl = "http://localhost:8888/flor_express"; // misma base del catálogo
                                    foreach ($carritoItems as $item):
                                        $imgSrc = !empty($item['imagen_principal'])
                                            ? $baseUrl . $item['imagen_principal']
                                            : "img/placeholder-flor.jpg"; // opcional, una genérica
                                        ?>
                                        <div class="flex items-center justify-between p-4 border rounded-lg">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                                                    <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                                                        alt="<?php echo htmlspecialchars($item['nombre_articulo']); ?>"
                                                        class="w-full h-full object-cover" />
                                                </div>
                                                <div>
                                                    <h3 class="font-semibold">
                                                        <?php echo htmlspecialchars($item['nombre_articulo']); ?>
                                                    </h3>
                                                    <p class="text-gray-600">Cantidad: <?php echo $item['cantidad']; ?></p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-magenta-flor">
                                                    $<?php echo number_format($item['precio'], 2); ?> MXN
                                                </div>
                                                <button
                                                    class="btn-eliminar-carrito bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                                    data-id="<?php echo $item['id_articulo']; ?>">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="border-t pt-4 mt-4">
                                    <div class="flex justify-between items-center mb-4">
                                        <span class="text-xl font-bold">Total:</span>
                                        <span class="text-2xl font-bold text-magenta-flor">
                                            $<?php echo number_format($totalCarrito, 2); ?> MXN
                                        </span>
                                    </div>
                                    <div class="mt-8 flex justify-end">
                                        <button id="btn-ir-pago"
                                            class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">
                                            Proceder al Pago
                                        </button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- /contenido-carrito -->

                    <!-- ✅ MODAL (fuera del contenedor dinámico para que no se borre al recargar) -->
                    <div id="modal-confirmar-pedido"
                        class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl">
                            <h3 class="text-2xl font-bold mb-4">Confirmar Pedido</h3>

                            <div id="resumen-pedido" class="space-y-3 text-gray-700 mb-6">
                                <!-- Aquí se mostrará el resumen del pedido dinámicamente -->
                            </div>

                            <div class="flex justify-end gap-3">
                                <button id="btn-cancelar-pedido"
                                    class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                    Cancelar
                                </button>
                                <button id="btn-confirmar-pedido"
                                    class="px-4 py-2 bg-magenta-flor text-white rounded hover:bg-pink-600">
                                    Confirmar Pedido
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /carrito -->



                <!-- FAVORITOS -->
                <div id="favoritos" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mis Favoritos</h2>
                    </div>

                    <?php if (count($favoritosLista) === 0): ?>
                        <div class="text-gray-500 text-sm">Todavía no tienes favoritos.</div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <?php
                            $baseUrl = "http://localhost:8888/flor_express"; // ajusta según tu entorno
                            foreach ($favoritosLista as $fav):
                                // Imagen real o fallback genérico
                                $imgSrc = !empty($fav['imagen_principal'])
                                    ? $baseUrl . $fav['imagen_principal']
                                    : "img/placeholder-flor.jpg";
                                ?>
                                <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover relative">
                                    <!-- Botón de eliminar de favoritos -->
                                    <button class="btn-eliminar-favorito absolute top-3 right-3 transition"
                                        data-id="<?php echo $fav['id_articulo']; ?>" title="Quitar de favoritos">
                                        <svg class="w-7 h-7 heart-icon text-red-500 hover:text-gray-400 transition"
                                            xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-8.682a4.5 4.5 0 010-6.364z" />
                                        </svg>
                                    </button>


                                    <!-- Imagen -->
                                    <div class="h-48 w-full overflow-hidden">
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                                            alt="<?php echo htmlspecialchars($fav['nombre_articulo']); ?>"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <!-- Contenido -->
                                    <div class="p-6">
                                        <h3 class="text-xl font-semibold mb-2">
                                            <?php echo htmlspecialchars($fav['nombre_articulo']); ?>
                                        </h3>
                                        <p class="text-gray-600 mb-4">
                                            <?php echo htmlspecialchars($fav['descripcion'] ?? ''); ?>
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <span class="text-2xl font-bold text-magenta-flor">
                                                $<?php echo number_format($fav['precio'], 2); ?> MXN
                                            </span>
                                            <button
                                                class="btn-agregar-carrito bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition"
                                                data-id="<?php echo $fav['id_articulo']; ?>">
                                                Agregar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div><!-- /favoritos -->
                <!-- RESEÑAS -->
                <div id="reseñas" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mis Reseñas</h2>
                    </div>

                    <!-- SECCIÓN: PENDIENTES POR RESEÑAR -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <h3 class="text-xl font-bold mb-4">Pedidos Entregados Pendientes de Reseña</h3>
                        <div id="pendientes-reseña">
                            <div class="text-gray-500 text-sm">Cargando...</div>
                        </div>
                    </div>

                    <!-- LISTADO DE RESEÑAS -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Mis Reseñas</h3>
                        <div id="lista-reseñas"></div>
                    </div>

                    <!-- Modal reseña -->
                    <div id="modal-reseña"
                        class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                            <h3 class="text-xl font-bold mb-4">Agregar reseña</h3>
                            <form id="form-reseña">
                                <input type="hidden" name="id_pedido" id="id_pedido_reseña">
                                <input type="hidden" name="id_articulo" id="id_articulo_reseña">

                                <label class="block text-sm font-semibold mb-1">Calificación</label>
                                <select name="calificacion" class="w-full border border-gray-300 rounded-lg p-2 mb-3"
                                    required>
                                    <option value="">Selecciona una calificación</option>
                                    <option value="5">5 - Excelente</option>
                                    <option value="4">4 - Muy bueno</option>
                                    <option value="3">3 - Aceptable</option>
                                    <option value="2">2 - Regular</option>
                                    <option value="1">1 - Malo</option>
                                </select>

                                <label class="block text-sm font-semibold mb-1">Comentario</label>
                                <textarea name="comentario" rows="3"
                                    class="w-full border border-gray-300 rounded-lg p-2 mb-4"
                                    placeholder="Escribe tu opinión..."></textarea>

                                <div class="flex justify-end gap-3">
                                    <button type="button" id="btn-cancelar-reseña"
                                        class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- PERFIL -->
                <div id="perfil" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mi Perfil</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Info Personal -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                                <h3 class="text-xl font-bold mb-4">Información Personal</h3>
                                <form id="form-actualizar-perfil" class="space-y-4" enctype="multipart/form-data">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                                            <input type="text" name="nombre"
                                                value="<?php echo htmlspecialchars($cliente['nombre'] ?? ''); ?>"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-gray-700 text-sm font-bold mb-2">Apellido(s)</label>
                                            <input type="text" name="apellidos"
                                                value="<?php echo htmlspecialchars($cliente['apellidos'] ?? ''); ?>"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                                        <input type="email" name="correo"
                                            value="<?php echo htmlspecialchars($usuarioGlobal['correo'] ?? ''); ?>"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                                            readonly>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                                        <input type="tel" name="telefono"
                                            value="<?php echo htmlspecialchars($cliente['telefono'] ?? ''); ?>"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de
                                            Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento"
                                            value="<?php echo htmlspecialchars($cliente['fecha_nacimiento'] ?? ''); ?>"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>

                                    <button type="submit"
                                        class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">
                                        Actualizar Información
                                    </button>
                                </form>

                            </div>

                            <!-- Cambiar Contraseña -->
                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <h3 class="text-xl font-bold mb-4">Cambiar Contraseña</h3>
                                <form id="form-cambiar-pass" class="space-y-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña
                                            Actual</label>
                                        <input type="password" name="actual" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Nueva
                                            Contraseña</label>
                                        <input type="password" name="nueva" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Confirmar
                                            Nueva
                                            Contraseña</label>
                                        <input type="password" name="confirmar" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>

                                    <button type="submit"
                                        class="bg-magenta-flor text-white px-6 py-2 rounded-lg hover:bg-pink-600 smooth-transition">
                                        Cambiar Contraseña
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Foto + stats -->
                        <div>
                            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                                <h3 class="text-xl font-bold mb-4">Foto de Perfil</h3>
                                <div class="text-center">
                                    <div class="w-32 h-32 rounded-full mx-auto mb-4 flex items-center justify-center text-white text-4xl font-bold overflow-hidden"
                                        style="background: linear-gradient(to bottom right, #C3D600, #16a34a);">
                                        <?php if (!empty($cliente['foto_perfil'])): ?>
                                            <img src="<?php echo htmlspecialchars($cliente['foto_perfil']); ?>"
                                                class="w-full h-full object-cover" />
                                        <?php else: ?>
                                            <?php
                                            $ini = strtoupper(substr($cliente['nombre'] ?? 'U', 0, 1) . substr($cliente['apellidos'] ?? 'S', 0, 1));
                                            echo htmlspecialchars($ini);
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                    <button
                                        class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition text-sm">Cambiar
                                        Foto</button>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <h3 class="text-xl font-bold mb-4">Estadísticas</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Miembro desde:</span>
                                        <span class="font-semibold"><?php
                                        echo $cliente['fecha_creacion'] ?? $usuarioGlobal['fecha_registro'] ?? '---';
                                        ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pedidos realizados:</span>
                                        <span class="font-semibold text-verde-hoja"><?php echo $totalPedidos; ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total gastado:</span>
                                        <span
                                            class="font-semibold text-magenta-flor">$<?php echo number_format($totalGastado, 2); ?>
                                            MXN</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Puntos acumulados:</span>
                                        <span class="font-semibold text-yellow-500">2,250</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- /perfil -->
                <!-- =============================
 SECCIÓN: DIRECCIONES
============================= -->
                <div id="direcciones" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mis Direcciones</h2>
                    </div>

                    <!-- CARD principal -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Direcciones guardadas</h3>
                            <button id="btn-nueva-direccion"
                                class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition">
                                + Nueva dirección
                            </button>
                        </div>

                        <!-- LISTADO DE DIRECCIONES -->
                        <div id="lista-direcciones" class="space-y-4">
                            <div class="text-gray-500 text-sm">Cargando direcciones...</div>
                        </div>
                    </div>

                    <!-- MODAL DIRECCIÓN -->
                    <div id="modal-direccion"
                        class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
                            <h3 id="titulo-modal" class="text-xl font-semibold mb-4">Agregar dirección</h3>
                            <form id="form-direccion" class="space-y-3">
                                <input type="hidden" id="id_direccion" name="id_direccion">

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium">Nombre receptor</label>
                                        <input type="text" name="nombre_receptor" id="nombre_receptor"
                                            class="w-full border p-2 rounded" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Apellidos receptor</label>
                                        <input type="text" name="apellidos_receptor" id="apellidos_receptor"
                                            class="w-full border p-2 rounded" required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium">Teléfono</label>
                                    <input type="text" name="telefono_receptor" id="telefono_receptor"
                                        class="w-full border p-2 rounded" required>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium">Código postal</label>
                                        <input type="number" name="codigo_postal" id="codigo_postal"
                                            class="w-full border p-2 rounded" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Estado</label>
                                        <input type="text" name="estado" id="estado" class="w-full border p-2 rounded"
                                            required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium">Municipio</label>
                                        <input type="text" name="municipio" id="municipio"
                                            class="w-full border p-2 rounded" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium">Colonia</label>
                                        <input type="text" name="colonia" id="colonia" class="w-full border p-2 rounded"
                                            required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium">Calle</label>
                                    <input type="text" name="calle" id="calle" class="w-full border p-2 rounded"
                                        required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium">Referencias</label>
                                    <textarea name="referencias" id="referencias"
                                        class="w-full border p-2 rounded"></textarea>
                                </div>

                                <div class="flex justify-end gap-3 mt-4">
                                    <button type="button" id="btn-cancelar-modal"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-verde-hoja text-white rounded hover:bg-green-600">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /direcciones -->

                <!-- =============================
 SECCIÓN: MÉTODOS DE PAGO
============================= -->
                <div id="pagos" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Métodos de Pago</h2>
                    </div>

                    <!-- CARD principal -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800">Tus métodos guardados</h3>
                            <button id="btn-nuevo-pago"
                                class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition">
                                + Agregar Método
                            </button>
                        </div>

                        <div id="lista-pagos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="text-gray-500 text-sm">Cargando métodos de pago...</div>
                        </div>
                    </div>

                    <!-- MODAL DE MÉTODO -->
                    <div id="modal-pago"
                        class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
                        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
                            <h3 id="titulo-modal-pago" class="text-xl font-semibold mb-4">Agregar Método de Pago</h3>
                            <form id="form-pago" class="space-y-3">
                                <input type="hidden" id="id_metodo" name="id_metodo">

                                <div>
                                    <label class="block text-sm font-medium mb-1">Tipo</label>
                                    <select id="tipo" name="tipo" class="w-full border p-2 rounded" required>
                                        <option value="">Selecciona tipo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="paypal">PayPal</option>
                                        <option value="oxxo">Oxxo Pay</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Alias</label>
                                    <input type="text" id="alias" name="alias" placeholder="Ejemplo: Visa personal"
                                        class="w-full border p-2 rounded" required>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium mb-1">Titular</label>
                                    <input type="text" id="titular" name="titular" placeholder="Nombre del titular"
                                        class="w-full border p-2 rounded">
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Últimos 4 dígitos</label>
                                        <input type="text" id="ultimos4" name="ultimos4" maxlength="4"
                                            placeholder="1234" class="w-full border p-2 rounded">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Expiración (MM/AA)</label>
                                        <input type="text" id="expiracion" name="expiracion" maxlength="5"
                                            placeholder="12/26" class="w-full border p-2 rounded">
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" id="es_principal" name="es_principal" value="1"
                                        class="w-4 h-4">
                                    <label for="es_principal" class="text-sm">Marcar como principal</label>
                                </div>

                                <div class="flex justify-end gap-3 mt-4">
                                    <button type="button" id="btn-cancelar-pago"
                                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-verde-hoja text-white rounded hover:bg-green-600">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- /pagos -->


                <!-- NOTIFICACIONES -->
                <div id="notificaciones" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Notificaciones</h2>
                    </div>

                    <div id="lista-notificaciones" class="space-y-4">
                        <div class="text-gray-500 text-sm">Cargando notificaciones...</div>
                    </div>
                </div>
                <!-- /notificaciones -->


            </div><!-- /dashboard-content -->
        </div><!-- /flex-1 -->
    </div><!-- /flex -->

    <script src="js/dashboard.js"></script>
</body>

</html>
<?php
$conn->close();
?>