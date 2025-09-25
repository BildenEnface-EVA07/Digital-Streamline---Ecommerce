<nav class="subnav">
  <div class="container subnav__inner">
    <button class="todo-btn" id="todoToggle" aria-haspopup="true" aria-expanded="false" aria-controls="megaMenu">
      <span class="hamburger" aria-hidden="true">☰</span> Todo
    </button>

    <!-- Aquí JS inyectará las categorías -->
    <ul class="subnav__links" id="categoryList" role="menubar" aria-label="Categorías">
      <li>Cargando categorías...</li>
    </ul>
  </div>

  <!-- Dropdown largo -->
  <div id="megaMenu" class="mega-dropdown" hidden>
    <div class="mega-dropdown__content" id="megaMenuContent">
      <!-- Aquí también se inyectarán subcategorías si quieres -->
    </div>
  </div>
</nav>

<script>
document.addEventListener("DOMContentLoaded", async () => {
  const list = document.getElementById("categoryList");
  const megaMenu = document.getElementById("megaMenuContent");

  try {
    const res = await fetch("/digitalstreamline/productos/categorias.php");
    const data = await res.json();

    if (data.success) {
      list.innerHTML = ""; // limpio "Cargando..."
      megaMenu.innerHTML = "";

      data.categorias.forEach(cat => {
        // enlace normal en la barra
        const li = document.createElement("li");
        li.innerHTML = `<a href="/digitalstreamline/categoria.php?cat=${cat.categoria_id}">
                          ${cat.nombre}
                        </a>`;
        list.appendChild(li);

        // sección dentro del mega menú
        const section = document.createElement("section");
        section.innerHTML = `
          <h3>${cat.nombre}</h3>
          <ul>
            <li><a href="/digitalstreamline/categoria.php?cat=${cat.categoria_id}">
              Ver ${cat.total_productos} productos
            </a></li>
          </ul>
        `;
        megaMenu.appendChild(section);
      });
    } else {
      list.innerHTML = "<li>Error al cargar categorías</li>";
    }
  } catch (err) {
    console.error("Error cargando categorías:", err);
    list.innerHTML = "<li>Error de conexión</li>";
  }
});
</script>
