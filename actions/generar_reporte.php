<?php
session_start();
require_once("../conexion.php");

// Verificar que sea admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}

// Obtener parámetros del reporte
$tipo_reporte = $_POST['tipo_reporte'] ?? 'ventas_floreria';
$fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_POST['fecha_fin'] ?? date('Y-m-d');
$formato = $_POST['formato'] ?? 'html';

// Validar y sanitizar fechas
if (!empty($fecha_inicio) && !empty($fecha_fin)) {
    $fecha_inicio = $conn->real_escape_string($fecha_inicio);
    $fecha_fin = $conn->real_escape_string($fecha_fin);
} else {
    // Por defecto: último mes
    $fecha_inicio = date('Y-m-01');
    $fecha_fin = date('Y-m-d');
}

// Generar reporte según tipo
switch ($tipo_reporte) {
    case 'ventas_floreria':
        $reporte = generarReporteVentasFlorerias($conn, $fecha_inicio, $fecha_fin);
        $titulo = "Reporte de Ventas por Florería";
        break;
    
    case 'usuarios_activos':
        $reporte = generarReporteUsuariosActivos($conn, $fecha_inicio, $fecha_fin);
        $titulo = "Reporte de Usuarios Activos";
        break;
    
    case 'pedidos_completados':
        $reporte = generarReportePedidosCompletados($conn, $fecha_inicio, $fecha_fin);
        $titulo = "Reporte de Pedidos Completados";
        break;
    
    case 'ingresos_mensuales':
        $reporte = generarReporteIngresosMensuales($conn, $fecha_inicio, $fecha_fin);
        $titulo = "Reporte de Ingresos Mensuales";
        break;
    
    case 'productos_vendidos':
        $reporte = generarReporteProductosVendidos($conn, $fecha_inicio, $fecha_fin);
        $titulo = "Reporte de Productos Más Vendidos";
        break;
    
    case 'florerias_desempeno':
        $reporte = generarReporteFloreriasDesempeno($conn, $fecha_inicio, $fecha_fin);
        $titulo = "Reporte de Florerías con Mejor Desempeño";
        break;
    
    default:
        $reporte = ["error" => "Tipo de reporte no válido"];
        $titulo = "Error";
}

// Exportar según formato
if ($formato === 'pdf') {
    exportarPDF($reporte, $titulo, $fecha_inicio, $fecha_fin);
} elseif ($formato === 'excel') {
    exportarExcel($reporte, $titulo, $fecha_inicio, $fecha_fin);
} else {
    // HTML (preview)
    mostrarHTML($reporte, $titulo, $fecha_inicio, $fecha_fin);
}

// Funciones de generación de reportes
function generarReporteVentasFlorerias($conn, $fecha_inicio, $fecha_fin) {
    $sql = "
        SELECT 
            f.nombre_floreria,
            COUNT(p.id_pedido) as total_pedidos,
            SUM(p.total) as ingresos_totales,
            AVG(p.total) as promedio_venta,
            COUNT(DISTINCT p.id_cliente) as clientes_unicos
        FROM pedidos p
        INNER JOIN detalles_pedido dp ON p.id_pedido = dp.id_pedido
        INNER JOIN florerias f ON dp.id_floreria = f.id_floreria
        WHERE p.fecha_pedido BETWEEN ? AND ?
        AND p.estado IN ('entregado', 'completado')
        GROUP BY f.id_floreria, f.nombre_floreria
        ORDER BY ingresos_totales DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return [
        'columnas' => ['Florería', 'Total Pedidos', 'Ingresos Totales', 'Promedio Venta', 'Clientes Únicos'],
        'datos' => $data
    ];
}

function generarReporteUsuariosActivos($conn, $fecha_inicio, $fecha_fin) {
    $sql = "
        SELECT 
            rol,
            COUNT(*) as total_usuarios,
            SUM(CASE WHEN ultimo_acceso BETWEEN ? AND ? THEN 1 ELSE 0 END) as usuarios_activos,
            SUM(CASE WHEN fecha_registro BETWEEN ? AND ? THEN 1 ELSE 0 END) as nuevos_registros
        FROM usuarios_globales
        WHERE activo = 1
        GROUP BY rol
        ORDER BY total_usuarios DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $fecha_inicio, $fecha_fin, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return [
        'columnas' => ['Rol', 'Total Usuarios', 'Usuarios Activos', 'Nuevos Registros'],
        'datos' => $data
    ];
}

