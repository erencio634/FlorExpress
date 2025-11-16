<?php

function obtenerFinanzas($conn)
{
    // Obtener comisión actual
    $configQ = $conn->query("SELECT comision FROM configuraciones ORDER BY id DESC LIMIT 1");
    $comision_porcentaje = $configQ->fetch_assoc()['comision'] ?? 10;

    // Mes actual
    $mes_actual = date('Y-m');

    // Ingresos Totales del mes
    $ingresosQ = $conn->query("
        SELECT SUM(total) AS total 
        FROM pedidos 
        WHERE estado IN ('entregado','completado') 
        AND DATE_FORMAT(fecha_pedido, '%Y-%m') = '$mes_actual'
    ");

    $ingresos_mes = $ingresosQ->fetch_assoc()['total'] ?? 0;

    // Comisión Flor Express
    $comisiones_mes = $ingresos_mes * ($comision_porcentaje / 100);

    // Gastos operativos fijos
    $gastos_operativos = 850000;

    // Utilidad Neta
    $utilidad_neta = $ingresos_mes - $comisiones_mes - $gastos_operativos;

    // Últimas transacciones
    $transaccionesQ = $conn->query("
        SELECT p.id_pedido, p.total, f.nombre_floreria, p.fecha_pedido
        FROM pedidos p
        INNER JOIN florerias f ON p.id_floreria = f.id_floreria
        WHERE p.estado IN ('entregado','completado')
        ORDER BY p.fecha_pedido DESC
        LIMIT 10
    ");

    return [
        'comision' => $comision_porcentaje,
        'ingresos_mes' => $ingresos_mes,
        'comisiones_mes' => $comisiones_mes,
        'gastos_operativos' => $gastos_operativos,
        'utilidad_neta' => $utilidad_neta,
        'transacciones' => $transaccionesQ
    ];
}
