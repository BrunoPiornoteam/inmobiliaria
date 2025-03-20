document.addEventListener("DOMContentLoaded", function () {
    const navItems = document.querySelectorAll(".nav-item");

    // Recupera el ítem activo del almacenamiento
    const activeNavItem = localStorage.getItem("activeNavItem");

    navItems.forEach((item) => {
        const button = item.querySelector(".nav-button");
        const submenu = item.querySelector(".submenu");

        if (button && submenu) {
            // Si este ítem estaba activo antes, lo reactiva
            if (activeNavItem && item.dataset.id === activeNavItem) {
                item.classList.add("active");
            }

            button.addEventListener("click", function () {
                const isActive = item.classList.toggle("active");

                // Cierra los otros submenús
                navItems.forEach((otherItem) => {
                    if (otherItem !== item) {
                        otherItem.classList.remove("active");
                    }
                });

                // Guarda el estado del ítem activo en localStorage
                if (isActive) {
                    localStorage.setItem("activeNavItem", item.dataset.id);
                } else {
                    localStorage.removeItem("activeNavItem");
                }
            });
        }
    });

    // Cerrar submenús al hacer clic fuera
    document.addEventListener("click", function (event) {
        if (!event.target.closest(".nav-container")) {
            navItems.forEach((item) => item.classList.remove("active"));
            localStorage.removeItem("activeNavItem"); // Limpiar almacenamiento al cerrar todo
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    var header = document.querySelector(".header");
    var toggleMenu = document.getElementById("toggle-menu");

    if (localStorage.getItem("menuOpen") === "true") {
        header.classList.add("open");
    }

    toggleMenu.addEventListener("click", function () {
        header.classList.toggle("open");

        localStorage.setItem("menuOpen", header.classList.contains("open"));
    });
});