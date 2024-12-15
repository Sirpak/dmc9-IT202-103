<?php
include_once 'resource/session.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

$result = ""; // Initialize result variable

if (isset($_POST['loginBtn'])) {
    // Array to hold errors
    $form_errors = [];

    // Validate required fields
    $required_fields = ['username', 'password'];
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    if (empty($form_errors)) {
        // Collect and sanitize form data
        $user = trim(htmlspecialchars($_POST['username']));
        $password = trim(htmlspecialchars($_POST['password']));

        // Check if user exists in the database
        $sqlQuery = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $statement = $db->prepare($sqlQuery);
        $statement->execute([':username' => $user]);

        if ($row = $statement->fetch()) {
            $id = $row['id'];
            $hashed_password = $row['password'];
            $username = $row['username'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit;
            } else {
                $result = "<p style='padding: 20px; color: red; border: 1px solid gray;'>Invalid username or password.</p>";
            }
        } else {
            $result = "<p style='padding: 20px; color: red; border: 1px solid gray;'>Invalid username or password.</p>";
        }
    } else {
        $error_count = count($form_errors);
        $result = "<p style='color: red;'>There " . ($error_count === 1 ? "was 1 error" : "were $error_count errors") . " in the form.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/resources/css/style.css">
    <link rel="stylesheet" href="/resources/css/custom.css">
    <!-- ReCaptcha -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LevI5sqAAAAAPC_NjuqO-d0cy0uvUc9_lYuk4Pb"></script>
    <script>
        grecaptcha.enterprise.ready(() => {
            grecaptcha.enterprise.execute('6LevI5sqAAAAAPC_NjuqO-d0cy0uvUc9_lYuk4Pb', { action: 'LOGIN' })
                .then(token => {
                    document.getElementById('g-recaptcha-response').value = token;
                });
        });
    </script>
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container">
    <h2>DC Bank</h2>
    <hr>
    <h4>
        When you go to "sign-up" you will be prompted to enter a username. This username is what you must use to log in.
        If you have forgotten your password, please select "Forgot Password" and reset it.
    </h4>
    <h3>Login Form</h3>

    <?php if (isset($result)) echo $result; ?>
    <?php if (!empty($form_errors)) echo show_errors($form_errors); ?>

    <form method="post" action="">
        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
        <table class="table">
            <tr>
                <td>Username:</td>
                <td><input type="text" name="username" class="form-control" placeholder="Enter your username"></td>
            </tr>
            <tr>
                <td>Password:</td>
                <td><input type="password" name="password" class="form-control" placeholder="Enter your password"></td>
            </tr>
            <tr>
                <td><a href="forgot_password.php">Forgot Password?</a></td>
                <td>
                    <button type="submit" name="loginBtn" class="btn btn-primary float-end">Sign In</button>
                </td>
            </tr>
        </table>
    </form>
    <p><a href="index.php">Back</a></p>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
