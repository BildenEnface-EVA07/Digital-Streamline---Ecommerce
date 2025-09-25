<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../auth/_init.php');

$sql = "SELECT c.categoria_id, c.nombre, COUNT(p.product_id) as total_productos
        FROM categorias c
        LEFT JOIN productos p ON c.categoria_id = p.categoria_id
        GROUP BY c.categoria_id, c.nombre";
$stmt = $pdo->query($sql);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

jsonResponse(["success" => true, "categorias" => $categorias]);
