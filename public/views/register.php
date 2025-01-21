<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind&family=Jomhuria&family=Kaushan+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/styles/register.css">
</head>
<body>
<div class="container">
    <div class="right-section">
        <form action="/register" method="POST" class="register-form">
            <h2>Create an Account</h2>

            <!-- Error message section -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <input type="text" name="first_name" placeholder="First Name" class="input-field" required>
            <input type="text" name="last_name" placeholder="Last Name" class="input-field" required>
            <input type="email" name="email" placeholder="Email" class="input-field" required>
            <input type="password" name="password" placeholder="Password" class="input-field" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" class="input-field" required>

            <button type="submit" class="continue-button">Register</button>
            <p>Already have an account?</p>
            <button type="button" class="register-button" onclick="window.location.href='/login'">Login</button>
        </form>
    </div>
</div>
</body>
</html>







