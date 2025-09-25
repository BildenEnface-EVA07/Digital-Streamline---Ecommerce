<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../auth/_init.php');

$stmt = $conn->query("SELECT * FROM productos ORDER BY precio ASC LIMIT 10");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse(["success" => true, "productos" => $productos]);
