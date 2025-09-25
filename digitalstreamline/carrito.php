<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/auth/_init.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// --- Procesar acciones del formulario ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['product_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($id && isset($_SESSION['carrito'][$id])) {
        if ($action === 'actualizar') {
            $cantidad = max(1, (int)($_POST['cantidad'] ?? 1));
            $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
        } elseif ($action === 'eliminar') {
            unset($_SESSION['carrito'][$id]);
        }
    }

    header("Location: carrito.php");
    exit;
}

$carrito = $_SESSION['carrito'] ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Carrito</title>
  <link rel="stylesheet" href="/digitalstreamline/assets/css/styles.css">
  <style>
    body { font-family: Arial, sans-serif; background:#f5f5f5; color:#333; margin:0; padding:0; }
    .container { max-width:1200px; margin:0 auto; padding:20px; }

    h1 { margin-bottom:20px; }

    .grid { display:flex; flex-direction:column; gap:20px; }

    .producto-card {
        display:flex;
        background:#fff;
        padding:15px;
        border-radius:8px;
        box-shadow:0 2px 6px rgba(0,0,0,0.1);
        align-items:center;
        gap:20px;
    }

    .producto-card img {
        width:120px;
        height:120px;
        object-fit:cover;
        border-radius:8px;
    }

    .producto-info { flex:1; }
    .producto-info h3 { margin:0 0 10px 0; font-size:1.1rem; }
    .producto-info p { margin:5px 0; }

    .carrito-form { display:flex; align-items:center; gap:10px; margin:10px 0; }
    .carrito-form input[type="number"] {
        width:60px;
        padding:5px;
        border:1px solid #ccc;
        border-radius:4px;
    }
    .carrito-form button {
        padding:6px 12px;
        border:none;
        border-radius:4px;
        background:#ffd814;
        cursor:pointer;
        font-weight:bold;
        transition: background 0.2s;
    }
    .carrito-form button:hover { background:#f7ca00; }

    .resumen {
        margin-top:30px;
        background:#fff;
        padding:20px;
        border-radius:8px;
        box-shadow:0 4px 8px rgba(0,0,0,0.1);
        max-width:400px;
    }
    .resumen h2 { margin-top:0; }
    .resumen p { margin:10px 0; }
    .resumen select { padding:5px; border-radius:4px; border:1px solid #ccc; width:100%; }
    .resumen button.btn {
        margin-top:15px;
        width:100%;
        padding:10px;
        font-size:1rem;
        font-weight:bold;
        background:#ffd814;
        border:none;
        border-radius:4px;
        cursor:pointer;
        transition: background 0.2s;
    }
    .resumen button.btn:hover { background:#f7ca00; }
  </style>
</head>
<body>
  <?php include __DIR__ . '/components/header.php'; ?>
  <?php include __DIR__ . '/components/topnav.php'; ?>

  <main class="container">
    <h1>Mi Carrito</h1>

    <?php if (empty($carrito)): ?>
      <p>Tu carrito está vacío.</p>
    <?php else: ?>
      <div class="grid">
        <?php 
        $total = 0;
        foreach ($carrito as $id => $item): 
          $subtotal = $item['precio'] * $item['cantidad'];
          $total += $subtotal;
        ?>
          <div class="producto-card">
            <img src="/digitalstreamline/uploads/<?= htmlspecialchars($item['imagen_url'] ?? 'noimage.png') ?>" 
                 alt="<?= htmlspecialchars($item['nombre']) ?>">

            <div class="producto-info">
                <h3><?= htmlspecialchars($item['nombre']) ?></h3>
                <p><strong>L <?= number_format($item['precio'], 2) ?></strong></p>

                <form action="carrito.php" method="POST" class="carrito-form">
                  <input type="hidden" name="product_id" value="<?= $id ?>">
                  <input type="number" name="cantidad" min="1" value="<?= $item['cantidad'] ?>">
                  <button type="submit" name="action" value="actualizar">Actualizar</button>
                  <button type="submit" name="action" value="eliminar">Eliminar</button>
                </form>

                <p>Subtotal: L <?= number_format($subtotal, 2) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="resumen">
          <h2>Resumen de compra</h2>
          <p>Productos: <?= count($carrito) ?></p>
          <p>Total: L <?= number_format($total, 2) ?></p>
          
          <?php  $metodos = $pdo->query("SELECT metodo_pago_id, nombre_metodo FROM metodo_pago")->fetchAll(PDO::FETCH_ASSOC); ?>
          <form action="/digitalstreamline/carrito/carrito.php" method="POST">
              <label for="metodo_pago">Método de pago:</label>
              <select name="metodo_pago" id="metodo_pago" required>
                  <option value="">Seleccione...</option>
                  <?php foreach ($metodos as $m): ?>
                      <option value="<?= $m['metodo_pago_id'] ?>"><?= htmlspecialchars($m['nombre_metodo']) ?></option>
                  <?php endforeach; ?>
              </select>
              <button type="submit" name="finalizar" class="btn">Finalizar compra</button>
          </form>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>
