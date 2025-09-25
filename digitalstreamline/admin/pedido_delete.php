<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pedidos.php?msg=ID inválido");
    exit;
}

$id = (int) $_GET['id'];

// Verificar que exista
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE pedido_id=?");
$stmt->execute([$id]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header("Location: pedidos.php?msg=Pedido no encontrado");
    exit;
}

// Eliminar
$stmt = $pdo->prepare("DELETE FROM pedidos WHERE pedido_id=?");
if ($stmt->execute([$id])) {
    header("Location: pedidos.php?msg=Pedido eliminado");
    exit;
} else {
    header("Location: pedidos.php?msg=Error al eliminar pedido");
    exit;
}
