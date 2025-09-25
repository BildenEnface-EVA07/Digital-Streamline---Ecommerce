<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$id = $_GET['id'] ?? null;
$editing = false;

$producto = [
    'code' => '',
    'nombre' => '',
    'descripcion' => '',
    'precio' => '',
    'stock' => '',
    'categoria_id' => '',
    'imagen_url' => ''
];

// Obtener categorías
$categorias = $pdo->query("SELECT categoria_id, nombre FROM categorias ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($id) {
    $editing = true;
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE product_id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$producto) {
        header("Location: products.php?msg=Producto no encontrado");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos
    $code = trim($_POST['code'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = floatval($_POST['precio'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categoria_id = $_POST['categoria_id'] ?: null;
    $imagen_url = trim($_POST['imagen_url'] ?? '');

    // Validaciones
    if ($code === '' || $nombre === '' || $precio <= 0) {
        $error = "El código, nombre y un precio válido son obligatorios.";
    } elseif ($imagen_url && !filter_var($imagen_url, FILTER_VALIDATE_URL)) {
        $error = "La URL de la imagen no es válida.";
    } else {
        // Validar duplicado de código
        $sqlDup = "SELECT product_id FROM productos WHERE code = ?";
        $paramsDup = [$code];
        if ($editing) {
            $sqlDup .= " AND product_id != ?";
            $paramsDup[] = $id;
        }
        $stmtDup = $pdo->prepare($sqlDup);
        $stmtDup->execute($paramsDup);
        if ($stmtDup->fetch()) {
            $error = "Ya existe un producto con el código ingresado.";
        }
    }

    if (empty($error)) {
        try {
            if ($editing) {
                $stmt = $pdo->prepare("
                    UPDATE productos
                    SET code = ?, nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria_id = ?, imagen_url = ?
                    WHERE product_id = ?
                ");
                $stmt->execute([$code, $nombre, $descripcion, $precio, $stock, $categoria_id, $imagen_url, $id]);
                header("Location: products.php?msg=Producto actualizado correctamente");
                exit;
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO productos (code, nombre, descripcion, precio, stock, categoria_id, imagen_url, fecha_registro)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$code, $nombre, $descripcion, $precio, $stock, $categoria_id, $imagen_url]);
                header("Location: products.php?msg=Producto agregado correctamente");
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
  <title><?= $editing ? 'Editar Producto' : 'Agregar Producto' ?></title>
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
    <h1><?= $editing ? 'Editar Producto' : 'Agregar Producto' ?></h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
      <label>Código:
        <input type="text" name="code" value="<?= htmlspecialchars($producto['code'] ?? '') ?>" required>
      </label>

      <label>Nombre:
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>" required>
      </label>

      <label>Descripción:
        <textarea name="descripcion"><?= htmlspecialchars($producto['descripcion'] ?? '') ?></textarea>
      </label>

      <label>Precio:
        <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio'] ?? '') ?>" required>
      </label>

      <label>Stock:
        <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock'] ?? 0) ?>" min="0">
      </label>

      <label>Categoría:
        <select name="categoria_id">
          <option value="">-- Seleccionar --</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['categoria_id'] ?>" <?= (string)$producto['categoria_id'] === (string)$c['categoria_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Imagen (URL):
        <input type="text" name="imagen_url" value="<?= htmlspecialchars($producto['imagen_url'] ?? '') ?>" placeholder="https://ejemplo.com/imagen.jpg">
      </label>

      <?php if (!empty($producto['imagen_url'])): ?>
        <div style="margin:8px 0;">
          <strong>Previsualización:</strong><br>
          <img src="<?= htmlspecialchars($producto['imagen_url']) ?>" alt="Imagen producto" style="max-width:200px; border:1px solid #ddd; padding:4px; border-radius:4px;">
        </div>
      <?php endif; ?>

      <button type="submit" class="btn btn-add">💾 Guardar</button>
      <a href="products.php" class="btn">↩️ Cancelar</a>
    </form>
  </div>
</body>
</html>
