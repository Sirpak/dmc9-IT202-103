<?php
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$result = ""; // Initialize result variable

if (isset($_POST['loginBtn'], $_POST['token'])) {
    $config = require __DIR__ . '/../config/app.php';
    $secret = $config['recaptcha']['secret'];

    // ReCaptcha request data
    $recaptcha = [
        'secret' => $secret,
        'response' => $_POST['token']
    ];

    // Verify ReCaptcha
    $response = verifyReCaptcha($recaptcha);

    if ($response && isset($response->success) && !$response->success) {
        $result = "<script type='text/javascript'>
                      swal('Error', 'ReCaptcha validation failed', 'error');
                   </script>";
    } elseif ($response && isset($response->hostname) && $response->hostname !== $_SERVER['HTTP_HOST']) {
        $result = "<script type='text/javascript'>
                      swal('Error', 'Request originates from a different server', 'error');
                   </script>";
    }
    // Validate CSRF Token
    elseif (validate_token($_POST['token'])) {
        // Initialize an array to hold form errors
        $form_errors = [];

        // Validate required fields
        $required_fields = ['username', 'password'];
        $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

        if (empty($form_errors)) {
            // Collect and sanitize form data
            $user = trim(htmlspecialchars($_POST['username']));
            $password = trim(htmlspecialchars($_POST['password']));
            $remember = isset($_POST['remember']) ? $_POST['remember'] : "";

            // Query to check if the user exists
            $sqlQuery = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $statement = $db->prepare($sqlQuery);

            if ($statement->execute([':username' => $user])) {
                if ($row = $statement->fetch()) {
                    $id = $row['id'];
                    $hashed_password = $row['password'];
                    $username = $row['username'];
                    $activated = $row['activated'];

                    // Check if account is activated
                    if ($activated === "0") {
                        if (checkDuplicateEntries('trash', 'user_id', $id, $db)) {
                            // Activate account
                            $db->exec("UPDATE users SET activated = '1' WHERE id = $id LIMIT 1");

                            // Remove info from trash table
                            $db->exec("DELETE FROM trash WHERE user_id = $id LIMIT 1");

                            // Log in the user
                            prepLogin($id, $username, $remember);
                        } else {
                            $result = flashMessage("Please activate your account");
                        }
                    } else {
                        // Validate the password
                        if (password_verify($password, $hashed_password)) {
                            // Log in the user
                            prepLogin($id, $username, $remember);
                        } else {
                            $result = flashMessage("You have entered an invalid password");
                        }
                    }
                } else {
                    $result = flashMessage("You have entered an invalid username");
                }
            } else {
                $result = flashMessage("Database query error: " . implode(" | ", $statement->errorInfo()));
            }
        } else {
            // Form validation errors
            $error_count = count($form_errors);
            $result = flashMessage("There " . ($error_count === 1 ? "was 1 error" : "were $error_count errors") . " in the form");
        }
    } else {
        // Invalid CSRF token
        $result = "<script type='text/javascript'>
                      swal('Error', 'This request originates from an unknown source, possible attack', 'error');
                   </script>";
    }
}

// Output the result for debugging or response to the client
if ($result) {
    echo $result;
}

/**
 * Verifies the Google reCAPTCHA response using cURL.
 *
 * @param array $recaptcha The ReCaptcha data containing the secret and token.
 * @return object|null The decoded JSON response from Google, or null on failure.
 */
function verifyReCaptcha($recaptcha)
{
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($recaptcha));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($http_status === 200) {
        return json_decode($response);
    }

    return null;
}
