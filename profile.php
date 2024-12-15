<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Ensure the user is logged in
ensureLoggedIn();

// Fetch user details
$user_id = $_SESSION['id'];
$stmt = $db->prepare("SELECT username, email, join_date FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo flashMessage("User not found.", "Fail");
    exit;
}

$username = $user['username'];
$email = $user['email'];
$join_date = date("F j, Y", strtotime($user['join_date']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/resources/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">My Profile</h1>
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body">
            <h5 class="card-title">Profile Information</h5>
            <p class="card-text"><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
            <p class="card-text"><strong>Date Joined:</strong> <?= htmlspecialchars($join_date) ?></p>
            <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
