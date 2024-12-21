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
    <link rel="stylesheet" href="/public/styles/dashboard.css"><!-- Załączenie osobnego pliku CSS -->
</head>
<body>
<div class="container">
    <div class="header">
        <h2><?= isset($email) ? $email : "" ?></h2>

        <div class="icons">
            <div class="icon-box">
                <!-- Przycisk plus, który otworzy modal -->
                <i class="fa-solid fa-plus" id="openModal"></i>
            </div>
            <div class="icon-box">
                <i class="fa-solid fa-gear"></i>
            </div>
            <div class="icon-box">
                <i class="fa-solid fa-door-open"></i>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="chart1">
            <p>Daily calories</p>
            <p>1784 kcal</p>
        </div>
        <div class="chart2"></div>
        <div class="chart3">CHART 3 !!!</div>
    </div>
</div>

<!-- Modal - formularz dodawania posiłku -->
<div id="mealModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            Add Meal
        </div>

        <!-- Dodany div dla inputa i przycisku -->
        <div class="modal-body">
            <form action="/dashboard" method="POST">
                <input type="text" name="meal_name" placeholder="Meal Name" required>
                <button type="submit">Confirm</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Otwórz modal po kliknięciu ikony plusa
    document.getElementById("openModal").addEventListener("click", function() {
        document.getElementById("mealModal").style.display = "flex";
    });

    // Zamknij modal jeśli klikniesz poza modalem
    window.onclick = function(event) {
        if (event.target == document.getElementById("mealModal")) {
            document.getElementById("mealModal").style.display = "none";
        }
    }
</script>
</body>
</html>


