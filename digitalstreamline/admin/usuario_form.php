<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin puede entrar
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$id = $_GET['id'] ?? null;
$editing = false;
$usuario = [
    'nombre' => '',
    'email' => '',
    'telefono' => '',
    'direccion' => '',
    'rol' => 'cliente'
];

if ($id) {
    $editing = true;
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user_id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        header("Location: users.php?msg=Usuario no encontrado");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $email    = strtolower(trim($_POST['email'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion= trim($_POST['direccion'] ?? '');
    $rol      = $_POST['rol'] ?? 'cliente';
    $password = $_POST['password'] ?? '';

    if (!$nombre || !$email) {
        $error = "Nombre y email son obligatorios";
    } elseif (!$editing && !$password) {
        $error = "La contraseña es obligatoria para nuevos usuarios";
    } else {
        try {
            if ($editing) {
                if ($password) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios 
                        SET nombre=?, email=?, telefono=?, direccion=?, rol=?, password_hash=? 
                        WHERE user_id=?");
                    $stmt->execute([$nombre, $email, $telefono, $direccion, $rol, $hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuarios 
                        SET nombre=?, email=?, telefono=?, direccion=?, rol=? 
                        WHERE user_id=?");
                    $stmt->execute([$nombre, $email, $telefono, $direccion, $rol, $id]);
                }
                header("Location: users.php?msg=Usuario actualizado correctamente");
                exit;
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, telefono, direccion, rol, password_hash, fecha_registro) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$nombre, $email, $telefono, $direccion, $rol, $hash]);
                header("Location: users.php?msg=Usuario agregado correctamente");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= $editing ? 'Editar Usuario' : 'Nuevo Usuario' ?></title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>
  <aside class="sidebar">
    <h2>Admin</h2>
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="users.php">👤 Usuarios</a>
    <a href="products.php">📦 Productos</a>
    <a href="categorias.php">🗂 Categorías</a>
    <a href="pedidos.php">📝 Pedidos</a>
    <a href="config.php">⚙️ Configuración</a>
  </aside>

  <div class="main">
    <h1><?= $editing ? 'Editar Usuario' : 'Agregar Usuario' ?></h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
      <label>Nombre:
        <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
      </label>

      <label>Email:
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
      </label>

      <label>Teléfono:
        <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>">
      </label>

      <label>Dirección:
        <textarea name="direccion"><?= htmlspecialchars($usuario['direccion']) ?></textarea>
      </label>

      <label>Rol:
        <select name="rol" required>
          <option value="cliente" <?= $usuario['rol'] === 'cliente' ? 'selected' : '' ?>>Cliente</option>
          <option value="admin" <?= $usuario['rol'] === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
      </label>

      <label>Contraseña <?= $editing ? "(dejar vacío para no cambiar)" : "" ?>:
        <input type="password" name="password" <?= $editing ? "" : "required" ?>>
      </label>

      <button type="submit" class="btn btn-add">💾 Guardar</button>
      <a href="users.php" class="btn">↩️ Cancelar</a>
    </form>
  </div>
</body>
</html>
