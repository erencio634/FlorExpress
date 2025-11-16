<?php
require_once("../conexion.php");
require_once("../vendor/autoload.inc.php");

use Dompdf\Dompdf;

if (!isset($_GET['datos'])) {
    die("Sin datos para generar PDF");
}

$titulo = $_GET['titulo'] ?? "Reporte";
$fecha_inicio = $_GET['fecha_inicio'] ?? "";
$fecha_fin = $_GET['fecha_fin'] ?? "";
$reporte = json_decode($_GET['datos'], true);

if (!isset($reporte['columnas']) || !isset($reporte['datos'])) {
    die("El reporte no tiene datos válidos.");
}

$html = "
<style>
body { font-family: Arial, sans-serif; }
h1 { text-align:center; background:#C3D600; color:white; padding:10px; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
th, td { border:1px solid #999; padding:6px; font-size:12px; }
th { background:#f2f2f2; }
tr:nth-child(even) { background:#fafafa; }
</style>

<h1>{$titulo}</h1>
<p><strong>Periodo:</strong> {$fecha_inicio} al {$fecha_fin}</p>
<p><strong>Generado:</strong> " . date('d/m/Y H:i') . "</p>

<table>
<thead>
<tr>";

foreach ($reporte['columnas'] as $col) {
    $html .= "<th>{$col}</th>";
}

$html .= "</tr></thead><tbody>";

foreach ($reporte['datos'] as $fila) {
    $html .= "<tr>";
    foreach ($fila as $val) {
        $html .= "<td>{$val}</td>";
    }
    $html .= "</tr>";
}

$html .= "</tbody></table>";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "landscape");
$dompdf->render();
$dompdf->stream("reporte_" . date("Y-m-d") . ".pdf", ["Attachment" => true]);
exit;
