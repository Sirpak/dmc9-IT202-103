<?php
$page_title = "DC Bank - Edit Profile";
include_once 'partials/headers.php';
include_once 'resource/guard.php'; // Ensure guard.php includes ensureLoggedIn()
include_once 'resource/Database.php';
include_once 'resource/utilities.php';
// include_once 'partials/navbar.php'; // was a double navbar

// Ensure the user is logged in
ensureLoggedIn();

$result = ""; // Variable for success or error messages
$form_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $hidden_id = $_POST['hidden_id'];
    $avatar = $_FILES['avatar'] ?? null;

    if (empty($email) || empty($username)) {
        $form_errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $form_errors[] = "Invalid email format.";
    } else {
        try {
            // Check for duplicate username/email
            $stmt = $db->prepare("SELECT id FROM users WHERE (email = :email OR username = :username) AND id != :id");
            $stmt->execute([
                ':email' => $email,
                ':username' => $username,
                ':id' => $hidden_id,
            ]);

            if ($stmt->fetch()) {
                $form_errors[] = "Email or username already in use.";
            } else {
                // Update user details
                $stmt = $db->prepare("UPDATE users SET email = :email, username = :username WHERE id = :id");
                $stmt->execute([
                    ':email' => $email,
                    ':username' => $username,
                    ':id' => $hidden_id,
                ]);

                // Handle avatar upload
                if ($avatar && $avatar['size'] > 0) {
                    $avatar_path = "uploads/avatars/" . basename($avatar['name']);
                    if (move_uploaded_file($avatar['tmp_name'], $avatar_path)) {
                        $stmt = $db->prepare("UPDATE users SET profile_picture = :profile_picture WHERE id = :id");
                        $stmt->execute([
                            ':profile_picture' => $avatar_path,
                            ':id' => $hidden_id,
                        ]);
                    } else {
                        $form_errors[] = "Failed to upload avatar.";
                    }
                }

                $result = flashMessage("Profile updated successfully.", "Pass");
            }
        } catch (Exception $e) {
            $form_errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/resources/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Edit Profile</h1>
    <?= $result ?>
    <?= !empty($form_errors) ? show_errors($form_errors) : '' ?>

    <form method="post" action="" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label for="emailField" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" id="emailField" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="usernameField" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="usernameField" value="<?= htmlspecialchars($username ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="fileField" class="form-label">Avatar</label>
            <input type="file" name="avatar" class="form-control" id="fileField">
        </div>
        <input type="hidden" name="hidden_id" value="<?= htmlspecialchars($_SESSION['id']) ?>">
        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

