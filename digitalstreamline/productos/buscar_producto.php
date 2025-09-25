<?php
require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../auth/_init.php');

// Evitar redeclaración de la función
if (!function_exists('jsonResponse')) {
    function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

$productId = $_GET['id'] ?? null;

if (!$productId || !is_numeric($productId)) {
    jsonResponse(["success" => false, "error" => "ID de producto inválido"]);
}

// Consultar el producto por ID
$sql = "SELECT p.product_id, p.code, p.nombre, p.marca, p.descripcion, p.precio, p.stock, p.imagen_url,
               c.categoria_id, c.nombre AS categoria
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.categoria_id
        WHERE p.product_id = :productId
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute([":productId" => $productId]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    jsonResponse(["success" => false, "error" => "Producto no encontrado"]);
}

// Productos similares
$sqlSimilares = "SELECT product_id, nombre, imagen_url
                 FROM productos
                 WHERE categoria_id = :catId AND product_id != :productId
                 ORDER BY RAND() LIMIT 4";
$stmtSim = $pdo->prepare($sqlSimilares);
$stmtSim->execute([
    ":catId" => $producto['categoria_id'],
    ":productId" => $producto['product_id']
]);
$similares = $stmtSim->fetchAll(PDO::FETCH_ASSOC);

// Devolver JSON
jsonResponse([
    "success" => true,
    "producto" => $producto,
    "similares" => $similares
]);
