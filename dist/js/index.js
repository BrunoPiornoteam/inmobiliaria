document.addEventListener("DOMContentLoaded", function () {
    const navItems = document.querySelectorAll(".nav-item");

    navItems.forEach((item) => {
        const button = item.querySelector(".nav-button");
        const submenu = item.querySelector(".submenu");

        if (button && submenu) {
            button.addEventListener("click", function () {
                item.classList.toggle("active");

                navItems.forEach((otherItem) => {
                    if (otherItem !== item) {
                        otherItem.classList.remove("active");
                    }
                });
            });
        }
    });

    document.addEventListener("click", function (event) {
        if (!event.target.closest(".nav-container")) {
            navItems.forEach((item) => item.classList.remove("active"));
        }
    });
});

document.getElementById('toggle-menu').addEventListener('click', function() {
    var header = document.querySelector('.header');
    header.classList.toggle('open');
});