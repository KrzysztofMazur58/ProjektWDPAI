<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Users List</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/public/styles/admin_dashboard.css">

</head>
<body>

<a href="/dashboard" class="back-btn">
    <i class="fas fa-arrow-left"></i>
</a>

<div class="users-list">
    <?php foreach ($users as $user): ?>
        <div class="user-card">
            <h3><?= htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) ?></h3>
            <p>Email: <?= htmlspecialchars($user['email']) ?></p>
            <p>Dołączył: <?= date('d-m-Y H:i', strtotime($user['joined_at'])) ?></p>

            <form action="/delete_user" method="POST" class="delete-form">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                <button type="submit" class="delete-btn">
                    <i class="fas fa-trash-alt"></i> Usuń
                </button>
            </form>

        </div>
    <?php endforeach; ?>
</div>
</body>
</html>
