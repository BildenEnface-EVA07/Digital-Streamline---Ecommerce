<?php
require_once __DIR__ . '/../auth/_init.php';

// Verificar que el usuario sea administrador
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

// Obtener totales desde la BD
try {
    // Total de usuarios
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $totalUsuarios = $stmt->fetchColumn();

    // Total de productos
    $stmt = $pdo->query("SELECT COUNT(*) FROM productos");
    $totalProductos = $stmt->fetchColumn();

    // Total de pedidos
    $stmt = $pdo->query("SELECT COUNT(*) FROM pedidos");
    $totalPedidos = $stmt->fetchColumn();

    // Total de ventas
    $stmt = $pdo->query("SELECT COALESCE(SUM(total), 0) FROM pedidos");
    $totalVentas = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Admin</title>
  <link rel="stylesheet" href="/digitalstreamline/assets/css/styles.css">
  <style>
    body {
      display: flex;
      min-height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    /* Sidebar */
    .sidebar {
      width: 220px;
      background: #222;
      color: #fff;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
    }
    .sidebar h2 {
      text-align: center;
      padding: 1rem;
      border-bottom: 1px solid #444;
      margin: 0;
    }
    .sidebar nav {
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .sidebar nav a {
      padding: 12px 20px;
      text-decoration: none;
      color: #ccc;
      transition: background 0.2s;
    }
    .sidebar nav a:hover {
      background: #444;
      color: #fff;
    }

    /* Main */
    .main {
      flex: 1;
      padding: 20px;
      background: #f4f6f9;
    }
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .topbar h1 {
      margin: 0;
    }
    .btn-logout {
      padding: 8px 12px;
      background: crimson;
      color: #fff;
      border: none;
      border-radius: 4px;
      text-decoration: none;
    }
    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
    }
    .card {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
    }
    .card h3 {
      margin-top: 0;
      font-size: 1.1rem;
      color: #555;
    }
    .card p {
      font-size: 1.8rem;
      font-weight: bold;
      margin: 0;
      color: #333;
    }
  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2>Admin</h2>
    <nav>
      <a href="dashboard.php">📊 Dashboard</a>
      <a href="users.php">👤 Usuarios</a>
      <a href="products.php">📦 Productos</a>
      <a href="categorias.php">🗂 Categorías</a>
      <a href="pedidos.php">📝 Pedidos</a>
      <a href="config.php">⚙️ Configuración</a>
    </nav>
  </aside>

  <!-- Main content -->
  <div class="main">
    <div class="topbar">
      <h1>Panel de Control</h1>
      <a href="/digitalstreamline/auth/logout.php" class="btn-logout">Cerrar sesión</a>
    </div>

    <div class="grid">
      <div class="card">
        <h3>Usuarios registrados</h3>
        <p><?= $totalUsuarios ?></p>
      </div>
      <div class="card">
        <h3>Productos disponibles</h3>
        <p><?= $totalProductos ?></p>
      </div>
      <div class="card">
        <h3>Pedidos realizados</h3>
        <p><?= $totalPedidos ?></p>
      </div>
      <div class="card">
        <h3>Ventas totales</h3>
        <p>L <?= number_format($totalVentas, 2) ?></p>
      </div>
    </div>
  </div>
</body>
</html>
