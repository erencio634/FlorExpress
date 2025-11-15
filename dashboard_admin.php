<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Flor Express</title>
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
    <!-- Mover el script de navegación al inicio para asegurar que las funciones estén definidas -->
    <script>
        // Funciones principales del dashboard - definidas globalmente al inicio
        function showAdminSection(sectionId) {
            // Hide all sections
            const sections = ['admin-dashboard', 'usuarios', 'florerias', 'catalogo', 'pedidos', 'finanzas', 'reportes', 'configuraciones', 'moderacion', 'chat'];
            sections.forEach(section => {
                const element = document.getElementById(section);
                if (element) {
                    element.classList.add('hidden');
                }
            });

            // Show selected section
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }

            // Update active button
            const navButtons = document.querySelectorAll('.admin-nav-btn');
            navButtons.forEach(btn => {
                btn.classList.remove('bg-white', 'bg-opacity-20');
                btn.classList.add('hover:bg-white', 'hover:bg-opacity-20');
            });

            // Find and highlight the active button
            const buttons = document.querySelectorAll('.admin-nav-btn');
            buttons.forEach(btn => {
                const onclick = btn.getAttribute('onclick');
                if (onclick && onclick.includes(`'${sectionId}'`)) {
                    btn.classList.add('bg-white', 'bg-opacity-20');
                    btn.classList.remove('hover:bg-white', 'hover:bg-opacity-20');
                }
            });
        }

        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                window.location.href = 'logout.php'; // o la página que tengas para logout
            }
        }

        function showCreateUserModal() {
            document.getElementById('create-user-modal').classList.remove('hidden');
        }

        function hideCreateUserModal() {
            document.getElementById('create-user-modal').classList.add('hidden');
        }

        function showCreateProductModal() {
            document.getElementById('create-product-modal').classList.remove('hidden');
        }

        function hideCreateProductModal() {
            document.getElementById('create-product-modal').classList.add('hidden');
        }

        // Initialize dashboard when page loads
        document.addEventListener('DOMContentLoaded', function () {
            showAdminSection('admin-dashboard');
        });
    </script>

    <div class="flex">
        <!-- Sidebar Admin -->
        <div class="w-64 gradient-bg text-white h-screen sticky top-0 overflow-y-auto">
            <div class="p-6">
                <div class="text-2xl font-bold mb-2">Flor Express</div>
                <div class="text-sm opacity-75 mb-8">Administrador</div>
                <nav class="space-y-2">
                    <button onclick="showAdminSection('admin-dashboard')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg bg-white bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Dashboard</span>
                    </button>
                    <button onclick="showAdminSection('usuarios')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Gestión de Usuarios</span>
                    </button>
                    <button onclick="showAdminSection('florerias')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Gestión de Florerías</span>
                    </button>
                    <button onclick="showAdminSection('catalogo')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Gestión de Catálogo</span>
                    </button>
                    <button onclick="showAdminSection('pedidos')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Gestión de Pedidos</span>
                    </button>
                    <button onclick="showAdminSection('finanzas')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Finanzas</span>
                    </button>
                    <button onclick="showAdminSection('reportes')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Reportes</span>
                    </button>
                    <button onclick="showAdminSection('configuraciones')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Configuraciones</span>
                    </button>
                    <button onclick="showAdminSection('moderacion')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Moderación</span>
                    </button>
                    <button onclick="showAdminSection('chat')"
                        class="admin-nav-btn w-full text-left px-4 py-2 rounded-lg hover:bg-white hover:bg-opacity-20 smooth-transition flex items-center space-x-2">
                        <span></span>
                        <span>Chat con Florerías</span>
                    </button>
                </nav>
                <button onclick="logout()"
                    class="mt-8 bg-magenta-flor text-white px-4 py-2 rounded-lg hover:bg-pink-600 smooth-transition w-full">Cerrar
                    Sesión</button>
            </div>
        </div>

        <!-- Main Content Admin -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div id="admin-content">
                <!-- Dashboard Admin -->
                <div id="admin-dashboard">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-2">Dashboard Administrativo</h2>
                        <p class="text-gray-600">Panel de control general del sistema</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-verde-hoja text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">👥</div>
                            <div class="text-2xl font-bold">1,234</div>
                            <div class="text-sm opacity-90">Usuarios Activos</div>
                        </div>
                        <div class="bg-magenta-flor text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">🏪</div>
                            <div class="text-2xl font-bold">89</div>
                            <div class="text-sm opacity-90">Florerías Registradas</div>
                        </div>
                        <div class="bg-blue-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">📦</div>
                            <div class="text-2xl font-bold">5,678</div>
                            <div class="text-sm opacity-90">Pedidos del Mes</div>
                        </div>
                        <div class="bg-purple-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="text-2xl font-bold">$12.5M</div>
                            <div class="text-sm opacity-90">Ingresos Totales</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Actividad Reciente</h3>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3 text-sm">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    <span>Nueva florería registrada</span>
                                </div>
                                <div class="flex items-center space-x-3 text-sm">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    <span>156 pedidos completados hoy</span>
                                </div>
                                <div class="flex items-center space-x-3 text-sm">
                                    <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                    <span>3 reportes pendientes</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Top Florerías</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm">Bella Rosa</span>
                                    <span class="text-sm font-bold">4.9 ⭐</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Flores del Campo</span>
                                    <span class="text-sm font-bold">4.8 ⭐</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm">Jardín Secreto</span>
                                    <span class="text-sm font-bold">4.7 ⭐</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Alertas del Sistema</h3>
                            <div class="space-y-2">
                                <div class="bg-red-50 text-red-700 p-2 rounded text-sm">
                                    2 florerías inactivas
                                </div>
                                <div class="bg-yellow-50 text-yellow-700 p-2 rounded text-sm">
                                    Mantenimiento programado
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Usuarios (usuarios_globales) -->
                <div id="usuarios" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Gestión de Usuarios</h2>
                        <button onclick="document.getElementById('modalCrearUsuario').classList.remove('hidden')"
                            class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition">
                            Crear Usuario
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Correo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activo
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">F.
                                        Registro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Último
                                        Acceso</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                require_once("conexion.php");
                                $q = $conn->query("SELECT id_usuario, correo, rol, fecha_registro, ultimo_acceso, activo
                                   FROM usuarios_globales
                                   ORDER BY id_usuario DESC");
                                if ($q && $q->num_rows > 0):
                                    while ($u = $q->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td class="px-6 py-4 font-medium">#<?php echo $u['id_usuario']; ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($u['correo']); ?></td>
                                            <td class="px-6 py-4 capitalize"><?php echo htmlspecialchars($u['rol']); ?></td>
                                            <td class="px-6 py-4">
                                                <?php if ((int) $u['activo'] === 1): ?>
                                                    <span
                                                        class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Activo</span>
                                                <?php else: ?>
                                                    <span
                                                        class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <?php echo $u['fecha_registro'] ?: '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <?php echo $u['ultimo_acceso'] ?: '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 space-x-2">
                                                <button onclick="abrirEditarUsuario(<?php echo (int) $u['id_usuario']; ?>,
                              '<?php echo htmlspecialchars($u['correo'], ENT_QUOTES); ?>',
                              '<?php echo htmlspecialchars($u['rol'], ENT_QUOTES); ?>',
                              <?php echo (int) $u['activo']; ?>)"
                                                    class="text-verde-hoja hover:underline">Editar</button>

                                                <form action="actions/gestionar_usuarios.php" method="POST" class="inline"
                                                    onsubmit="return confirm('¿Eliminar este usuario?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_usuario"
                                                        value="<?php echo (int) $u['id_usuario']; ?>">
                                                    <button type="submit" class="text-red-500 hover:underline">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td class="px-6 py-6 text-center text-gray-500" colspan="7">Sin usuarios</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal: Crear Usuario -->
                    <div id="modalCrearUsuario"
                        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                            <h3 class="text-xl font-bold mb-4">Crear Usuario</h3>
                            <button onclick="document.getElementById('modalCrearUsuario').classList.add('hidden')"
                                class="absolute top-3 right-4 text-gray-500">✕</button>

                            <form action="actions/gestionar_usuarios.php" method="POST" class="space-y-4">
                                <input type="hidden" name="accion" value="agregar">

                                <div>
                                    <label class="block text-sm font-semibold">Correo</label>
                                    <input type="email" name="correo" required class="w-full border rounded px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold">Contraseña</label>
                                    <input type="text" name="contrasena" required
                                        class="w-full border rounded px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold">Rol</label>
                                    <select name="rol" class="w-full border rounded px-3 py-2">
                                        <option value="cliente">cliente</option>
                                        <option value="floreria">floreria</option>
                                        <option value="admin">admin</option>
                                        <option value="superadmin">superadmin</option>
                                    </select>
                                </div>

                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox" name="activo" class="rounded" checked>
                                    <span class="text-sm">Activo</span>
                                </label>

                                <div class="flex justify-end space-x-2">
                                    <button type="button"
                                        onclick="document.getElementById('modalCrearUsuario').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal: Editar Usuario -->
                    <div id="modalEditarUsuario"
                        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                            <h3 class="text-xl font-bold mb-4">Editar Usuario</h3>
                            <button onclick="document.getElementById('modalEditarUsuario').classList.add('hidden')"
                                class="absolute top-3 right-4 text-gray-500">✕</button>

                            <form action="actions/gestionar_usuarios.php" method="POST" class="space-y-4">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" id="edit-id_usuario" name="id_usuario">

                                <div>
                                    <label class="block text-sm font-semibold">Correo</label>
                                    <input type="email" id="edit-correo" name="correo" required
                                        class="w-full border rounded px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold">Nueva contraseña (opcional)</label>
                                    <input type="text" id="edit-contrasena" name="contrasena"
                                        placeholder="Dejar vacío para no cambiar"
                                        class="w-full border rounded px-3 py-2">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold">Rol</label>
                                    <select id="edit-rol" name="rol" class="w-full border rounded px-3 py-2">
                                        <option value="cliente">cliente</option>
                                        <option value="floreria">floreria</option>
                                        <option value="admin">admin</option>
                                        <option value="superadmin">superadmin</option>
                                    </select>
                                </div>

                                <label class="inline-flex items-center space-x-2">
                                    <input type="checkbox" id="edit-activo" name="activo" class="rounded">
                                    <span class="text-sm">Activo</span>
                                </label>

                                <div class="flex justify-end space-x-2">
                                    <button type="button"
                                        onclick="document.getElementById('modalEditarUsuario').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <script>
                        function abrirEditarUsuario(id, correo, rol, activo) {
                            document.getElementById('edit-id_usuario').value = id;
                            document.getElementById('edit-correo').value = correo;
                            document.getElementById('edit-rol').value = rol;
                            document.getElementById('edit-activo').checked = (parseInt(activo) === 1);
                            document.getElementById('edit-contrasena').value = ''; // limpia
                            document.getElementById('modalEditarUsuario').classList.remove('hidden');
                        }
                    </script>
                </div>

                <!-- Gestión de Florerías -->
                <div id="florerias" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Gestión de Florerías</h2>
                        <button onclick="document.getElementById('modalCrearFloreria').classList.remove('hidden')"
                            class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition">
                            + Crear Florería
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Ubicación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Capacidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estatus
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                        Registro</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                $sqlFlorerias = "SELECT id_floreria, id_usuario, nombre_floreria, foto_perfil_f, 
                                                       descripcion, correo_contacto, telefono, direccion_floreria, 
                                                       estado, municipio, estatus, capacidad_diaria, pedidos_actuales, 
                                                       fecha_creacion
                                               FROM florerias
                                               ORDER BY id_floreria DESC";
                                $qFlorerias = $conn->query($sqlFlorerias);

                                if ($qFlorerias && $qFlorerias->num_rows > 0):
                                    while ($f = $qFlorerias->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td class="px-6 py-4 font-medium">#<?php echo $f['id_floreria']; ?></td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-2">
                                                    <?php if (!empty($f['foto_perfil_f'])): ?>
                                                        <img src="<?php echo htmlspecialchars($f['foto_perfil_f']); ?>"
                                                            class="w-8 h-8 rounded-full object-cover" />
                                                    <?php else: ?>
                                                        <div
                                                            class="w-8 h-8 rounded-full bg-verde-hoja text-white flex items-center justify-center text-xs font-bold">
                                                            <?php echo strtoupper(substr($f['nombre_floreria'], 0, 2)); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span><?php echo htmlspecialchars($f['nombre_floreria']); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div><?php echo htmlspecialchars($f['correo_contacto'] ?? '-'); ?></div>
                                                <div class="text-gray-500">
                                                    <?php echo htmlspecialchars($f['telefono'] ?? '-'); ?></div>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div><?php echo htmlspecialchars($f['municipio'] ?? '-'); ?></div>
                                                <div class="text-gray-500"><?php echo htmlspecialchars($f['estado'] ?? '-'); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-center">
                                                <div class="font-semibold"><?php echo $f['pedidos_actuales'] ?? 0; ?> /
                                                    <?php echo $f['capacidad_diaria'] ?? 0; ?></div>
                                                <div class="text-xs text-gray-500">actual / diaria</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if ($f['estatus'] === 'activa'): ?>
                                                    <span
                                                        class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Activa</span>
                                                <?php else: ?>
                                                    <span
                                                        class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">Inactiva</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                <?php echo $f['fecha_creacion'] ? date('d/m/Y', strtotime($f['fecha_creacion'])) : '-'; ?>
                                            </td>
                                            <td class="px-6 py-4 space-x-2">
                                                <button onclick='abrirEditarFloreria(<?php echo json_encode($f); ?>)'
                                                    class="text-verde-hoja hover:underline">Editar</button>

                                                <form action="actions/gestionar_florerias.php" method="POST" class="inline"
                                                    onsubmit="return confirm('¿Eliminar esta florería y todos sus datos relacionados?');">
                                                    <input type="hidden" name="accion" value="eliminar">
                                                    <input type="hidden" name="id_floreria"
                                                        value="<?php echo (int) $f['id_floreria']; ?>">
                                                    <button type="submit" class="text-red-500 hover:underline">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td class="px-6 py-6 text-center text-gray-500" colspan="8">No hay florerías
                                            registradas</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal actualizado para crear florería con todos los campos de la BD -->
                    <div id="modalCrearFloreria"
                        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-screen overflow-y-auto relative">
                            <h3 class="text-xl font-bold mb-4">Crear Florería</h3>
                            <button onclick="document.getElementById('modalCrearFloreria').classList.add('hidden')"
                                class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-2xl">✕</button>

                            <form action="actions/gestionar_florerias.php" method="POST" enctype="multipart/form-data"
                                class="space-y-4">
                                <input type="hidden" name="accion" value="agregar">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">ID Usuario (opcional)</label>
                                        <input type="number" name="id_usuario" class="w-full border rounded px-3 py-2"
                                            placeholder="Dejar vacío si no tiene usuario asignado">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Nombre de la Florería *</label>
                                        <input type="text" name="nombre_floreria" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Descripción</label>
                                    <textarea name="descripcion" rows="2" class="w-full border rounded px-3 py-2"
                                        placeholder="Breve descripción de la florería..."></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Correo de Contacto *</label>
                                        <input type="email" name="correo_contacto" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Teléfono *</label>
                                        <input type="text" name="telefono" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Dirección Completa *</label>
                                    <input type="text" name="direccion_floreria" required
                                        class="w-full border rounded px-3 py-2">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Estado *</label>
                                        <input type="text" name="estado" required
                                            class="w-full border rounded px-3 py-2" placeholder="Ej: Michoacán">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Municipio *</label>
                                        <input type="text" name="municipio" required
                                            class="w-full border rounded px-3 py-2" placeholder="Ej: Ciudad Hidalgo">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Longitud (GPS)</label>
                                        <input type="text" name="longitud" class="w-full border rounded px-3 py-2"
                                            placeholder="Ej: -100.556450">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Latitud (GPS)</label>
                                        <input type="text" name="latitud" class="w-full border rounded px-3 py-2"
                                            placeholder="Ej: 19.694980">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Capacidad Diaria *</label>
                                        <input type="number" name="capacidad_diaria" required value="25"
                                            class="w-full border rounded px-3 py-2" min="1">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Estatus</label>
                                        <select name="estatus" class="w-full border rounded px-3 py-2">
                                            <option value="activa">Activa</option>
                                            <option value="inactiva">Inactiva</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Foto de Perfil</label>
                                        <input type="file" name="foto_perfil_f" accept="image/*"
                                            class="w-full border rounded px-3 py-2">
                                        <p class="text-xs text-gray-500 mt-1">Logo o imagen de perfil de la florería</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Foto del Local</label>
                                        <input type="file" name="foto_floreria" accept="image/*"
                                            class="w-full border rounded px-3 py-2">
                                        <p class="text-xs text-gray-500 mt-1">Foto del establecimiento</p>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2 pt-4 border-t">
                                    <button type="button"
                                        onclick="document.getElementById('modalCrearFloreria').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Guardar
                                        Florería</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal actualizado para editar florería -->
                    <div id="modalEditarFloreria"
                        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg p-6 w-full max-w-3xl max-h-screen overflow-y-auto relative">
                            <h3 class="text-xl font-bold mb-4">Editar Florería</h3>
                            <button onclick="document.getElementById('modalEditarFloreria').classList.add('hidden')"
                                class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-2xl">✕</button>

                            <form action="actions/gestionar_florerias.php" method="POST" enctype="multipart/form-data"
                                class="space-y-4">
                                <input type="hidden" name="accion" value="editar">
                                <input type="hidden" id="edit-id_floreria" name="id_floreria">

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">ID Usuario</label>
                                        <input type="number" id="edit-id_usuario" name="id_usuario"
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Nombre de la Florería *</label>
                                        <input type="text" id="edit-nombre_floreria" name="nombre_floreria" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Descripción</label>
                                    <textarea id="edit-descripcion" name="descripcion" rows="2"
                                        class="w-full border rounded px-3 py-2"></textarea>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Correo de Contacto *</label>
                                        <input type="email" id="edit-correo_contacto" name="correo_contacto" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Teléfono *</label>
                                        <input type="text" id="edit-telefono" name="telefono" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Dirección Completa *</label>
                                    <input type="text" id="edit-direccion_floreria" name="direccion_floreria" required
                                        class="w-full border rounded px-3 py-2">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Estado *</label>
                                        <input type="text" id="edit-estado" name="estado" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Municipio *</label>
                                        <input type="text" id="edit-municipio" name="municipio" required
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Longitud (GPS)</label>
                                        <input type="text" id="edit-longitud" name="longitud"
                                            class="w-full border rounded px-3 py-2">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Latitud (GPS)</label>
                                        <input type="text" id="edit-latitud" name="latitud"
                                            class="w-full border rounded px-3 py-2">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Capacidad Diaria *</label>
                                        <input type="number" id="edit-capacidad_diaria" name="capacidad_diaria" required
                                            class="w-full border rounded px-3 py-2" min="1">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Pedidos Actuales</label>
                                        <input type="number" id="edit-pedidos_actuales" name="pedidos_actuales"
                                            class="w-full border rounded px-3 py-2" min="0" readonly>
                                        <p class="text-xs text-gray-500 mt-1">Solo lectura (se actualiza
                                            automáticamente)</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-1">Estatus</label>
                                    <select id="edit-estatus" name="estatus" class="w-full border rounded px-3 py-2">
                                        <option value="activa">Activa</option>
                                        <option value="inactiva">Inactiva</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Nueva Foto de Perfil</label>
                                        <input type="file" name="foto_perfil_f" accept="image/*"
                                            class="w-full border rounded px-3 py-2">
                                        <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la actual</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold mb-1">Nueva Foto del Local</label>
                                        <input type="file" name="foto_floreria" accept="image/*"
                                            class="w-full border rounded px-3 py-2">
                                        <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la actual</p>
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2 pt-4 border-t">
                                    <button type="button"
                                        onclick="document.getElementById('modalEditarFloreria').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancelar</button>
                                    <button type="submit"
                                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Actualizar
                                        Florería</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Script actualizado para manejar la edición -->
                    <script>
                        function abrirEditarFloreria(floreria) {
                            document.getElementById('edit-id_floreria').value = floreria.id_floreria || '';
                            document.getElementById('edit-id_usuario').value = floreria.id_usuario || '';
                            document.getElementById('edit-nombre_floreria').value = floreria.nombre_floreria || '';
                            document.getElementById('edit-descripcion').value = floreria.descripcion || '';
                            document.getElementById('edit-correo_contacto').value = floreria.correo_contacto || '';
                            document.getElementById('edit-telefono').value = floreria.telefono || '';
                            document.getElementById('edit-direccion_floreria').value = floreria.direccion_floreria || '';
                            document.getElementById('edit-estado').value = floreria.estado || '';
                            document.getElementById('edit-municipio').value = floreria.municipio || '';
                            document.getElementById('edit-longitud').value = floreria.longitud || '';
                            document.getElementById('edit-latitud').value = floreria.latitud || '';
                            document.getElementById('edit-capacidad_diaria').value = floreria.capacidad_diaria || 25;
                            document.getElementById('edit-pedidos_actuales').value = floreria.pedidos_actuales || 0;
                            document.getElementById('edit-estatus').value = floreria.estatus || 'activa';

                            document.getElementById('modalEditarFloreria').classList.remove('hidden');
                        }
                    </script>
                </div>

                <!-- Gestión de Catálogo -->
                <div id="catalogo" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Gestión de Catálogo</h2>
                        <button onclick="document.getElementById('modalAgregar').classList.remove('hidden')"
                            class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition">Agregar
                            Producto</button>
                    </div>

                    <!-- Listado de productos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php
                        require_once("conexion.php");
                        $result = $conn->query("SELECT * FROM catalogo ORDER BY id_articulo DESC");
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "
                <div class='bg-white rounded-lg shadow-lg overflow-hidden'>
                    <img src='{$row['imagen_principal']}' class='w-full h-48 object-cover'>
                    <div class='p-6'>
                        <h3 class='text-xl font-semibold mb-2'>{$row['nombre_articulo']}</h3>
                        <p class='text-gray-600 mb-4'>{$row['descripcion']}</p>
                        <div class='flex justify-between items-center mb-2'>
                            <span class='text-lg font-bold text-magenta-flor'>\$ {$row['precio']}</span>
                            <span class='bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm'>{$row['categoria']}</span>
                        </div>
                        <div class='flex space-x-2 mt-4'>
                            <button onclick='openEditModal({$row['id_articulo']}, \"{$row['nombre_articulo']}\", \"{$row['descripcion']}\", \"{$row['categoria']}\", {$row['precio']})' 
                                class=\"bg-verde-hoja text-white px-3 py-1 rounded text-sm flex-1 hover:bg-green-600\">Editar</button>

                            <form action='actions/gestionar_catalogo.php' method='POST' onsubmit='return confirm(\"¿Eliminar este producto?\")' class='flex-1'>
                                <input type='hidden' name='accion' value='eliminar'>
                                <input type='hidden' name='id_articulo' value='{$row['id_articulo']}'>
                                <button type='submit' class='bg-red-500 text-white px-3 py-1 rounded text-sm w-full hover:bg-red-600'>Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>";
                            }
                        } else {
                            echo "<p class='text-gray-500'>No hay productos en el catálogo.</p>";
                        }
                        ?>
                    </div>
                </div>

                <!-- Modal para agregar producto -->
                <div id="modalAgregar"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Agregar Producto</h3>
                        <button onclick="document.getElementById('modalAgregar').classList.add('hidden')"
                            class="absolute top-3 right-4 text-gray-500">✕</button>

                        <form action="actions/gestionar_catalogo.php" method="POST" enctype="multipart/form-data"
                            class="space-y-4">
                            <input type="hidden" name="accion" value="agregar">
                            <input type="hidden" name="id_floreria" value="1">

                            <div>
                                <label class="block text-sm font-semibold">Nombre</label>
                                <input type="text" name="nombre_articulo" required
                                    class="w-full border rounded px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Descripción</label>
                                <textarea name="descripcion" rows="2" required
                                    class="w-full border rounded px-3 py-2"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Categoría</label>
                                <input type="text" name="categoria" required class="w-full border rounded px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Precio</label>
                                <input type="number" step="0.01" name="precio" required
                                    class="w-full border rounded px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Imagen principal</label>
                                <input type="file" name="imagen_principal" accept="image/*" required>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button"
                                    onclick="document.getElementById('modalAgregar').classList.add('hidden')"
                                    class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal para editar producto -->
                <div id="modalEditar"
                    class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800">Editar Producto</h3>
                        <button onclick="document.getElementById('modalEditar').classList.add('hidden')"
                            class="absolute top-3 right-4 text-gray-500">✕</button>

                        <form id="formEditar" action="actions/gestionar_catalogo.php" method="POST"
                            enctype="multipart/form-data" class="space-y-4">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" id="edit-id" name="id_articulo">

                            <div>
                                <label class="block text-sm font-semibold">Nombre</label>
                                <input type="text" id="edit-nombre" name="nombre_articulo" required
                                    class="w-full border rounded px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Descripción</label>
                                <textarea id="edit-descripcion" name="descripcion" rows="2" required
                                    class="w-full border rounded px-3 py-2"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Categoría</label>
                                <input type="text" id="edit-categoria" name="categoria" required
                                    class="w-full border rounded px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Precio</label>
                                <input type="number" id="edit-precio" step="0.01" name="precio" required
                                    class="w-full border rounded px-3 py-2">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold">Nueva imagen (opcional)</label>
                                <input type="file" name="imagen_principal" accept="image/*">
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button"
                                    onclick="document.getElementById('modalEditar').classList.add('hidden')"
                                    class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function openEditModal(id, nombre, descripcion, categoria, precio) {
                        document.getElementById('edit-id').value = id;
                        document.getElementById('edit-nombre').value = nombre;
                        document.getElementById('edit-descripcion').value = descripcion;
                        document.getElementById('edit-categoria').value = categoria;
                        document.getElementById('edit-precio').value = precio;
                        document.getElementById('modalEditar').classList.remove('hidden');
                    }
                </script>


                <!-- Gestión de Pedidos -->
                <div id="pedidos" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Gestión de Pedidos</h2>
                        <div class="flex space-x-4 mb-6">
                            <button onclick="filtrarPedidos('todos')"
                                class="filtro-pedido bg-verde-hoja text-white px-4 py-2 rounded-lg"
                                data-estado="todos">Todos</button>
                            <button onclick="filtrarPedidos('pendiente')"
                                class="filtro-pedido bg-gray-200 text-gray-700 px-4 py-2 rounded-lg"
                                data-estado="pendiente">Pendientes</button>
                            <button onclick="filtrarPedidos('en_proceso')"
                                class="filtro-pedido bg-gray-200 text-gray-700 px-4 py-2 rounded-lg"
                                data-estado="en_proceso">En Proceso</button>
                            <button onclick="filtrarPedidos('completado')"
                                class="filtro-pedido bg-gray-200 text-gray-700 px-4 py-2 rounded-lg"
                                data-estado="completado">Completados</button>
                            <button onclick="filtrarPedidos('cancelado')"
                                class="filtro-pedido bg-gray-200 text-gray-700 px-4 py-2 rounded-lg"
                                data-estado="cancelado">Cancelados</button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Florería
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="tablaPedidos">
                                <?php
                                $sqlPedidos = "SELECT p.*, 
                                                      u.correo as correo_cliente,
                                                      (SELECT GROUP_CONCAT(DISTINCT f.nombre_floreria SEPARATOR ', ')
                                                       FROM detalles_pedido dp
                                                       LEFT JOIN florerias f ON dp.id_floreria = f.id_floreria
                                                       WHERE dp.id_pedido = p.id_pedido) as nombre_floreria
                                               FROM pedidos p
                                               LEFT JOIN usuarios_globales u ON p.id_cliente = u.id_usuario
                                               ORDER BY p.fecha_pedido DESC";
                                $qPedidos = $conn->query($sqlPedidos);

                                if ($qPedidos && $qPedidos->num_rows > 0):
                                    while ($p = $qPedidos->fetch_assoc()):
                                        // Determinar color del badge según estado
                                        $estadoClass = '';
                                        $estadoTexto = ucfirst(str_replace('_', ' ', $p['estado']));
                                        switch ($p['estado']) {
                                            case 'confirmado':
                                            case 'pendiente':
                                                $estadoClass = 'bg-blue-100 text-blue-800';
                                                $estadoTexto = 'Confirmado';
                                                break;
                                            case 'preparando':
                                            case 'en_proceso':
                                                $estadoClass = 'bg-yellow-100 text-yellow-800';
                                                $estadoTexto = 'Preparando';
                                                break;
                                            case 'enviado':
                                                $estadoClass = 'bg-purple-100 text-purple-800';
                                                $estadoTexto = 'Enviado';
                                                break;
                                            case 'entregado':
                                            case 'completado':
                                                $estadoClass = 'bg-green-100 text-green-800';
                                                $estadoTexto = 'Entregado';
                                                break;
                                            case 'cancelado':
                                                $estadoClass = 'bg-red-100 text-red-800';
                                                $estadoTexto = 'Cancelado';
                                                break;
                                            default:
                                                $estadoClass = 'bg-gray-100 text-gray-800';
                                        }
                                        ?>
                                        <tr data-estado="<?php echo htmlspecialchars($p['estado']); ?>">
                                            <td class="px-6 py-4 font-medium">#<?php echo $p['id_pedido']; ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($p['correo_cliente'] ?? 'N/A'); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($p['nombre_floreria'] ?? 'Sin asignar'); ?></td>
                                            <td class="px-6 py-4">
                                                <span class="<?php echo $estadoClass; ?> px-2 py-1 rounded-full text-sm">
                                                    <?php echo $estadoTexto; ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 font-semibold">$<?php echo number_format($p['total'], 2); ?>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo date('d/m/Y H:i', strtotime($p['fecha_pedido'])); ?></td>
                                            <td class="px-6 py-4">
                                                <button
                                                    onclick='verDetallePedido(<?php echo json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'
                                                    class="text-verde-hoja hover:underline mr-2">Ver</button>
                                                <?php if ($p['estado'] !== 'cancelado' && $p['estado'] !== 'entregado' && $p['estado'] !== 'completado'): ?>
                                                    <form action="actions/gestionar_pedidos.php" method="POST" class="inline"
                                                        onsubmit="return confirm('¿Estás seguro de cancelar este pedido?');">
                                                        <input type="hidden" name="accion" value="cancelar">
                                                        <input type="hidden" name="id_pedido"
                                                            value="<?php echo $p['id_pedido']; ?>">
                                                        <button type="submit" class="text-red-500 hover:underline">Cancelar</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-gray-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td class="px-6 py-6 text-center text-gray-500" colspan="7">No hay pedidos
                                            registrados</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Finanzas -->
                <div id="finanzas" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Finanzas</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Resumen Financiero</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ingresos del Mes:</span>
                                    <span class="font-bold text-green-600">$12.5M</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Comisiones del Mes:</span>
                                    <span class="font-bold text-blue-600">$1.25M</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Gastos Operativos:</span>
                                    <span class="font-bold text-red-600">$850K</span>
                                </div>
                                <div class="flex justify-between border-t pt-2">
                                    <span class="text-gray-600 font-semibold">Utilidad Neta:</span>
                                    <span class="font-bold text-verde-hoja">$10.4M</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Distribución de Ingresos</h3>
                            <div class="h-48 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-4xl mb-2">📊</div>
                                    <p class="text-gray-600">Gráfico de distribución</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Transacciones Recientes</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 border rounded-lg">
                                <div>
                                    <div class="font-semibold">Comisión - Florería Bella Rosa</div>
                                    <div class="text-sm text-gray-600">Pedido #FE-001</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-600">+$4.500</div>
                                    <div class="text-sm text-gray-600">15 Ene 2024</div>
                                </div>
                            </div>
                            <div class="flex justify-between items-center p-3 border rounded-lg">
                                <div>
                                    <div class="font-semibold">Comisión - Flores del Campo</div>
                                    <div class="text-sm text-gray-600">Pedido #FE-002</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-600">+$8.500</div>
                                    <div class="text-sm text-gray-600">16 Ene 2024</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reportes -->
                <!-- Reportes -->
                <div id="reportes" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Sistema de Reportes</h2>
                        <p class="text-gray-600">Genera reportes detallados del sistema en diferentes formatos</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Formulario de Generación de Reportes -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <h3 class="text-xl font-bold mb-4">Generar Reporte</h3>
                                <form id="form-generar-reporte" action="actions/generar_reporte.php" method="POST"
                                    target="_blank" class="space-y-6">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de
                                            Reporte</label>
                                        <select name="tipo_reporte" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                            <option value="">Selecciona un tipo de reporte</option>
                                            <option value="ventas_floreria">📊 Ventas por Florería</option>
                                            <option value="usuarios_activos">👥 Usuarios Activos</option>
                                            <option value="pedidos_completados">📦 Pedidos Completados</option>
                                            <option value="ingresos_mensuales">💰 Ingresos Mensuales</option>
                                            <option value="productos_vendidos">🌸 Productos Más Vendidos</option>
                                            <option value="florerias_desempeno">🏆 Florerías con Mejor Desempeño
                                            </option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Fecha
                                                Inicio</label>
                                            <input type="date" name="fecha_inicio"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                                                value="<?php echo date('Y-m-01'); ?>">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Fecha Fin</label>
                                            <input type="date" name="fecha_fin"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                                                value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Formato de
                                            Salida</label>
                                        <div class="grid grid-cols-3 gap-4">
                                            <label
                                                class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                                <input type="radio" name="formato" value="html" checked class="mr-3">
                                                <div>
                                                    <div class="font-semibold">Vista Previa</div>
                                                    <div class="text-sm text-gray-600">HTML</div>
                                                </div>
                                            </label>
                                            <label
                                                class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                                <input type="radio" name="formato" value="excel" class="mr-3">
                                                <div>
                                                    <div class="font-semibold">Excel</div>
                                                    <div class="text-sm text-gray-600">.xls</div>
                                                </div>
                                            </label>
                                            <label
                                                class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                                                <input type="radio" name="formato" value="pdf" class="mr-3">
                                                <div>
                                                    <div class="font-semibold">PDF</div>
                                                    <div class="text-sm text-gray-600">.pdf</div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex space-x-4">
                                        <button type="submit"
                                            class="flex-1 bg-verde-hoja text-white px-6 py-3 rounded-lg hover:bg-green-600 smooth-transition font-semibold">
                                            🚀 Generar Reporte
                                        </button>
                                        <button type="button" onclick="resetearFormulario()"
                                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 smooth-transition">
                                            🔄 Limpiar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Estadísticas Rápidas -->
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Estadísticas Rápidas</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <div class="font-semibold text-green-800">Pedidos Hoy</div>
                                        <div class="text-2xl font-bold text-green-600">
                                            <?php
                                            $hoy = date('Y-m-d');
                                            $sql = "SELECT COUNT(*) as total FROM pedidos WHERE DATE(fecha_pedido) = '$hoy'";
                                            $result = $conn->query($sql);
                                            echo $result->fetch_assoc()['total'] ?? 0;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="text-3xl">📦</div>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <div class="font-semibold text-blue-800">Ingresos del Mes</div>
                                        <div class="text-2xl font-bold text-blue-600">
                                            $<?php
                                            $mes_actual = date('Y-m');
                                            $sql = "SELECT SUM(total) as total FROM pedidos WHERE DATE_FORMAT(fecha_pedido, '%Y-%m') = '$mes_actual' AND estado IN ('entregado', 'completado')";
                                            $result = $conn->query($sql);
                                            echo number_format($result->fetch_assoc()['total'] ?? 0, 2);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="text-3xl">💰</div>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                    <div>
                                        <div class="font-semibold text-purple-800">Usuarios Activos</div>
                                        <div class="text-2xl font-bold text-purple-600">
                                            <?php
                                            $sql = "SELECT COUNT(*) as total FROM usuarios_globales WHERE activo = 1 AND ultimo_acceso >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                                            $result = $conn->query($sql);
                                            echo $result->fetch_assoc()['total'] ?? 0;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="text-3xl">👥</div>
                                </div>

                                <div class="flex justify-between items-center p-3 bg-orange-50 rounded-lg">
                                    <div>
                                        <div class="font-semibold text-orange-800">Florerías Activas</div>
                                        <div class="text-2xl font-bold text-orange-600">
                                            <?php
                                            $sql = "SELECT COUNT(*) as total FROM florerias WHERE estatus = 'activa'";
                                            $result = $conn->query($sql);
                                            echo $result->fetch_assoc()['total'] ?? 0;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="text-3xl">🏪</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reportes Predefinidos Rápidos -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold mb-4">Reportes Rápidos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <button onclick="generarReporteRapido('ventas_floreria')"
                                class="p-4 bg-white rounded-lg shadow-lg hover:shadow-xl smooth-transition text-left">
                                <div class="text-2xl mb-2">📊</div>
                                <div class="font-semibold">Ventas por Florería</div>
                                <div class="text-sm text-gray-600">Este mes</div>
                            </button>

                            <button onclick="generarReporteRapido('productos_vendidos')"
                                class="p-4 bg-white rounded-lg shadow-lg hover:shadow-xl smooth-transition text-left">
                                <div class="text-2xl mb-2">🌸</div>
                                <div class="font-semibold">Productos Top</div>
                                <div class="text-sm text-gray-600">Más vendidos</div>
                            </button>

                            <button onclick="generarReporteRapido('usuarios_activos')"
                                class="p-4 bg-white rounded-lg shadow-lg hover:shadow-xl smooth-transition text-left">
                                <div class="text-2xl mb-2">👥</div>
                                <div class="font-semibold">Usuarios Activos</div>
                                <div class="text-sm text-gray-600">Últimos 30 días</div>
                            </button>

                            <button onclick="generarReporteRapido('florerias_desempeno')"
                                class="p-4 bg-white rounded-lg shadow-lg hover:shadow-xl smooth-transition text-left">
                                <div class="text-2xl mb-2">🏆</div>
                                <div class="font-semibold">Ranking Florerías</div>
                                <div class="text-sm text-gray-600">Por desempeño</div>
                            </button>
                        </div>
                    </div>

                    <!-- Historial de Reportes Generados -->
                    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Reportes Recientes</h3>
                        <div class="space-y-3">
                            <?php
                            // Simular historial de reportes - en producción esto vendría de una tabla
                            $reportes_recientes = [
                                ['tipo' => 'Ventas por Florería', 'fecha' => date('d/m/Y H:i', strtotime('-1 hour')), 'formato' => 'Excel'],
                                ['tipo' => 'Usuarios Activos', 'fecha' => date('d/m/Y H:i', strtotime('-3 hours')), 'formato' => 'PDF'],
                                ['tipo' => 'Productos Más Vendidos', 'fecha' => date('d/m/Y H:i', strtotime('-1 day')), 'formato' => 'HTML'],
                            ];

                            foreach ($reportes_recientes as $reporte):
                                ?>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <div class="font-semibold"><?php echo $reporte['tipo']; ?></div>
                                        <div class="text-sm text-gray-600">Generado: <?php echo $reporte['fecha']; ?> |
                                            Formato: <?php echo $reporte['formato']; ?></div>
                                    </div>
                                    <button class="text-verde-hoja hover:underline text-sm">Regenerar</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <script>
                    function resetearFormulario() {
                        document.getElementById('form-generar-reporte').reset();
                        // Restablecer fechas por defecto
                        const hoy = new Date().toISOString().split('T')[0];
                        const primerDiaMes = new Date(new Date().getFullYear(), new Date().getMonth(), 2).toISOString().split('T')[0];

                        document.querySelector('input[name="fecha_inicio"]').value = primerDiaMes;
                        document.querySelector('input[name="fecha_fin"]').value = hoy;
                    }

                    function generarReporteRapido(tipo) {
                        const form = document.getElementById('form-generar-reporte');
                        form.tipo_reporte.value = tipo;
                        form.formato.value = 'html'; // Vista previa por defecto para rápidos

                        // Establecer rango de fechas común para reportes rápidos
                        const hoy = new Date().toISOString().split('T')[0];
                        const hace30Dias = new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

                        form.fecha_inicio.value = hace30Dias;
                        form.fecha_fin.value = hoy;

                        form.submit();
                    }

                    // Validación de fechas
                    document.getElementById('form-generar-reporte').addEventListener('submit', function (e) {
                        const fechaInicio = new Date(this.fecha_inicio.value);
                        const fechaFin = new Date(this.fecha_fin.value);

                        if (fechaInicio > fechaFin) {
                            e.preventDefault();
                            alert('La fecha de inicio no puede ser mayor a la fecha fin');
                            return false;
                        }

                        // Limitar a máximo 2 años de datos para evitar consultas muy pesadas
                        const diffTime = Math.abs(fechaFin - fechaInicio);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                        if (diffDays > 730) { // 2 años
                            e.preventDefault();
                            alert('El rango de fechas no puede ser mayor a 2 años');
                            return false;
                        }
                    });
                </script>

                <!-- Configuraciones -->
                <div id="configuraciones" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Configuraciones del Sistema</h2>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold mb-4">Ajustes Generales</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Comisión por Pedido
                                    (%)</label>
                                <input type="number" value="10"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tiempo Máximo de Entrega
                                    (minutos)</label>
                                <input type="number" value="120"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Radio de Cobertura
                                    (km)</label>
                                <input type="number" value="15"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">Modo Mantenimiento</div>
                                    <div class="text-sm text-gray-600">Desactivar el sistema temporalmente</div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-verde-hoja peer-focus:ring-opacity-30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-verde-hoja">
                                    </div>
                                </label>
                            </div>
                            <button
                                class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Guardar
                                Configuración</button>
                        </div>
                    </div>
                </div>

                <!-- Moderación -->
                <div id="moderacion" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Moderación</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Reportes Pendientes</h3>
                            <div class="space-y-4">
                                <div class="p-4 border rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="font-semibold">Reporte #RP-001</div>
                                        <span
                                            class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">Urgente</span>
                                    </div>
                                    <p class="text-gray-600 mb-2">Usuario reporta problema con entrega de pedido #FE-045
                                    </p>
                                    <div class="text-sm text-gray-500 mb-2">Reportado por: María García</div>
                                    <div class="flex space-x-2">
                                        <button
                                            class="bg-verde-hoja text-white px-3 py-1 rounded text-sm">Revisar</button>
                                        <button
                                            class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm">Descartar</button>
                                    </div>
                                </div>
                                <div class="p-4 border rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="font-semibold">Reporte #RP-002</div>
                                        <span
                                            class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm">Medio</span>
                                    </div>
                                    <p class="text-gray-600 mb-2">Florería reporta problema con pago</p>
                                    <div class="text-sm text-gray-500 mb-2">Reportado por: Florería Bella Rosa</div>
                                    <div class="flex space-x-2">
                                        <button
                                            class="bg-verde-hoja text-white px-3 py-1 rounded text-sm">Revisar</button>
                                        <button
                                            class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm">Descartar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Actividad Reciente</h3>
                            <div class="space-y-3">
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white text-sm">
                                        ✓</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Usuario verificado</div>
                                        <div class="text-sm text-gray-600">Juan Pérez - #USR-001</div>
                                    </div>
                                    <span class="text-xs text-gray-500">10:30</span>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center text-white text-sm">
                                        !</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Reporte creado</div>
                                        <div class="text-sm text-gray-600">Problema con entrega</div>
                                    </div>
                                    <span class="text-xs text-gray-500">09:15</span>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                    <div
                                        class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center text-white text-sm">
                                        ✗</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Cuenta suspendida</div>
                                        <div class="text-sm text-gray-600">Florería Rosas Elegantes</div>
                                    </div>
                                    <span class="text-xs text-gray-500">Ayer</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat con Florerías -->
                <div id="chat" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Chat con Florerías</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Conversaciones</h3>
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg cursor-pointer">
                                    <div
                                        class="w-10 h-10 bg-verde-hoja rounded-full flex items-center justify-center text-white font-bold">
                                        BR</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Florería Bella Rosa</div>
                                        <div class="text-sm text-gray-600">Tengo un problema con un pedido...</div>
                                    </div>
                                    <span class="text-xs text-gray-500">10:30</span>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg cursor-pointer">
                                    <div
                                        class="w-10 h-10 bg-magenta-flor rounded-full flex items-center justify-center text-white font-bold">
                                        FC</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Flores del Campo</div>
                                        <div class="text-sm text-gray-600">¿Pueden ayudarme con...</div>
                                    </div>
                                    <span class="text-xs text-gray-500">09:15</span>
                                </div>
                                <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg cursor-pointer">
                                    <div
                                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                                        JS</div>
                                    <div class="flex-1">
                                        <div class="font-semibold">Jardín Secreto</div>
                                        <div class="text-sm text-gray-600">Necesito ayuda con mi cuenta...</div>
                                    </div>
                                    <span class="text-xs text-gray-500">Ayer</span>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Conversación con Florería Bella Rosa</h3>
                            <div class="h-96 overflow-y-auto mb-4 space-y-4">
                                <div class="flex justify-start">
                                    <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                        <p>Hola, tengo un problema con un pedido.</p>
                                        <span class="text-xs text-gray-500">10:30 AM</span>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="bg-verde-hoja text-white rounded-lg p-3 max-w-xs">
                                        <p>¡Hola! ¿En qué podemos ayudarte?</p>
                                        <span class="text-xs text-white text-opacity-80">10:32 AM</span>
                                    </div>
                                </div>
                                <div class="flex justify-start">
                                    <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                                        <p>El cliente dice que no recibió el pedido, pero nuestro repartidor confirma la
                                            entrega.</p>
                                        <span class="text-xs text-gray-500">10:33 AM</span>
                                    </div>
                                </div>
                                <div class="flex justify-end">
                                    <div class="bg-verde-hoja text-white rounded-lg p-3 max-w-xs">
                                        <p>Vamos a revisar el caso. ¿Tienes algún comprobante de entrega?</p>
                                        <span class="text-xs text-white text-opacity-80">10:35 AM</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <input type="text" placeholder="Escribe tu mensaje..."
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                <button
                                    class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Crear Usuario -->
    <div id="create-user-modal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Crear Usuario</h3>
                <button onclick="hideCreateUserModal()" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tipo de Usuario</label>
                    <select
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                        <option>Cliente</option>
                        <option>Florería</option>
                        <option>Administrador</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre Completo</label>
                    <input type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                    <input type="tel"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña Temporal</label>
                    <input type="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                        required>
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="hideCreateUserModal()"
                        class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 smooth-transition">Cancelar</button>
                    <button type="submit"
                        class="flex-1 bg-verde-hoja text-white py-2 rounded-lg hover:bg-green-600 smooth-transition">Crear
                        Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Crear Producto -->
    <div id="create-product-modal"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Crear Producto</h3>
                <button onclick="hideCreateProductModal()" class="text-gray-500 hover:text-gray-700">✕</button>
            </div>

            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Nombre del Producto</label>
                        <input type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Categoría</label>
                        <select
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                            <option>Ramos</option>
                            <option>Funerarios</option>
                            <option>Eventos</option>
                            <option>Regalos</option>
                            <option>Plantas</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                    <textarea rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                        required></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Precio</label>
                        <input type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Stock Inicial</label>
                        <input type="number"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja"
                            required>
                    </div>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Imagen del Producto</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <div class="text-4xl mb-2">📷</div>
                        <p class="text-gray-600">Arrastra una imagen o haz clic para seleccionar</p>
                        <input type="file" class="hidden" id="product-image">
                        <button type="button" onclick="document.getElementById('product-image').click()"
                            class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition mt-2">Seleccionar
                            Imagen</button>
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" id="product-available" class="mr-2" checked>
                    <label for="product-available" class="text-gray-700">Producto disponible para venta</label>
                </div>
                <div class="flex space-x-4">
                    <button type="button" onclick="hideCreateProductModal()"
                        class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 smooth-transition">Cancelar</button>
                    <button type="submit"
                        class="flex-1 bg-verde-hoja text-white py-2 rounded-lg hover:bg-green-600 smooth-transition">Crear
                        Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Florería -->
    <div id="modalAgregarFloreria"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Agregar Florería</h3>
            <button onclick="document.getElementById('modalAgregarFloreria').classList.add('hidden')"
                class="absolute top-3 right-4 text-gray-500">✕</button>

            <form action="actions/gestionar_florerias.php" method="POST" class="space-y-4">
                <input type="hidden" name="accion" value="agregar">
                <div>
                    <label class="block text-sm font-semibold">Nombre</label>
                    <input type="text" name="nombre_floreria" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Dirección</label>
                    <input type="text" name="direccion" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Teléfono</label>
                    <input type="text" name="telefono" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Correo</label>
                    <input type="email" name="correo" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Estado</label>
                    <select name="estado" class="w-full border rounded px-3 py-2">
                        <option value="activa">Activa</option>
                        <option value="inactiva">Inactiva</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('modalAgregarFloreria').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Agregar Usuario -->
    <div id="modalAgregarUsuario"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Agregar Usuario</h3>
            <button onclick="document.getElementById('modalAgregarUsuario').classList.add('hidden')"
                class="absolute top-3 right-4 text-gray-500">✕</button>

            <form action="actions/gestionar_usuarios.php" method="POST" class="space-y-4">
                <input type="hidden" name="accion" value="agregar">
                <div>
                    <label class="block text-sm font-semibold">Nombre</label>
                    <input type="text" name="nombre" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Correo</label>
                    <input type="email" name="correo" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Teléfono</label>
                    <input type="text" name="telefono" required class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Rol</label>
                    <select name="rol" class="w-full border rounded px-3 py-2">
                        <option value="cliente">Cliente</option>
                        <option value="floreria">Florería</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold">Contraseña</label>
                    <input type="password" name="password" required class="w-full border rounded px-3 py-2">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('modalAgregarUsuario').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Usuario -->
    <div id="modalEditarUsuario"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Editar Usuario</h3>
            <button onclick="document.getElementById('modalEditarUsuario').classList.add('hidden')"
                class="absolute top-3 right-4 text-gray-500">✕</button>

            <form action="actions/gestionar_usuarios.php" method="POST" class="space-y-4">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" id="edit-id-usuario" name="id_usuario">

                <div>
                    <label class="block text-sm font-semibold">Nombre</label>
                    <input type="text" id="edit-nombre-usuario" name="nombre" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Correo</label>
                    <input type="email" id="edit-correo-usuario" name="correo" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Teléfono</label>
                    <input type="text" id="edit-telefono-usuario" name="telefono" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Rol</label>
                    <select id="edit-rol-usuario" name="rol" class="w-full border rounded px-3 py-2">
                        <option value="cliente">Cliente</option>
                        <option value="floreria">Florería</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('modalEditarUsuario').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editarUsuario(id, nombre, correo, telefono, rol) {
            document.getElementById('edit-id-usuario').value = id;
            document.getElementById('edit-nombre-usuario').value = nombre;
            document.getElementById('edit-correo-usuario').value = correo;
            document.getElementById('edit-telefono-usuario').value = telefono;
            document.getElementById('edit-rol-usuario').value = rol;
            document.getElementById('modalEditarUsuario').classList.remove('hidden');
        }
    </script>

    <!-- Modal Editar Florería -->
    <div id="modalEditarFloreria"
        class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Editar Florería</h3>
            <button onclick="document.getElementById('modalEditarFloreria').classList.add('hidden')"
                class="absolute top-3 right-4 text-gray-500">✕</button>

            <form action="actions/gestionar_florerias.php" method="POST" class="space-y-4">
                <input type="hidden" name="accion" value="editar">
                <input type="hidden" id="edit-id-floreria" name="id_floreria">

                <div>
                    <label class="block text-sm font-semibold">Nombre</label>
                    <input type="text" id="edit-nombre-floreria" name="nombre_floreria" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Dirección</label>
                    <input type="text" id="edit-direccion-floreria" name="direccion" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Teléfono</label>
                    <input type="text" id="edit-telefono-floreria" name="telefono" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Correo</label>
                    <input type="email" id="edit-correo-floreria" name="correo" required
                        class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold">Estado</label>
                    <select id="edit-estado-floreria" name="estado" class="w-full border rounded px-3 py-2">
                        <option value="activa">Activa</option>
                        <option value="inactiva">Inactiva</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button"
                        onclick="document.getElementById('modalEditarFloreria').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 rounded-lg">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-verde-hoja text-white rounded-lg hover:bg-green-600">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editarFloreria(id, nombre, direccion, telefono, correo, estado) {
            document.getElementById('edit-id-floreria').value = id;
            document.getElementById('edit-nombre-floreria').value = nombre;
            document.getElementById('edit-direccion-floreria').value = direccion;
            document.getElementById('edit-telefono-floreria').value = telefono;
            document.getElementById('edit-correo-floreria').value = correo;
            document.getElementById('edit-estado-floreria').value = estado;
            document.getElementById('modalEditarFloreria').classList.remove('hidden');
        }
    </script>

    <!-- Modal para ver detalles del pedido -->
    <div id="modalDetallePedido"
        class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-screen overflow-y-auto relative">
            <h3 class="text-2xl font-bold mb-4 text-gray-800">Detalles del Pedido</h3>
            <button onclick="document.getElementById('modalDetallePedido').classList.add('hidden')"
                class="absolute top-3 right-4 text-gray-500 hover:text-gray-700 text-2xl">✕</button>

            <div id="contenidoDetallePedido" class="space-y-4">
                <!-- El contenido se llenará dinámicamente con JavaScript -->
            </div>

            <div class="flex justify-end mt-6 pt-4 border-t">
                <button onclick="document.getElementById('modalDetallePedido').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cerrar</button>
            </div>
        </div>
    </div>

    <!-- Scripts específicos de funcionalidad movidos al final pero después de las funciones principales -->
    <script>
        function verDetallePedido(pedido) {
            const contenido = document.getElementById('contenidoDetallePedido');

            // Determinar color del estado
            let estadoClass = '';
            let estadoTexto = pedido.estado.replace('_', ' ').charAt(0).toUpperCase() + pedido.estado.replace('_', ' ').slice(1);
            switch (pedido.estado) {
                case 'confirmado':
                case 'pendiente':
                    estadoClass = 'bg-blue-100 text-blue-800';
                    estadoTexto = 'Confirmado';
                    break;
                case 'preparando':
                case 'en_proceso':
                    estadoClass = 'bg-yellow-100 text-yellow-800';
                    estadoTexto = 'Preparando';
                    break;
                case 'enviado':
                    estadoClass = 'bg-purple-100 text-purple-800';
                    estadoTexto = 'Enviado';
                    break;
                case 'entregado':
                case 'completado':
                    estadoClass = 'bg-green-100 text-green-800';
                    estadoTexto = 'Entregado';
                    break;
                case 'cancelado':
                    estadoClass = 'bg-red-100 text-red-800';
                    estadoTexto = 'Cancelado';
                    break;
                default:
                    estadoClass = 'bg-gray-100 text-gray-800';
            }

            contenido.innerHTML = `
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Información del Pedido</h4>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID:</span>
                                <span class="font-semibold">#${pedido.id_pedido}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Estado:</span>
                                <span class="${estadoClass} px-2 py-1 rounded-full text-sm">${estadoTexto}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fecha:</span>
                                <span class="font-semibold">${new Date(pedido.fecha_pedido).toLocaleDateString('es-MX')}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total:</span>
                                <span class="font-semibold text-verde-hoja">$${parseFloat(pedido.total).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-semibold text-gray-700 mb-2">Florería Asignada</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-semibold">${pedido.nombre_floreria || 'Sin asignar'}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Información del Cliente</h4>
                    <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                        <div>
                            <span class="text-gray-600">Correo:</span>
                            <p class="font-semibold">${pedido.correo_cliente || 'N/A'}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Dirección de Entrega:</span>
                            <p class="font-semibold">${pedido.direccion_entrega || 'N/A'}</p>
                        </div>
                        ${pedido.telefono_contacto ? `
                        <div>
                            <span class="text-gray-600">Teléfono:</span>
                            <p class="font-semibold">${pedido.telefono_contacto}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>
                
                ${pedido.notas_especiales ? `
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Notas Especiales</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p>${pedido.notas_especiales}</p>
                    </div>
                </div>
                ` : ''}
                
                ${pedido.dedicatoria ? `
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Dedicatoria</h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="italic">"${pedido.dedicatoria}"</p>
                    </div>
                </div>
                ` : ''}
            `;

            document.getElementById('modalDetallePedido').classList.remove('hidden');
        }

        function filtrarPedidos(estado) {
            const filas = document.querySelectorAll('#tablaPedidos tr[data-estado]');
            const botones = document.querySelectorAll('.filtro-pedido');

            // Actualizar estilos de botones
            botones.forEach(btn => {
                if (btn.getAttribute('data-estado') === estado) {
                    btn.classList.remove('bg-gray-200', 'text-gray-700');
                    btn.classList.add('bg-verde-hoja', 'text-white');
                } else {
                    btn.classList.remove('bg-verde-hoja', 'text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                }
            });

            // Filtrar filas
            filas.forEach(fila => {
                const estadoFila = fila.getAttribute('data-estado');
                if (estado === 'todos' || estadoFila === estado) {
                    fila.style.display = '';
                } else {
                    fila.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>