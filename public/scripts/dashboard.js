document.getElementById("addMealBox").addEventListener("click", function () {
    document.getElementById("mealModal").style.display = "flex";
});

document.getElementById("settingsBox").addEventListener("click", function (event) {
    const menu = document.getElementById("settingsMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";

    event.stopPropagation();
});

window.onclick = function (event) {
    if (event.target == document.getElementById("mealModal")) {
        document.getElementById("mealModal").style.display = "none";
    }
};

window.addEventListener("click", function () {
    const menu = document.getElementById("settingsMenu");
    if (menu.style.display === "block") {
        menu.style.display = "none";
    }
});

const hamburgerMenu = document.querySelector('.hamburger-menu');
const icons = document.querySelector('.icons');

document.addEventListener('DOMContentLoaded', function () {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const icons = document.querySelector('.icons');

    hamburgerMenu.addEventListener('click', function(event) {
        event.stopPropagation();

        icons.classList.toggle('visible');
        hamburgerMenu.classList.toggle('active');
        hamburgerMenu.classList.toggle('hidden');
    });

    window.addEventListener('click', function(event) {
        if (!hamburgerMenu.contains(event.target) && !icons.contains(event.target)) {
            icons.classList.remove('visible');
            hamburgerMenu.classList.remove('active');
            hamburgerMenu.classList.remove('hidden');
        }
    });
});
