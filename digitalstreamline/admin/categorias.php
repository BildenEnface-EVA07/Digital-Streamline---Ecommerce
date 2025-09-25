<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$stmt = $pdo->query("SELECT categoria_id, nombre, fecha_registro FROM categorias ORDER BY categoria_id DESC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Categorías</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <aside class="sidebar">
    <h2>Admin</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="users.php">👤 Usuarios</a>
    <a href="products.php">📦 Productos</a>
    <a href="categorias.php" class="active">🗂 Categorías</a>
    <a href="pedidos.php">📝 Pedidos</a>
    <a href="config.php">⚙️ Configuración</a>
  </aside>

  <div class="main">
    <h1>Categorías</h1>
    <?php if (!empty($_GET['msg'])): ?>
      <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <a href="categoria_form.php" class="btn btn-add">➕ Nueva Categoría</a>

    <table>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Fecha Registro</th>
        <th>Acciones</th>
      </tr>
      <?php foreach ($categorias as $c): ?>
      <tr>
        <td><?= $c['categoria_id'] ?></td>
        <td><?= htmlspecialchars($c['nombre']) ?></td>
        <td><?= $c['fecha_registro'] ?></td>
        <td>
          <a href="categoria_form.php?id=<?= $c['categoria_id'] ?>" class="btn btn-edit">✏️ Editar</a>
          <a href="categoria_delete.php?id=<?= $c['categoria_id'] ?>" class="btn btn-delete"
             onclick="return confirm('¿Seguro de eliminar esta categoría?')">🗑️ Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
