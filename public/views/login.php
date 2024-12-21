<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind&family=Jomhuria&family=Kaushan+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/styles/login.css">
</head>
<body>
<div class="container">

    <div class="left-section">
        <div class="logo">
            <i class="fa-solid fa-drumstick-bite"></i>
        </div>
        <h1>NutriTrack</h1>
    </div>

    <div class="right-section">

        <form action="/login" method="POST" class="login-form">
            <h2>Please, Log in</h2>

            <!-- Sekcja na komunikat błędu -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <input type="email" name="email" placeholder="Email" class="input-field">
            <input type="password" name="password" placeholder="Password" class="input-field">

            <button type="submit" class="continue-button">Continue</button>
            <p>or register</p>
            <button type="button" class="register-button" onclick="window.location.href='/register'">Register</button>
        </form>


    </div>

</div>

</body>
</html>
