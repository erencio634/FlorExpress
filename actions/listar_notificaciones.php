<?php
session_start();
include('../conexion.php');

$id_usuario = $_SESSION['id_usuario'] ?? 0;

if (!$id_usuario) {
    echo '<div class="text-gray-500 text-sm">No hay notificaciones disponibles.</div>';
    exit;
}

$sql = "SELECT tipo, titulo, mensaje, leido, fecha 
        FROM notificaciones 
        WHERE id_usuario = ? 
        ORDER BY fecha DESC 
        LIMIT 20";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo '<div class="text-gray-500 text-sm">Aún no tienes notificaciones.</div>';
} else {
    while ($row = $res->fetch_assoc()) {
        $icono = match ($row['tipo']) {
            'pedido'  => '📦',
            'mensaje' => '💬',
            'sistema' => '⚙️',
            'pago'    => '💳',
            default   => '🔔',
        };

        $bg = $row['leido'] ? 'bg-gray-50' : 'bg-white border-l-4 border-verde-hoja';

        echo "
        <div class='$bg rounded-lg shadow p-4 mb-3'>
            <div class='flex items-start space-x-3'>
                <div class='text-2xl'>$icono</div>
                <div>
                    <div class='font-semibold text-verde-hoja'>{$row['titulo']}</div>
                    <div class='text-gray-700 text-sm'>{$row['mensaje']}</div>
                    <div class='text-gray-400 text-xs mt-1'>{$row['fecha']}</div>
                </div>
            </div>
        </div>";
    }
}
