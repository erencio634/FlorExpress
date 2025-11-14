<?php
session_start();
include('../conexion.php');

if (!isset($_SESSION['id_usuario'])) {
    echo "<p class='text-gray-500'>Sesión no válida.</p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$res = $stmt->get_result();
$cliente = $res->fetch_assoc();
$id_cliente = $cliente['id_cliente'] ?? 0;

if (!$id_cliente) {
    echo "<p class='text-gray-500'>Cliente no encontrado.</p>";
    exit;
}

$sql = "SELECT * FROM metodos_pago_cliente WHERE id_cliente = ? ORDER BY es_principal DESC, fecha_agregado DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p class='text-gray-500'>No tienes métodos de pago guardados.</p>";
    exit;
}

while ($pago = $res->fetch_assoc()):
?>
<div class="bg-white p-4 rounded-lg shadow flex justify-between items-start border">
  <div>
    <div class="flex items-center space-x-2 mb-2">
      <span class="bg-gray-200 px-2 py-1 rounded text-xs uppercase">
        <?php echo htmlspecialchars($pago['tipo']); ?>
      </span>
      <span class="font-semibold"><?php echo htmlspecialchars($pago['alias']); ?></span>
      <?php if ($pago['es_principal']): ?>
        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Principal</span>
      <?php endif; ?>
    </div>
    <?php if ($pago['tipo'] === 'tarjeta'): ?>
      <div class="text-gray-600 text-sm">•••• <?php echo htmlspecialchars($pago['ultimos4']); ?>
        <span class="text-gray-400 ml-1">(<?php echo htmlspecialchars($pago['expiracion']); ?>)</span>
      </div>
    <?php endif; ?>
    <div class="text-sm text-gray-500">Titular: <?php echo htmlspecialchars($pago['titular']); ?></div>
  </div>

  <div class="text-sm space-y-2 text-right">
    <button class="text-verde-hoja hover:underline btn-editar-pago" data-id="<?php echo $pago['id_metodo']; ?>">Editar</button>
    <button class="text-red-500 hover:underline btn-eliminar-pago" data-id="<?php echo $pago['id_metodo']; ?>">Eliminar</button>
  </div>
</div>
<?php endwhile; ?>
