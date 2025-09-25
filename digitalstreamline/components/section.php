<?php
if (!function_exists('renderSection')) {
    function renderSection(string $title, array $products, string $moreHref = '#'): void
    { ?>
        <section class="product-section">
            <div class="product-section__header">
                <h2><?= htmlspecialchars($title) ?></h2>
                <a href="<?= htmlspecialchars($moreHref) ?>" class="product-section__more" aria-label="Ver más">➜</a>
            </div>

            <div class="product-section__grid">
                <?php foreach ($products as $product) {
    echo '<article class="product-card">';
    echo '<a href="' . htmlspecialchars($product['url']) . '">';
    echo '<div class="product-card__img">';
    echo '<img src="' . htmlspecialchars($product['img'] ?: 'noimage.png') . '" alt="' . htmlspecialchars($product['title']) . '">';
    echo '</div>';
    echo '<h3 class="product-card__title">' . htmlspecialchars($product['title']) . '</h3>';
    echo '</a>';
    echo '</article>';
}
 ?>
            </div>
        </section>
    <?php }
}