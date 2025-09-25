<?php
require_once __DIR__ . '/../auth/_init.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE product_id=?");
        $stmt->execute([$id]);
        header("Location: products.php?msg=Producto eliminado correctamente");
        exit;
    } catch (PDOException $e) {
        header("Location: products.php?msg=Error al eliminar: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: products.php?msg=ID inválido");
    exit;
}
