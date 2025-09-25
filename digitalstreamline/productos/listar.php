<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../auth/_init.php');

$categoria = $_GET['categoria'] ?? null;
$q = $_GET['q'] ?? null;

$sql = "SELECT * FROM productos WHERE 1=1";
$params = [];

if ($categoria) {
    $sql .= " AND categoria_id = ?";
    $params[] = $categoria;
}
if ($q) {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ? OR marca LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse(["success" => true, "productos" => $productos]);
