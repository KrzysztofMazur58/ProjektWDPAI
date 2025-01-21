<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Dashboard - Add Meal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/styles/add_meal.css">
    <link rel="stylesheet" href="/public/styles/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

</head>
<body>
<div class="container">

    <div class="header">
        <h1>Hi, <?php echo htmlspecialchars($name); ?></h1>

        <div class="hamburger-menu">
            <div></div>
            <div></div>
            <div></div>
        </div>

        <div class="icons">
            <div class="icon-box" id="addMealBox">
                <i class="fa-solid fa-plus" id="openModal"></i>
            </div>
            <div class="icon-box" id="settingsBox">
                <i class="fa-solid fa-gear" id="settingsIcon"></i>
                <div class="dropdown-menu" id="settingsMenu">
                    <div class="personal-data-section">
                        <h2>Settings</h2>
                        <a href="/user_details" class="change-data-link">Change Personal Data</a>

                        <?php if ($this->isAdmin()): ?>
                            <a href="/admin_dashboard" class="change-data-link">Change to Admin</a>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <div class="icon-box" id="logoutBox">
                <form action="/logout" method="POST">
                    <button type="submit">
                        <i class="fa-solid fa-door-open" title="Logout"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="chart1">
            <h2>Daily calories</h2>
            <p id="remainingCalories"></p>
            <div class="chart-item">
                <canvas id="calorieChart"></canvas>
            </div>
        </div>
        <div class="chart2">
            <div class="chart-item">
                <h3>Protein</h3>
                <canvas id="proteinChart"></canvas>
            </div>
            <div class="chart-item">
                <h3>Fat</h3>
                <canvas id="fatChart"></canvas>
            </div>
            <div class="chart-item">
                <h3>Carbohydrates</h3>
                <canvas id="carbsChart"></canvas>
            </div>
        </div>

        <div class="chart3">
            <h3>Personalized Nutrition Tips</h3>
            <div id="nutritionTip"></div>
            <div id="nutritionBenefit"></div>
            <h4>Macronutrient Advice</h4>
            <div id="macronutrientAdvice"></div>
        </div>
    </div>
</div>

<!-- Modal - formularz dodawania posiłku -->
<div id="mealModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            Add Meal
        </div>

        <div class="modal-body">
            <div id="addedMealMessage"></div>
            <form id="addMealForm">
                <input type="text" id="productQuery" placeholder="Meal Name" required>
                <input type="number" id="mealWeight" name="mealWeight" placeholder="Meal Weight (g)" required>
                <button type="button" id="confirmButton">Confirm</button>
            </form>

        </div>
    </div>
</div>


<script>
    // Kliknięcie na cały box otwiera modal
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

    // Find the hamburger menu and the icons
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const icons = document.querySelector('.icons');

    // Toggle visibility of icons when clicking hamburger menu
    document.addEventListener('DOMContentLoaded', function () {
        const hamburgerMenu = document.querySelector('.hamburger-menu');
        const icons = document.querySelector('.icons');

        // Dodajemy nasłuchiwanie na kliknięcie w hamburgera
        hamburgerMenu.addEventListener('click', function(event) {
            event.stopPropagation();  // Zapobieganie propagacji zdarzenia

            // Toggle klasy 'visible' dla ikon i 'active' dla hamburgera
            icons.classList.toggle('visible');
            hamburgerMenu.classList.toggle('active');
            hamburgerMenu.classList.toggle('hidden');  // Ukrycie hamburgera po kliknięciu
        });

        // Ukrywanie menu, jeśli klikniesz poza nim
        window.addEventListener('click', function(event) {
            if (!hamburgerMenu.contains(event.target) && !icons.contains(event.target)) {
                icons.classList.remove('visible');
                hamburgerMenu.classList.remove('active');
                hamburgerMenu.classList.remove('hidden');  // Przywrócenie hamburgera, jeśli klikniesz poza menu
            }
        });
    });


</script>

<script src="public/scripts/charts.js"></script>
<script src="public/scripts/name.js"></script>
</body>
</html>



