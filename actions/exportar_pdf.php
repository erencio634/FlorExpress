<?php
// Exportación básica a PDF - necesitarías instalar una librería como TCPDF para una implementación completa
header("Content-type: application/pdf");
header("Content-Disposition: attachment; filename=reporte_" . date('Y-m-d') . ".pdf");

// Por ahora redirigimos a una implementación HTML mejorada
$datos = json_decode($_GET['datos'] ?? '{}', true);
$titulo = $_GET['titulo'] ?? 'Reporte';
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';

echo "<html>
<head>
    <title>{$titulo}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #C3D600; padding-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #C3D600; color: white; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='header'>
        <h1>{$titulo}</h1>
        <p>Período: {$fecha_inicio} al {$fecha_fin}</p>
        <p>Generado el: " . date('d/m/Y H:i:s') . "</p>
    </div>";

if (isset($datos['columnas']) && isset($datos['datos'])) {
    echo "<table>
        <thead>
            <tr>";
    foreach ($datos['columnas'] as $columna) {
        echo "<th>{$columna}</th>";
    }
    echo "</tr>
        </thead>
        <tbody>";
    
    foreach ($datos['datos'] as $fila) {
        echo "<tr>";
        foreach ($fila as $valor) {
            echo "<td>{$valor}</td>";
        }
        echo "</tr>";
    }
    echo "</tbody>
    </table>";
}

echo "<div class='footer'>
        <p>Reporte generado por Flor Express - Sistema Administrativo</p>
    </div>
</body>
</html>";
?>