<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'msg' => 'ID de producto inválido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT product_id, nombre, precio, imagen_url FROM productos WHERE product_id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo json_encode(['success' => false, 'msg' => 'Producto no encontrado']);
        exit;
    }

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Si ya existe en el carrito, sumamos la cantidad
    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id]['cantidad'] += 1;
    } else {
        $_SESSION['carrito'][$id] = [
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'imagen_url' => $producto['imagen_url'],
            'cantidad' => 1
        ];
    }

    // Contador total (sumar cantidades)
    $totalProductos = 0;
    foreach ($_SESSION['carrito'] as $item) {
        $totalProductos += $item['cantidad'];
    }

    echo json_encode(['success' => true, 'msg' => 'Agregado al carrito', 'total' => $totalProductos]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => 'Error en servidor']);
}
