<?php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(404);
    die("Producto no especificado");
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Producto - DigitalStreamline</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/digitalstreamline/assets/css/styles.css">
</head>
<style>
body { font-family: Arial, sans-serif; background:#f5f5f5; color:#333; margin:0; padding:0; }
.container { max-width:1200px; margin:0 auto; padding:20px; }

.product-detail {
    display:flex;
    flex-wrap:wrap;
    background:#fff;
    padding:20px;
    border-radius:8px;
    box-shadow:0 4px 8px rgba(0,0,0,0.1);
}

.product-detail__gallery {
    flex:1 1 400px;
    max-width:500px;
    margin-right:30px;
}
.product-detail__gallery img {
    width:100%;
    border-radius:8px;
}

.product-detail__info {
    flex:1 1 300px;
}
.product-detail__info h1 {
    font-size:1.8rem;
    margin-bottom:10px;
}
.product-detail__category a {
    text-decoration:none;
    color:#007185;
    font-weight:bold;
}
.product-detail__price {
    font-size:1.5rem;
    color:#b12704;
    margin:15px 0;
}
.product-detail__desc {
    margin:15px 0;
    line-height:1.5;
}

.btn-buy {
    background:#ffd814;
    border:1px solid #fcd200;
    padding:12px 20px;
    font-size:1rem;
    font-weight:bold;
    color:#111;
    cursor:pointer;
    border-radius:4px;
    transition:background 0.2s;
}
.btn-buy:hover { background:#f7ca00; }

.product-description {
    background:#fff;
    padding:20px;
    border-radius:8px;
    margin-top:20px;
    box-shadow:0 4px 8px rgba(0,0,0,0.1);
}

/* similares */
.similar-section { margin-top:30px; }
.similar-grid {
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(180px,1fr));
    gap:20px;
}
.product-card {
    background:#fff;
    border-radius:8px;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
    overflow:hidden;
    text-align:center;
    transition:transform 0.2s, box-shadow 0.2s;
}
.product-card:hover {
    transform:translateY(-5px);
    box-shadow:0 6px 12px rgba(0,0,0,0.15);
}
.product-card__img img {
    width:100%;
    height:150px;
    object-fit:cover;
}
.product-card__title {
    font-size:1rem;
    margin:10px 0;
    padding:0 5px;
    color:#333;
}
</style>

<body>
<?php include __DIR__ . '/components/header.php'; ?>
<?php include __DIR__ . '/components/topnav.php'; ?>

<main class="page product-page container">
    <div class="product-detail">
        <div class="product-detail__gallery">
            <img id="productImg" src="https://via.placeholder.com/800x600?text=Producto" alt="Producto">
        </div>

        <div class="product-detail__info">
            <h1 id="productTitle">Cargando...</h1>

            <p class="product-detail__category">
                Categoría:
                <a id="productCategory" href="#">Cargando...</a>
            </p>

            <p class="product-detail__price" id="productPrice">L 0.00</p>

            <p class="product-detail__desc" id="productDesc">Cargando descripción...</p>

            <button class="btn-buy">Agregar al carrito</button>
        </div>
    </div>

    <section class="product-description">
        <h2>Descripción</h2>
        <p id="productDescSection">Cargando...</p>
    </section>

    <section class="similar-section">
        <h2>Artículos similares</h2>
        <div class="similar-grid" id="similarGrid">
            <!-- Aquí se cargarán productos similares -->
        </div>
    </section>
</main>

<script>
const productId = "<?= htmlspecialchars($id) ?>";

async function cargarProducto() {
    try {
        const res = await fetch(`/digitalstreamline/productos/buscar_producto.php?id=${productId}`);
        const data = await res.json();

        if (!data.success) {
            document.querySelector('.product-detail__info').innerHTML = "<p>Producto no encontrado.</p>";
            document.getElementById('productDescSection').textContent = "";
            return;
        }

        const p = data.producto;

        document.getElementById('productTitle').textContent = p.nombre;
        document.getElementById('productImg').src = `${p.imagen_url}`;
        document.getElementById('productImg').alt = p.nombre;
        document.getElementById('productCategory').textContent = p.categoria;
        document.getElementById('productCategory').href = `/digitalstreamline/categoria.php?cat=${p.categoria_id}`;
        document.getElementById('productPrice').textContent = `L ${parseFloat(p.precio).toFixed(2)}`;
        document.getElementById('productDesc').textContent = p.descripcion;
        document.getElementById('productDescSection').textContent = p.descripcion;

        // Productos similares
        const similarGrid = document.getElementById('similarGrid');
        similarGrid.innerHTML = '';
        if (data.similares && data.similares.length > 0) {
            data.similares.forEach(s => {
                const article = document.createElement('article');
                article.className = 'product-card';
                article.innerHTML = `
                    <a href="/digitalstreamline/producto.php?id=${s.product_id}">
                        <div class="product-card__img">
                            <img src="${s.imagen_url}" alt="${s.nombre}">
                        </div>
                        <h3 class="product-card__title">${s.nombre}</h3>
                    </a>
                `;
                similarGrid.appendChild(article);
            });
        }
    } catch (err) {
        console.error(err);
        document.querySelector('.product-detail__info').innerHTML = "<p>Error al cargar el producto.</p>";
        document.getElementById('productDescSection').textContent = "";
    }
}

cargarProducto();
</script>

<script src="/assets/js/main.js" defer></script>
</body>
</html>
<script>
document.querySelector(".btn-buy").addEventListener("click", async () => {
    try {
        const res = await fetch("/digitalstreamline/carrito/agregar_carrito.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id=${productId}`
        });
        const data = await res.json();

        if (data.success) {
            alert("Producto agregado al carrito");
            // Actualizar contador
            const cartCount = document.getElementById("cartCount");
            if (cartCount) cartCount.textContent = data.total;
        } else {
            alert("x" + data.msg);
        }
    } catch (err) {
        console.error(err);
        alert("Error al agregar al carrito");
    }
});
</script>


