<?php
session_start();
require_once("../conexion.php");

$busqueda = $_POST['busqueda'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$id_cliente = $_SESSION['id_cliente'] ?? 0;

$sql = "
SELECT c.id_articulo, c.nombre_articulo, c.descripcion, c.precio, c.imagen_principal,
       CASE WHEN f.id_favorito IS NOT NULL THEN 1 ELSE 0 END AS es_favorito
FROM catalogo c
LEFT JOIN favoritos f ON f.id_articulo = c.id_articulo AND f.id_cliente = ?
WHERE c.estado='activo' AND c.visible=1
";

if (!empty($busqueda)) {
    $sql .= " AND (c.nombre_articulo LIKE ? OR c.descripcion LIKE ?)";
}
if (!empty($categoria)) {
    $sql .= " AND c.categoria = ?";
}
$sql .= " ORDER BY c.fecha_creacion DESC";

$stmt = $conn->prepare($sql);

if (!empty($busqueda) && !empty($categoria)) {
    $like = "%$busqueda%";
    $stmt->bind_param("isss", $id_cliente, $like, $like, $categoria);
} elseif (!empty($busqueda)) {
    $like = "%$busqueda%";
    $stmt->bind_param("iss", $id_cliente, $like, $like);
} elseif (!empty($categoria)) {
    $stmt->bind_param("is", $id_cliente, $categoria);
} else {
    $stmt->bind_param("i", $id_cliente);
}

$stmt->execute();
$res = $stmt->get_result();

$baseUrl = "http://localhost/flor_express/";
if ($res->num_rows === 0) {
    echo "<div class='col-span-full text-gray-500 text-center p-6'>No se encontraron productos.</div>";
    exit;
}

while ($art = $res->fetch_assoc()):
    $imgSrc = !empty($art['imagen_principal'])
        ? $baseUrl . ltrim($art['imagen_principal'], '/')
        : "img/placeholder-flor.jpg";
    ?>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover relative">
        <!-- ❤️ Botón de favorito -->
        <button class="btn-favorito absolute top-3 right-3 transition" data-id="<?php echo $art['id_articulo']; ?>">
            <svg class="w-7 h-7 heart-icon transition <?php echo ($art['es_favorito'] ? 'text-red-500' : 'text-gray-400 hover:text-red-500'); ?>"
                xmlns="http://www.w3.org/2000/svg"
                fill="<?php echo ($art['es_favorito'] ? 'currentColor' : 'none'); ?>"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 016.364 0L12 7.636l1.318-1.318a4.5 4.5 0 116.364 6.364L12 21.364l-7.682-8.682a4.5 4.5 0 010-6.364z" />
            </svg>
        </button>

        <!-- Imagen -->
        <div class="h-48 w-full overflow-hidden">
            <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                alt="<?php echo htmlspecialchars($art['nombre_articulo']); ?>"
                class="w-full h-full object-cover">
        </div>

        <!-- Info -->
        <div class="p-6">
            <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($art['nombre_articulo']); ?></h3>
            <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($art['descripcion']); ?></p>
            <div class="flex justify-between items-center">
                <span class="text-2xl font-bold text-magenta-flor">$<?php echo number_format($art['precio'], 2); ?> MXN</span>
                <button class="bg-verde-hoja text-white px-4 py-2 rounded-lg hover:bg-green-600 smooth-transition btn-agregar-carrito"
                        data-id="<?php echo $art['id_articulo']; ?>">Agregar</button>
            </div>
        </div>
    </div>
<?php endwhile; ?>
