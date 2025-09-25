<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$stmt = $pdo->query("
    SELECT 
        p.product_id, 
        p.nombre AS producto_nombre, 
        p.precio, 
        p.stock, 
        c.nombre AS categoria_nombre
    FROM productos p
    LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
    ORDER BY p.product_id DESC
");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <aside class="sidebar">
    <h2>Admin</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="users.php">👤 Usuarios</a>
    <a href="products.php" class="active">📦 Productos</a>
    <a href="categorias.php">🗂 Categorías</a>
    <a href="pedidos.php">📝 Pedidos</a>
    <a href="config.php">⚙️ Configuración</a>
  </aside>

  <div class="main">
    <h1>Productos</h1>
    <?php if (!empty($_GET['msg'])): ?>
      <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <a href="product_form.php" class="btn btn-add">➕ Nuevo Producto</a>

    <table>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Precio</th>
        <th>Stock</th>
        <th>Categoría</th>
        <th>Acciones</th>
      </tr>
      <?php foreach ($productos as $p): ?>
      <tr>
        <td><?= $p['product_id'] ?></td>
        <td><?= htmlspecialchars($p['producto_nombre']) ?></td>
        <td>L <?= number_format($p['precio'], 2) ?></td>
        <td><?= $p['stock'] ?></td>
        <td><?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></td>
        <td>
          <a href="product_form.php?id=<?= $p['product_id'] ?>" class="btn btn-edit">✏️ Editar</a>
          <a href="product_delete.php?id=<?= $p['product_id'] ?>" class="btn btn-delete"
             onclick="return confirm('¿Seguro de eliminar este producto?')">🗑️ Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
