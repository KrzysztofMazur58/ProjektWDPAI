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
    <div class="form-section">
        <form action="/user_details" method="POST" class="user-details-form">
            <h2>Tell us about you</h2>

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
                <option value="sedentary">Sedentary</option>
                <option value="light">Light Activity</option>
                <option value="moderate">Moderate Activity</option>
                <option value="active">Active</option>
                <option value="very_active">Very Active</option>
            </select>

            <button type="submit" class="continue-button">Continue</button>
        </form>
    </div>
</div>
</body>
</html>
