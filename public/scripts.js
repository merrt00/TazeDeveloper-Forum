document.addEventListener('click', function (event) {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach((menu) => {
        if (!menu.parentElement.contains(event.target)) {
            menu.classList.remove('show');
        }
    });

    const toggle = event.target.closest('.dropdown-toggle');
    if (toggle) {
        const menu = toggle.nextElementSibling;
        menu.classList.toggle('show');
    }
});

