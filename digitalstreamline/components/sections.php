<?php
require_once __DIR__ . '/../auth/_init.php';
require_once __DIR__ . '/section.php';


function getProductsByCategory(PDO $pdo, int $categoryId, int $limit = 8): array {
    $stmt = $pdo->prepare("SELECT product_id, nombre AS title, imagen_url AS img 
                           FROM productos 
                           WHERE categoria_id = :cat 
                           ORDER BY fecha_registro DESC 
                           LIMIT :limit");
    $stmt->bindValue(':cat', $categoryId, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Agregar enlace a producto.php
    foreach ($productos as &$p) {
        $p['url'] = "/digitalstreamline/producto.php?id=" . $p['product_id'];
    }

    return $productos;
}



$productsComputers   = getProductsByCategory($pdo, 1); // 1 = computadoras
$productsPhones      = getProductsByCategory($pdo, 2); // 2 = celulares
$productsAccessories = getProductsByCategory($pdo, 3); // 3 = accesorios


renderSection('Lo más vendido en Computadoras', $productsComputers, '/computadoras');
renderSection('Lo más vendido en Celulares', $productsPhones, '/celulares');
renderSection('Accesorios más buscados', $productsAccessories, '/accesorios');
