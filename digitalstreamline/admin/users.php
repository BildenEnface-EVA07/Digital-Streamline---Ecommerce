<?php
require_once __DIR__ . '/../auth/_init.php';

// Verificar si es admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

// Manejar eliminar usuario
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE user_id = ?");
        $stmt->execute([$deleteId]);
        header("Location: users.php?msg=Usuario eliminado correctamente");
        exit;
    } catch (PDOException $e) {
        $error = "Error al eliminar: " . $e->getMessage();
    }
}

// Obtener todos los usuarios
$stmt = $pdo->query("SELECT user_id, nombre, email, telefono, rol, fecha_registro FROM usuarios ORDER BY user_id DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Usuarios - Admin</title>
  <link rel="stylesheet" href="/digitalstreamline/assets/css/styles.css">
  <style>
    body { display: flex; margin:0; font-family: Arial, sans-serif; }
    .sidebar { width:220px; background:#222; color:#fff; height:100vh; padding-top:1rem; }
    .sidebar a { display:block; padding:10px 20px; color:#ccc; text-decoration:none; }
    .sidebar a:hover { background:#444; color:#fff; }
    .main { flex:1; padding:20px; background:#f4f6f9; }
    h1 { margin-top:0; }
    .btn { padding:6px 10px; border-radius:4px; text-decoration:none; font-size:14px; }
    .btn-add { background:green; color:#fff; }
    .btn-edit { background:orange; color:#fff; }
    .btn-delete { background:crimson; color:#fff; }
    table { width:100%; border-collapse: collapse; background:#fff; }
    th, td { padding:10px; border:1px solid #ddd; text-align:left; }
    th { background:#eee; }
    .msg { padding:10px; margin-bottom:15px; background:#dff0d8; color:#3c763d; border-radius:4px; }
    .error { padding:10px; margin-bottom:15px; background:#f2dede; color:#a94442; border-radius:4px; }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2 style="text-align:center;">Admin</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="users.php">👤 Usuarios</a>
    <a href="products.php">📦 Productos</a>
    <a href="categorias.php">🗂 Categorías</a>
    <a href="pedidos.php">📝 Pedidos</a>
    <a href="config.php">⚙️ Configuración</a>
  </aside>

  <!-- Main -->
  <div class="main">
    <h1>Gestión de Usuarios</h1>

    <?php if (!empty($_GET['msg'])): ?>
      <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <a href="usuario_form.php" class="btn btn-add">➕ Agregar Usuario</a>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Email</th>
          <th>Teléfono</th>
          <th>Rol</th>
          <th>Fecha Registro</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($usuarios as $u): ?>
          <tr>
            <td><?= $u['user_id'] ?></td>
            <td><?= htmlspecialchars($u['nombre']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['telefono']) ?></td>
            <td><?= htmlspecialchars($u['rol']) ?></td>
            <td><?= $u['fecha_registro'] ?></td>
            <td>
              <a href="usuario_form.php?id=<?= $u['user_id'] ?>" class="btn btn-edit">✏️ Editar</a>
              <a href="users.php?delete=<?= $u['user_id'] ?>" class="btn btn-delete" onclick="return confirm('¿Seguro que quieres eliminar este usuario?')">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>