function generarReportePedidosCompletados($conn, $fecha_inicio, $fecha_fin) {
    $sql = "
        SELECT 
            DATE(p.fecha_pedido) as fecha,
            COUNT(*) as total_pedidos,
            SUM(p.total) as ingresos_dia,
            AVG(p.total) as promedio_dia,
            SUM(CASE WHEN p.estado = 'entregado' THEN 1 ELSE 0 END) as entregados,
            SUM(CASE WHEN p.estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados
        FROM pedidos p
        WHERE p.fecha_pedido BETWEEN ? AND ?
        GROUP BY DATE(p.fecha_pedido)
        ORDER BY fecha DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return [
        'columnas' => ['Fecha', 'Total Pedidos', 'Ingresos del Día', 'Promedio por Pedido', 'Entregados', 'Cancelados'],
        'datos' => $data
    ];
}

function generarReporteIngresosMensuales($conn, $fecha_inicio, $fecha_fin) {
    $sql = "
        SELECT 
            YEAR(p.fecha_pedido) as año,
            MONTH(p.fecha_pedido) as mes,
            COUNT(*) as total_pedidos,
            SUM(p.total) as ingresos_mes,
            AVG(p.total) as promedio_venta,
            (SELECT COUNT(*) FROM pedidos p2 
             WHERE YEAR(p2.fecha_pedido) = YEAR(p.fecha_pedido) 
             AND MONTH(p2.fecha_pedido) = MONTH(p.fecha_pedido)
             AND p2.estado = 'cancelado') as pedidos_cancelados
        FROM pedidos p
        WHERE p.fecha_pedido BETWEEN ? AND ?
        AND p.estado IN ('entregado', 'completado')
        GROUP BY YEAR(p.fecha_pedido), MONTH(p.fecha_pedido)
        ORDER BY año DESC, mes DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    while ($row = $result->fetch_assoc()) {
        $row['mes_nombre'] = $meses[$row['mes']] ?? $row['mes'];
        $data[] = $row;
    }
    
    return [
        'columnas' => ['Año', 'Mes', 'Total Pedidos', 'Ingresos del Mes', 'Promedio Venta', 'Pedidos Cancelados'],
        'datos' => $data
    ];
}

function generarReporteProductosVendidos($conn, $fecha_inicio, $fecha_fin) {
    $sql = "
        SELECT 
            c.nombre_articulo,
            c.categoria,
            COUNT(dp.id_detalle) as veces_vendido,
            SUM(dp.cantidad) as total_unidades,
            SUM(dp.subtotal) as ingresos_totales,
            AVG(dp.precio_unitario) as precio_promedio,
            f.nombre_floreria
        FROM detalles_pedido dp
        INNER JOIN catalogo c ON dp.id_articulo = c.id_articulo
        INNER JOIN pedidos p ON dp.id_pedido = p.id_pedido
        LEFT JOIN florerias f ON dp.id_floreria = f.id_floreria
        WHERE p.fecha_pedido BETWEEN ? AND ?
        AND p.estado IN ('entregado', 'completado')
        GROUP BY c.id_articulo, c.nombre_articulo, c.categoria
        ORDER BY ingresos_totales DESC
        LIMIT 20
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return [
        'columnas' => ['Producto', 'Categoría', 'Veces Vendido', 'Total Unidades', 'Ingresos Totales', 'Precio Promedio', 'Florería'],
        'datos' => $data
    ];
}

function generarReporteFloreriasDesempeno($conn, $fecha_inicio, $fecha_fin) {
    $sql = "
        SELECT 
            f.nombre_floreria,
            f.estado,
            f.municipio,
            COUNT(DISTINCT p.id_pedido) as total_pedidos,
            SUM(p.total) as ingresos_totales,
            AVG(p.total) as ticket_promedio,
            COUNT(DISTINCT p.id_cliente) as clientes_unicos,
            (SELECT AVG(calificacion) FROM resenas r 
             INNER JOIN detalles_pedido dp ON r.id_articulo = dp.id_articulo 
             WHERE dp.id_floreria = f.id_floreria) as calificacion_promedio,
            f.capacidad_diaria,
            f.pedidos_actuales
        FROM florerias f
        LEFT JOIN detalles_pedido dp ON f.id_floreria = dp.id_floreria
        LEFT JOIN pedidos p ON dp.id_pedido = p.id_pedido
        WHERE p.fecha_pedido BETWEEN ? AND ?
        OR p.fecha_pedido IS NULL
        GROUP BY f.id_floreria, f.nombre_floreria, f.estado, f.municipio
        ORDER BY ingresos_totales DESC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['calificacion_promedio'] = $row['calificacion_promedio'] ? number_format($row['calificacion_promedio'], 1) : 'N/A';
        $row['utilizacion_capacidad'] = $row['capacidad_diaria'] > 0 ? 
            round(($row['pedidos_actuales'] / $row['capacidad_diaria']) * 100, 1) . '%' : '0%';
        $data[] = $row;
    }
    
    return [
        'columnas' => ['Florería', 'Estado', 'Municipio', 'Total Pedidos', 'Ingresos Totales', 'Ticket Promedio', 'Clientes Únicos', 'Calificación', 'Utilización Capacidad'],
        'datos' => $data
    ];
}

