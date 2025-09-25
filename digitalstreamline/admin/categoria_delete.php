<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: categorias.php?msg=ID inválido");
    exit;
}

$id = (int) $_GET['id'];

// Verificar que exista
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE categoria_id=?");
$stmt->execute([$id]);
$categoria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categoria) {
    header("Location: categorias.php?msg=Categoría no encontrada");
    exit;
}

// Eliminar
$stmt = $pdo->prepare("DELETE FROM categorias WHERE categoria_id=?");
if ($stmt->execute([$id])) {
    header("Location: categorias.php?msg=Categoría eliminada");
    exit;
} else {
    header("Location: categorias.php?msg=Error al eliminar categoría");
    exit;
}
