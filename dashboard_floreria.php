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
                <div class="text-sm opacity-75 mb-8">Florería Bella Rosa</div>
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
                <button onclick="logout()" class="mt-8 bg-magenta-flor text-white px-4 py-2 rounded-lg hover:bg-pink-600 smooth-transition w-full">Cerrar Sesión</button>
            </div>
        </div>
        
        <!-- Main Content Florería -->
        <div class="flex-1 p-8 overflow-y-auto">
            <div id="floreria-content">
                <!-- Dashboard Florería -->
                <div id="floreria-dashboard">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-2">Dashboard</h2>
                        <p class="text-gray-600">Bienvenido de vuelta, Florería Bella Rosa</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-verde-hoja text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">📦</div>
                            <div class="text-2xl font-bold">156</div>
                            <div class="text-sm opacity-90">Pedidos Completados</div>
                        </div>
                        <div class="bg-magenta-flor text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">💰</div>
                            <div class="text-2xl font-bold">$2.1M</div>
                            <div class="text-sm opacity-90">Ingresos del Mes</div>
                        </div>
                        <div class="bg-blue-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">⭐</div>
                            <div class="text-2xl font-bold">4.8</div>
                            <div class="text-sm opacity-90">Calificación</div>
                        </div>
                        <div class="bg-purple-500 text-white p-6 rounded-lg">
                            <div class="text-3xl mb-2">⏱️</div>
                            <div class="text-2xl font-bold">45 min</div>
                            <div class="text-sm opacity-90">Tiempo Promedio</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Pedidos Pendientes</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                    <div>
                                        <div class="font-semibold">Ramo de Rosas Blancas</div>
                                        <div class="text-sm text-gray-600">Cliente: María García</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-magenta-flor">$45.000</div>
                                        <button class="bg-verde-hoja text-white px-3 py-1 rounded text-sm mt-1">Aceptar</button>
                                    </div>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                                    <div>
                                        <div class="font-semibold">Corona Fúnebre</div>
                                        <div class="text-sm text-gray-600">Cliente: Carlos López</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold text-magenta-flor">$85.000</div>
                                        <button class="bg-verde-hoja text-white px-3 py-1 rounded text-sm mt-1">Aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Estadísticas del Día</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Pedidos Completados:</span>
                                    <span class="font-bold text-verde-hoja">8</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Ingresos del Día:</span>
                                    <span class="font-bold text-magenta-flor">$340.000</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Calificación Promedio:</span>
                                    <span class="font-bold text-yellow-500">4.8 ⭐</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Tiempo Promedio:</span>
                                    <span class="font-bold text-blue-500">45 min</span>
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
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-magenta-flor">#FE-2024-001</h3>
                                    <p class="text-gray-600">Cliente: Ana Rodríguez</p>
                                    <p class="text-gray-600">Entrega: Calle 123 #45-67, Bogotá</p>
                                    <p class="text-gray-600">Distancia: 2.3 km</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-verde-hoja">$45.000</div>
                                    <div class="text-sm text-gray-500">Hace 15 min</div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Productos:</h4>
                                <ul class="list-disc list-inside text-gray-600">
                                    <li>Ramo de Rosas Rojas (12 unidades)</li>
                                    <li>Tarjeta personalizada</li>
                                </ul>
                            </div>
                            <div class="flex space-x-4">
                                <button class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Aceptar Pedido</button>
                                <button class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 smooth-transition">Ver Detalles</button>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-magenta-flor">#FE-2024-002</h3>
                                    <p class="text-gray-600">Cliente: Carlos Mendoza</p>
                                    <p class="text-gray-600">Entrega: Carrera 15 #93-47, Bogotá</p>
                                    <p class="text-gray-600">Distancia: 3.1 km</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-verde-hoja">$85.000</div>
                                    <div class="text-sm text-gray-500">Hace 30 min</div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h4 class="font-semibold mb-2">Productos:</h4>
                                <ul class="list-disc list-inside text-gray-600">
                                    <li>Corona Fúnebre Premium</li>
                                    <li>Cinta personalizada</li>
                                </ul>
                            </div>
                            <div class="flex space-x-4">
                                <button class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Aceptar Pedido</button>
                                <button class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 smooth-transition">Ver Detalles</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mis Pedidos -->
                <div id="mis-pedidos" class="hidden">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold mb-4">Mis Pedidos</h2>
                        <div class="flex space-x-4 mb-6">
                            <button class="bg-verde-hoja text-white px-4 py-2 rounded-lg">Todos</button>
                            <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Pendientes</button>
                            <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">En Proceso</button>
                            <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">Completados</button>
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
                                <tr>
                                    <td class="px-6 py-4 font-medium">#FE-001</td>
                                    <td class="px-6 py-4">Ana Rodríguez</td>
                                    <td class="px-6 py-4">Ramo de Rosas Rojas</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-sm">Entregado</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold">$45.000</td>
                                    <td class="px-6 py-4">2024-01-15</td>
                                    <td class="px-6 py-4">
                                        <button class="text-verde-hoja hover:underline mr-2">Ver</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 font-medium">#FE-002</td>
                                    <td class="px-6 py-4">Carlos Mendoza</td>
                                    <td class="px-6 py-4">Corona Fúnebre</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-sm">En Proceso</span>
                                    </td>
                                    <td class="px-6 py-4 font-semibold">$85.000</td>
                                    <td class="px-6 py-4">2024-01-16</td>
                                    <td class="px-6 py-4">
                                        <button class="text-verde-hoja hover:underline mr-2">Ver</button>
                                        <button class="text-magenta-flor hover:underline">Actualizar</button>
                                    </td>
                                </tr>
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
                                <form class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Florería</label>
                                            <input type="text" value="Florería Bella Rosa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Teléfono</label>
                                            <input type="tel" value="+57 300 123 4567" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Dirección</label>
                                        <input type="text" value="Calle 123 #45-67, Bogotá" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-bold mb-2">Descripción</label>
                                        <textarea rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">Especialistas en arreglos florales para toda ocasión. Más de 10 años de experiencia.</textarea>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Horario de Apertura</label>
                                            <input type="time" value="08:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                        <div>
                                            <label class="block text-gray-700 text-sm font-bold mb-2">Horario de Cierre</label>
                                            <input type="time" value="20:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="disponible" class="mr-2" checked>
                                        <label for="disponible" class="text-gray-700">Disponible para recibir pedidos</label>
                                    </div>
                                    <button type="submit" class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Actualizar Información</button>
                                </form>
                            </div>
                        </div>
                        
                        <div>
                            <div class="bg-white rounded-lg shadow-lg p-6">
                                <h3 class="text-xl font-bold mb-4">Logo de la Florería</h3>
                                <div class="text-center">
                                    <div class="w-32 h-32 bg-gradient-to-br from-verde-hoja to-green-600 rounded-lg mx-auto mb-4 flex items-center justify-center text-white text-4xl font-bold">
                                        BR
                                    </div>
                                    <button class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition text-sm">Cambiar Logo</button>
                                </div>
                            </div>
                            
                            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                                <h3 class="text-xl font-bold mb-4">Estadísticas Rápidas</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Pedidos este mes:</span>
                                        <span class="font-semibold text-verde-hoja">156</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ingresos del mes:</span>
                                        <span class="font-semibold text-magenta-flor">$2.1M</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Calificación promedio:</span>
                                        <span class="font-semibold text-yellow-500">4.8 ⭐</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tiempo promedio de entrega:</span>
                                        <span class="font-semibold text-blue-500">45 min</span>
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
                            <h3 class="text-xl font-bold mb-4">Ventas del Mes</h3>
                            <div class="h-64 flex items-end justify-between">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 120px;"></div>
                                    <span class="text-sm">Lun</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 80px;"></div>
                                    <span class="text-sm">Mar</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 150px;"></div>
                                    <span class="text-sm">Mié</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 100px;"></div>
                                    <span class="text-sm">Jue</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 180px;"></div>
                                    <span class="text-sm">Vie</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 200px;"></div>
                                    <span class="text-sm">Sáb</span>
                                </div>
                                <div class="flex flex-col items-center">
                                    <div class="w-8 bg-verde-hoja rounded-t-lg mb-2" style="height: 160px;"></div>
                                    <span class="text-sm">Dom</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Productos Más Vendidos</h3>
                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span>Ramo de Rosas Rojas</span>
                                        <span>45 ventas</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-verde-hoja h-2 rounded-full" style="width: 90%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span>Corona Fúnebre</span>
                                        <span>32 ventas</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-verde-hoja h-2 rounded-full" style="width: 64%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between mb-1">
                                        <span>Ramo de Girasoles</span>
                                        <span>28 ventas</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-verde-hoja h-2 rounded-full" style="width: 56%"></div>
                                    </div>
                                </div>
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
                                        <div class="text-sm text-gray-600">Hola, ¿cómo estás?</div>
                                    </div>
                                    <span class="text-xs text-gray-500">10:30</span>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
                            <h3 class="text-xl font-bold mb-4">Conversación con Administrador</h3>
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
                            </div>
                            <div class="flex space-x-4">
                                <input type="text" placeholder="Escribe tu mensaje..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-verde-hoja">
                                <button class="bg-verde-hoja text-white px-6 py-2 rounded-lg hover:bg-green-600 smooth-transition">Enviar</button>
                            </div>
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
            event.target.classList.add('bg-white', 'bg-opacity-20');
        }

        function logout() {
            window.location.href = 'index.html';
        }

        // Initialize dashboard with dashboard visible
        document.addEventListener('DOMContentLoaded', function() {
            showFloreriaSection('floreria-dashboard');
        });
    </script>
</body>
</html>