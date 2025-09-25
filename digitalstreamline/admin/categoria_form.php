<?php
require_once __DIR__ . '/../auth/_init.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$id = $_GET['id'] ?? null;
$editing = false;

$categoria = ['nombre' => ''];

if ($id) {
    $editing = true;
    $stmt = $pdo->prepare("SELECT * FROM categorias WHERE categoria_id=?");
    $stmt->execute([$id]);
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$categoria) {
        header("Location: categorias.php?msg=Categoría no encontrada");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');

    if (!$nombre) {
        $error = "El nombre es obligatorio";
    } else {
        try {
            if ($editing) {
                $stmt = $pdo->prepare("UPDATE categorias SET nombre=? WHERE categoria_id=?");
                $stmt->execute([$nombre, $id]);
                header("Location: categorias.php?msg=Categoría actualizada");
                exit;
            } else {
                $stmt = $pdo->prepare("INSERT INTO categorias (nombre, fecha_registro) VALUES (?, NOW())");
                $stmt->execute([$nombre]);
                header("Location: categorias.php?msg=Categoría agregada");
                exit;
            }
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
  <title><?= $editing ? 'Editar Categoría' : 'Nueva Categoría' ?></title>
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
    <h1><?= $editing ? 'Editar Categoría' : 'Agregar Categoría' ?></h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
      <label>Nombre:
        <input type="text" name="nombre" value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
      </label>

      <button type="submit" class="btn btn-add">💾 Guardar</button>
      <a href="categorias.php" class="btn">↩️ Cancelar</a>
    </form>
  </div>
</body>
</html>
