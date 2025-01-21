<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind&family=Jomhuria&family=Kaushan+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/styles/login.css">
</head>
<body>
<div class="container">
    <form action="/user_details" method="POST" class="user-details-form">
        <h2>Complete your profile</h2>

        <!-- Inne pola formularza -->
        <select name="gender" class="input-field" required>
            <option value="" disabled selected>Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>

        <input type="number" name="height" placeholder="Height (cm)" class="input-field" required>
        <input type="number" name="weight" placeholder="Weight (kg)" class="input-field" required>
        <input type="number" name="age" placeholder="Age" class="input-field" required>

        <select name="activity_level" class="input-field" required>
            <option value="" disabled selected>Activity Level</option>
            <option value="1">Sedentary</option>
            <option value="2">Light Activity</option>
            <option value="3">Moderate Activity</option>
            <option value="4">Active</option>
            <option value="5">Very Active</option>
        </select>

        <button type="submit" class="continue-button">Continue</button>
    </form>
</div>
</body>
</html>

