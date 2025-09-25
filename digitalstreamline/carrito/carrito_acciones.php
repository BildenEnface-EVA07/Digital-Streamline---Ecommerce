<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Actualizar cantidades
if (isset($_POST['actualizar']) && !empty($_POST['cantidades'])) {
    foreach ($_POST['cantidades'] as $id => $cantidad) {
        $cantidad = (int)$cantidad;
        if ($cantidad > 0) {
            $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
        }
    }
}

// Eliminar un producto
if (isset($_POST['eliminar'])) {
    $id = $_POST['eliminar'];
    unset($_SESSION['carrito'][$id]);
}

header("Location: carrito.php");
exit;
