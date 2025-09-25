<?php
require_once __DIR__ . '/../auth/_init.php';

// Solo admin
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'admin') {
    header("Location: /digitalstreamline/index.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php?msg=ID de usuario inválido");
    exit;
}

$id = (int) $_GET['id'];

// Evitar que un admin se borre a sí mismo
if ($id === (int) $_SESSION['user']['user_id']) {
    header("Location: users.php?msg=No puedes eliminar tu propio usuario");
    exit;
}

// Verificar si el usuario existe
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user_id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: users.php?msg=Usuario no encontrado");
    exit;
}

// Eliminar usuario
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE user_id = ?");
if ($stmt->execute([$id])) {
    header("Location: users.php?msg=Usuario eliminado con éxito");
    exit;
} else {
    header("Location: users.php?msg=Error al eliminar el usuario");
    exit;
}
