<?php
// categoria.php
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Productos por Categoría</title>
  <link rel="stylesheet" href="/digitalstreamline/assets/css/styles.css">
  <style>
    /* Contenedor de productos */
    .productos-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    /* Tarjetas de producto */
    .producto-card {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
    }

    /* Efecto hover */
    .producto-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    /* Imagen del producto */
    .producto-card img {
      width: 100%;
      height: 230px;
      object-fit: cover;
    }

    /* Contenido */
    .producto-card h3 {
      font-size: 1.1rem;
      margin: 10px;
      color: #333;
    }

    .producto-card p {
      font-size: 0.95rem;
      margin: 5px 10px;
      color: #666;
    }

    .producto-card p strong {
      color: #000;
    }

    /* Link para que toda la tarjeta sea clickeable */
    .producto-card a {
      color: inherit;
      text-decoration: none;
      display: block;
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/components/header.php'; ?>
  <?php include __DIR__ . '/components/topnav.php'; ?>

  <main class="container">
    <h1 id="categoriaTitulo">Productos</h1>
    <div id="productosGrid" class="grid productos-grid"></div>
  </main>

  <script>
    // Obtener parámetro "cat" de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const categoriaId = urlParams.get("cat");

    const grid = document.getElementById("productosGrid");
    const titulo = document.getElementById("categoriaTitulo");

    async function cargarProductos() {
      if (!categoriaId) {
        titulo.textContent = "Categoría no especificada";
        return;
      }

      try {
        const res = await fetch(`/digitalstreamline/productos/listar.php?categoria=${categoriaId}`);
        const text = await res.text();

        try {
          const data = JSON.parse(text);
          if (data.success) {
            grid.innerHTML = "";

            if (data.productos.length === 0) {
              grid.innerHTML = "<p>No hay productos en esta categoría.</p>";
              return;
            }

            data.productos.forEach(prod => {
            const card = document.createElement("div");
            card.className = "producto-card";

            // Determinar la ruta correcta de la imagen
            let imgSrc = prod.imagen_url || 'noimage.png';
            if (!imgSrc.startsWith('http')) {
              imgSrc = `/digitalstreamline/uploads/${imgSrc}`;
            }

            card.innerHTML = `
              <a href="/digitalstreamline/producto.php?id=${prod.product_id}">
                <img src="${imgSrc}" alt="${prod.nombre}">
                <h3>${prod.nombre}</h3>
                <p>${prod.descripcion || ''}</p>
                <p><strong>L ${prod.precio}</strong></p>
              </a>
            `;
            grid.appendChild(card);
          });

          } else {
            grid.innerHTML = "<p>Error al cargar productos.</p>";
            console.error("API error:", data);
          }
        } catch (err) {
          console.error("Respuesta no JSON:", text);
          grid.innerHTML = "<p>Error inesperado al cargar productos.</p>";
        }
      } catch (err) {
        console.error("Fetch error:", err);
        grid.innerHTML = "<p>Error de conexión.</p>";
      }
    }

    cargarProductos();
  </script>
</body>
</html>

