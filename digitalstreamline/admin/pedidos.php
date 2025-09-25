<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$stmt = $pdo->query("
    SELECT p.order_id, p.user_id, u.nombre AS cliente, p.total, p.fecha_pedido, ep.nombre AS estado
    FROM pedidos p
    LEFT JOIN usuarios u ON p.user_id = u.user_id
    LEFT JOIN estado_pedido ep ON p.estado_pedido_id = ep.estado_pedido_id
    ORDER BY p.order_id DESC
");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pedidos</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <aside class="sidebar">
    <h2>Admin</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="users.php">👤 Usuarios</a>
    <a href="products.php">📦 Productos</a>
    <a href="categorias.php">🗂 Categorías</a>
    <a href="pedidos.php" class="active">📝 Pedidos</a>
    <a href="config.php">⚙️ Configuración</a>
  </aside>

  <div class="main">
    <h1>Pedidos</h1>
    <?php if (!empty($_GET['msg'])): ?>
      <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <table>
      <tr>
        <th>ID</th>
        <th>Cliente</th>
        <th>Total</th>
        <th>Estado</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
      <?php foreach ($pedidos as $p): ?>
      <tr>
        <td><?= $p['pedido_id'] ?></td>
        <td><?= htmlspecialchars($p['cliente'] ?? 'Usuario eliminado') ?></td>
        <td>L <?= number_format($p['total'], 2) ?></td>
        <td><?= htmlspecialchars($p['estado']) ?></td>
        <td><?= $p['fecha_pedido'] ?></td>
        <td>
          <a href="pedido_edit.php?id=<?= $p['order_id'] ?>" class="btn btn-edit">✏️ Editar</a>
          <a href="pedido_delete.php?id=<?= $p['order_id'] ?>" class="btn btn-delete"
             onclick="return confirm('¿Seguro de eliminar este pedido?')">🗑️ Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
