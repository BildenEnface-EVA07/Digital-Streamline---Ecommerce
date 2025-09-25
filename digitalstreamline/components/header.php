<header class="topbar">
    <div class="topbar__inner container">

        <!-- Bloque de logos -->
        <a class="topbar__logo topbar__logo--stacked" href="/digitalstreamline/index.php">
            <img src="/digitalstreamline/assets/img/DS.svg" alt="DS" class="logo-ds">
            <img src="/digitalstreamline/assets/img/DigitalStream.svg" alt="DigitalStream" class="logo-digitalstream">
        </a>

        <!-- Buscador -->
        <form class="topbar__search" action="#" method="get" role="search" aria-label="Buscar">
            <input class="topbar__search-input" name="q" type="search" placeholder="Buscar productos, marcas y más…"
                aria-label="Buscar">
        </form>

        <!-- Iconos -->
        <nav class="topbar__icons" aria-label="Acciones">
            <a class="icon-btn" href="/digitalstreamline/carrito.php" aria-label="Carrito">
                <img src="/digitalstreamline/assets/icons/cart.svg" alt="" width="24" height="24">
                 <span id="cartCount" class="cart-count">
                    <?= isset($_SESSION['carrito']) ? array_sum(array_column($_SESSION['carrito'], 'cantidad')) : 0 ?>
            </span>
            </a>
            <a class="icon-btn" href="#" aria-label="Perfil">
                <img src="/digitalstreamline/assets/icons/user.svg" alt="" width="24" height="24">
            </a>
        </nav>
    </div>
</header>