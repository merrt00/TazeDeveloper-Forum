document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("menu-toggle");
    const menuContent = document.getElementById("menu-content");

    // Menü butonuna tıklandığında menüyü aç/kapat
    menuToggle.addEventListener("click", function (event) {
        event.stopPropagation(); // Menü butonuna tıklandığında diğer tıklama olaylarını durdur
        menuContent.classList.toggle("active");
    });

    // Menünün dışına tıklandığında menüyü kapat
    document.addEventListener("click", function () {
        if (menuContent.classList.contains("active")) {
            menuContent.classList.remove("active");
        }
    });

    // Menü içindeki öğelere tıklandığında menüyü kapatma
    menuContent.addEventListener("click", function (event) {
        event.stopPropagation(); // Menü içindeki öğelere tıklandığında dış alan tıklama olayını durdur
    });
});
