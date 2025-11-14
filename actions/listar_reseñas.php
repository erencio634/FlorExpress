<?php
session_start();
require_once "../conexion.php";

// Verificar sesión activa
if (!isset($_SESSION['id_usuario'])) {
    echo '<div class="text-gray-500 text-sm">Sesión no activa.</div>';
    exit;
}

// Obtener ID del cliente a partir del usuario logueado
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

// Consultar reseñas del cliente
$sql = "
SELECT 
  r.id_resena,
  r.calificacion,
  r.comentario,
  r.fecha,
  c.nombre_articulo
FROM resenas r
JOIN catalogo c ON r.id_articulo = c.id_articulo
WHERE r.id_cliente = ?
ORDER BY r.fecha DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

// Si no hay reseñas
if ($res->num_rows === 0) {
    echo '<div class="text-gray-500 text-sm">Aún no has dejado ninguna reseña.</div>';
    exit;
}

// Mostrar reseñas
while ($row = $res->fetch_assoc()) {
    $estrellas = str_repeat('⭐', (int)$row['calificacion']);
    echo '
    <div class="bg-white rounded-lg shadow-md p-4 mb-4 border border-gray-100">
        <div class="flex items-center space-x-3 mb-2">
            <div>
                <div class="font-semibold text-verde-hoja">' . htmlspecialchars($row['nombre_articulo']) . '</div>
                <div class="text-yellow-500 text-sm">' . $estrellas . '</div>
            </div>
        </div>
        <p class="text-gray-700 text-sm mb-2">' . htmlspecialchars($row['comentario']) . '</p>
        <span class="text-xs text-gray-400">' . htmlspecialchars($row['fecha']) . '</span>
    </div>';
}
?>
