<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
  echo "<p class='text-gray-500'>No hay sesión activa.</p>";
  exit;
}

$id_usuario = $_SESSION['id_usuario'];

$sql = "SELECT id_cliente FROM clientes WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();
$id_cliente = $cliente['id_cliente'] ?? 0;

if (!$id_cliente) {
  echo "<p class='text-gray-500'>No se encontró el cliente.</p>";
  exit;
}

$sqlDir = "SELECT * FROM direcciones WHERE id_cliente = ?";
$stmtDir = $conn->prepare($sqlDir);
$stmtDir->bind_param("i", $id_cliente);
$stmtDir->execute();
$resDir = $stmtDir->get_result();

if ($resDir->num_rows === 0) {
  echo "<p class='text-gray-500'>No tienes direcciones guardadas.</p>";
  exit;
}

while ($dir = $resDir->fetch_assoc()):
?>
<div class="bg-white rounded-lg shadow p-4 flex justify-between items-start">
  <div>
    <div class="font-semibold text-lg"><?php echo htmlspecialchars($dir['calle']); ?></div>
    <div class="text-gray-600">
      <?php echo htmlspecialchars("{$dir['colonia']}, {$dir['municipio']}, {$dir['estado']}"); ?>
    </div>
    <div class="text-sm text-gray-500">
      CP <?php echo htmlspecialchars($dir['codigo_postal']); ?>
    </div>
    <div class="mt-2 text-sm text-gray-500">
      <strong>Receptor:</strong> 
      <?php echo htmlspecialchars($dir['nombre_receptor'].' '.$dir['apellidos_receptor']); ?>,
      <?php echo htmlspecialchars($dir['telefono_receptor']); ?>
    </div>
  </div>
  <div class="space-y-2 text-sm">
    <button class="text-verde-hoja hover:underline btn-editar-direccion" data-id="<?php echo $dir['id_direccion']; ?>">Editar</button>
    <button class="text-red-500 hover:underline btn-eliminar-direccion" data-id="<?php echo $dir['id_direccion']; ?>">Eliminar</button>
  </div>
</div>
<?php endwhile; ?>