// Funciones de exportación
function exportarPDF($reporte, $titulo, $fecha_inicio, $fecha_fin) {
    // Para PDF necesitarías una librería como TCPDF o Dompdf
    // Por ahora redireccionamos a una implementación básica
    header("Location: exportar_pdf.php?" . http_build_query([
        'titulo' => $titulo,
        'fecha_inicio' => $fecha_inicio,
        'fecha_fin' => $fecha_fin,
        'datos' => json_encode($reporte)
    ]));
    exit;
}

function exportarExcel($reporte, $titulo, $fecha_inicio, $fecha_fin) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $titulo . '_' . date('Y-m-d') . '.xls"');
    
    echo "<table border='1'>";
    echo "<tr><th colspan='" . count($reporte['columnas']) . "' style='background:#C3D600;color:white;'>{$titulo}</th></tr>";
    echo "<tr><th colspan='" . count($reporte['columnas']) . "'>Período: {$fecha_inicio} al {$fecha_fin}</th></tr>";
    echo "<tr>";
    foreach ($reporte['columnas'] as $columna) {
        echo "<th style='background:#f0f0f0;'>{$columna}</th>";
    }
    echo "</tr>";
    
    foreach ($reporte['datos'] as $fila) {
        echo "<tr>";
        foreach ($fila as $valor) {
            echo "<td>{$valor}</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    exit;
}

function mostrarHTML($reporte, $titulo, $fecha_inicio, $fecha_fin) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>{$titulo}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .reporte-header { background: #C3D600; color: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
            th { background-color: #f8f9fa; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .acciones { margin: 20px 0; }
            .btn { padding: 10px 15px; margin: 5px; border: none; border-radius: 5px; cursor: pointer; }
            .btn-excel { background: #217346; color: white; }
            .btn-pdf { background: #d32f2f; color: white; }
        </style>
    </head>
    <body>
        <div class='reporte-header'>
            <h1>{$titulo}</h1>
            <p>Período: {$fecha_inicio} al {$fecha_fin}</p>
            <p>Generado el: " . date('d/m/Y H:i:s') . "</p>
        </div>
        
        <div class='acciones'>
            <button class='btn btn-excel' onclick=\"exportar('excel')\">Descargar Excel</button>
            <button class='btn btn-pdf' onclick=\"exportar('pdf')\">Descargar PDF</button>
            <button class='btn' onclick='window.print()'>Imprimir</button>
        </div>
        
        <table>
            <thead>
                <tr>";
    foreach ($reporte['columnas'] as $columna) {
        echo "<th>{$columna}</th>";
    }
    echo "</tr>
            </thead>
            <tbody>";
    
    foreach ($reporte['datos'] as $fila) {
        echo "<tr>";
        foreach ($fila as $valor) {
            echo "<td>{$valor}</td>";
        }
        echo "</tr>";
    }
    
    echo "</tbody>
        </table>
        
        <script>
            function exportar(formato) {
                window.location.href = 'generar_reporte.php?formato=' + formato + 
                    '&tipo_reporte=" . $_POST['tipo_reporte'] . "&fecha_inicio={$fecha_inicio}&fecha_fin={$fecha_fin}';
            }
        </script>
    </body>
    </html>";
    exit;
}
?>