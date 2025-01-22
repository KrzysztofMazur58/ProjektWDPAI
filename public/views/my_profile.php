<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Fitness Dashboard</title>
    <link rel="stylesheet" href="/public/styles/myprofile.css">
</head>
<body>
<div class="container">

    <div class="header">
        <h1>My Profile</h1>
    </div>

    <div class="content">
        <div class="form-group">
            <label for="first_name">First Name:</label>
            <p id="first_name"><?php echo htmlspecialchars($user['first_name']); ?></p>
        </div>

        <div class="form-group">
            <label for="last_name">Last Name:</label>
            <p id="last_name"><?php echo htmlspecialchars($user['last_name']); ?></p>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <p id="email"><?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <div class="form-group">
            <label for="height">Height (cm):</label>
            <p id="height"><?php echo htmlspecialchars($user['height']); ?></p>
        </div>

        <div class="form-group">
            <label for="weight">Weight (kg):</label>
            <p id="weight"><?php echo htmlspecialchars($user['weight']); ?></p>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <p id="gender"><?php echo htmlspecialchars($user['gender']); ?></p>
        </div>

        <div class="form-group">
            <label for="activity_level">Physical Activity Level:</label>
            <p id="activity_level"><?php echo htmlspecialchars($user['activity_level']); ?></p>
        </div>

        <div class="form-group">
            <label for="age">Age:</label>
            <p id="age"><?php echo htmlspecialchars($user['age']); ?></p>
        </div>

        <a href="/dashboard" class="btn">Back to Dashboard</a>
    </div>
</div>
</body>
</html>



