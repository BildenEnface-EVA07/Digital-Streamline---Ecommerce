document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("todoToggle");
  const menu = document.getElementById("megaMenu");

  const openMenu = () => {
    // centra la flechita bajo el botón
    const rect = toggle.getBoundingClientRect();
    const left = rect.left + rect.width / 2 + window.scrollX;
    menu.style.setProperty("--menu-offset", left + "px");

    menu.hidden = false;
    toggle.setAttribute("aria-expanded", "true");
  };

  const closeMenu = () => {
    menu.hidden = true;
    toggle.setAttribute("aria-expanded", "false");
  };

  toggle?.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation(); // evita cierre inmediato
    if (menu.hidden) openMenu();
    else closeMenu();
  });

  // no cerrar al hacer click dentro del menú
  menu.addEventListener("click", (e) => e.stopPropagation());

  // cerrar al click fuera
  document.addEventListener("click", () => {
    if (!menu.hidden) closeMenu();
  });

  // cerrar con ESC
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && !menu.hidden) closeMenu();
  });

  // realinea flechita al redimensionar
  window.addEventListener("resize", () => {
    if (!menu.hidden) {
      const rect = toggle.getBoundingClientRect();
      const left = rect.left + rect.width / 2 + window.scrollX;
      menu.style.setProperty("--menu-offset", left + "px");
    }
  });
});

// Flecha ➜: desplaza 1 “pantalla” de la pista
document.addEventListener("click", (e) => {
  const btn = e.target.closest(".product-section__arrow");
  if (!btn) return;
  const track = document.querySelector(btn.dataset.target);
  if (!track) return;

  const by = track.clientWidth; // desplaza por ancho visible (4 ítems aprox)
  track.scrollBy({ left: by, behavior: "smooth" });
});
