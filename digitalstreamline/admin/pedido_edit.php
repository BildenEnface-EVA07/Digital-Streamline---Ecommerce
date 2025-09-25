<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: pedidos.php?msg=ID inválido");
    exit;
}

// Obtener pedido
$stmt = $pdo->prepare("
    SELECT p.*, u.nombre AS cliente 
    FROM pedidos p
    LEFT JOIN usuarios u ON p.user_id = u.user_id
    WHERE p.pedido_id = ?
");
$stmt->execute([$id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: pedidos.php?msg=Pedido no encontrado");
    exit;
}

// Obtener estados
$estados = $pdo->query("SELECT estado_pedido_id, nombre FROM estado_pedido")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $estado_pedido_id = $_POST['estado_pedido_id'] ?? null;

    if (!$estado_pedido_id) {
        $error = "Debes seleccionar un estado.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE pedidos SET estado_pedido_id=? WHERE pedido_id=?");
            $stmt->execute([$estado_pedido_id, $id]);
            header("Location: pedidos.php?msg=✅ Pedido actualizado");
            exit;
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Pedido</title>
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
    <h1>Editar Pedido #<?= $pedido['pedido_id'] ?></h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
      <label>Cliente:
        <input type="text" value="<?= htmlspecialchars($pedido['cliente'] ?? 'Usuario eliminado') ?>" disabled>
      </label>

      <label>Total:
        <input type="text" value="L <?= number_format($pedido['total'], 2) ?>" disabled>
      </label>

      <label>Estado:
        <select name="estado_pedido_id" required>
          <option value="">-- Seleccionar --</option>
          <?php foreach ($estados as $e): ?>
            <option value="<?= $e['estado_pedido_id'] ?>" <?= $pedido['estado_pedido_id'] == $e['estado_pedido_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($e['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <button type="submit" class="btn btn-add">💾 Guardar</button>
      <a href="pedidos.php" class="btn">↩️ Cancelar</a>
    </form>
  </div>
</body>
</html>
