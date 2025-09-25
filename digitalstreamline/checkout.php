<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Checkout - DigitalStreamline</title>
  <link rel="stylesheet" href="/digitalstreamline/assets/css/styles.css">
</head>
<body>
  <?php include __DIR__ . '/components/header.php'; ?>
  <?php include __DIR__ . '/components/topnav.php'; ?>

  <main class="container">
    <h1>Checkout</h1>

    <?php
    $success = $_GET['success'] ?? null;
    $orderId = $_GET['orderId'] ?? null;
    $error   = $_GET['error'] ?? null;

    if ($success === "1" && $orderId): ?>
        <div class="alert success">
          <p>Pedido realizado con éxito. Número de pedido: <strong>#<?= htmlspecialchars($orderId) ?></strong></p>
          <a href="/digitalstreamline/index.php" class="btn">Volver al inicio</a>
        </div>
    <?php elseif ($success === "0" && $error): ?>
        <div class="alert error">
          <p><?= htmlspecialchars($error) ?></p>
          <a href="/digitalstreamline/carrito.php" class="btn">Volver al carrito</a>
        </div>
    <?php else: ?>
        <p>No hay información de pedido para mostrar.</p>
        <a href="/digitalstreamline/index.php" class="btn">Volver al inicio</a>
    <?php endif; ?>
  </main>
</body>
</html>
