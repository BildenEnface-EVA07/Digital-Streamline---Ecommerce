<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

$action = $_POST['action'] ?? null;
$productId = $_POST['product_id'] ?? null;

if ($action && $productId) {
    if (!isset($_SESSION['carrito'][$productId])) {
        header("Location: carrito.php");
        exit;
    }

    switch ($action) {
        case "actualizar":
            $cantidad = max(1, (int)($_POST['cantidad'] ?? 1));
            $_SESSION['carrito'][$productId]['cantidad'] = $cantidad;
            break;

        case "eliminar":
            unset($_SESSION['carrito'][$productId]);
            break;
    }

    header("Location: carrito.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    $metodo_pago = $_POST['metodo_pago'] ?? null;
    $carrito = $_SESSION['carrito'] ?? [];

    if (!$metodo_pago) {
        echo "<p style='color:red'>Debes seleccionar un método de pago.</p>";
    } elseif (!empty($carrito)) {
        try {
            $pdo->beginTransaction();

            // Calcular total
            $total = 0;
            foreach ($carrito as $item) {
                $total += $item['precio'] * $item['cantidad'];
            }

            // 1. Crear pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos (user_id, estado_pedido_id, total, fecha_pedido, fecha_registro) 
                                   VALUES (?, ?, ?, NOW(), NOW())");
            $estadoPendiente = 1; // pendiente
            $userId = $_SESSION['user_id'] ?? null;
            $stmt->execute([$userId, $estadoPendiente, $total]);
            $orderId = $pdo->lastInsertId();

            // 2. Insertar detalles
            $stmtDetalle = $pdo->prepare("INSERT INTO detalle_pedido (order_id, product_id, cantidad, precio_unitario, fecha_registro) 
                                          VALUES (?, ?, ?, ?, NOW())");
            foreach ($carrito as $id => $item) {
                $stmtDetalle->execute([$orderId, $id, $item['cantidad'], $item['precio']]);
            }

            // 3. Insertar pago
            $stmtPago = $pdo->prepare("INSERT INTO pagos (order_id, metodo_pago_id, monto, fecha_pago, fecha_registro) 
                                       VALUES (?, ?, ?, NOW(), NOW())");
            $stmtPago->execute([$orderId, $metodo_pago, $total]);

            $pdo->commit();

            // Vaciar carrito
            $_SESSION['carrito'] = [];
            header("Location: /digitalstreamline/checkout.php?success=1&orderId=$orderId");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = urlencode("Error al procesar el pedido: " . $e->getMessage());
            header("Location: /digitalstreamline/checkout.php?success=0&error=$error");
            exit;
        }
    }
}
