<?php
$page_title = "User Authentication - Profile";
include_once 'partials/headers.php';
include_once 'partials/parseProfile.php';
include_once 'partials/navbar.php'; // Include the navbar for consistent menu structure
?>

<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/resources/css/style.css">
    <link rel="stylesheet" href="/resources/css/custom.css">
</head>
<body>

<div class="container mt-4">
    <div>
        <h1>Profile</h1>
        <?php if (!isset($_SESSION['username'])): ?>
            <p class="lead">You are not authorized to view this page. <a href="login.php">Login</a>
                Not yet a member? <a href="signup.php">Signup</a></p>
        <?php else: ?>
            <section class="col col-lg-7">
                <div class="row mb-3">
                    <div class="col-lg-3">
                        <!-- Ensure the image is responsive -->
                        <img src="<?php echo isset($profile_picture) ? $profile_picture : 'uploads/default.jpg'; ?>" 
                             class="img-fluid img-thumbnail" alt="Profile Picture" />
                    </div>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th style="width: 20%;">Username:</th>
                        <td><?php echo isset($username) ? $username : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?php echo isset($email) ? $email : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <th>Date Joined:</th>
                        <td><?php echo isset($date_joined) ? $date_joined : 'N/A'; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <a class="btn btn-primary" href="edit-profile.php?user_identity=<?php echo isset($encode_id) ? $encode_id : ''; ?>">
                                Edit Profile
                            </a>
                            <a class="btn btn-secondary" href="update-password.php?user_identity=<?php echo isset($encode_id) ? $encode_id : ''; ?>">
                                Change Password
                            </a>
                            <a class="btn btn-warning text-danger" href="deactivate-account.php?user_identity=<?php echo isset($encode_id) ? $encode_id : ''; ?>">
                                Deactivate Account
                            </a>
                        </td>
                    </tr>
                </table>
            </section>
        <?php endif; ?>
    </div>
</div>

<!-- Include footer -->
<?php include_once 'partials/footers.php'; ?>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
