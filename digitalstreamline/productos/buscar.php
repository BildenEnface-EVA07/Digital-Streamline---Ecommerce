<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../auth/_init.php');

$q = $_GET['q'] ?? '';

$stmt = $conn->prepare("SELECT * FROM productos 
                        WHERE nombre LIKE ? OR descripcion LIKE ? OR marca LIKE ?");
$stmt->execute(["%$q%", "%$q%", "%$q%"]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse(["success" => true, "productos" => $productos]);
