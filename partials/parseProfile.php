<?php
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

if (isset($_GET['u'])) {
    $username = $_GET['u'];

    $sqlQuery = "SELECT * FROM users WHERE username = :username";
    $statement = $db->prepare($sqlQuery);
    $statement->execute([':username' => $username]);

    while ($rs = $statement->fetch()) {
        $username = $rs['username'];
        $profile_picture = isset($rs['avatar']) ? $rs['avatar'] : 'uploads/default.jpg';
        $date_joined = date("M d, Y", strtotime($rs["join_date"]));
        $status = $rs['activated'] == 1 ? "Activated" : "Not Activated";
    }
} elseif ((isset($_SESSION['id']) || isset($_GET['user_identity'])) && !isset($_POST['updateProfileBtn'])) {
    if (isset($_GET['user_identity'])) {
        $url_encoded_id = $_GET['user_identity'];
        $decode_id = base64_decode($url_encoded_id);
        $user_id_array = explode("encodeuserid", $decode_id);
        $id = $user_id_array[1];
    } else {
        $id = $_SESSION['id'];
    }

    $sqlQuery = "SELECT * FROM users WHERE id = :id";
    $statement = $db->prepare($sqlQuery);
    $statement->execute([':id' => $id]);

    while ($rs = $statement->fetch()) {
        $username = $rs['username'];
        $email = $rs['email'];
        $profile_picture = isset($rs['avatar']) ? $rs['avatar'] : 'uploads/default.jpg';
        $date_joined = date("M d, Y", strtotime($rs["join_date"]));
    }

    $encode_id = base64_encode("encodeuserid{$id}");
} elseif (isset($_POST['updateProfileBtn'], $_POST['token'])) {
    if (validate_token($_POST['token'])) {
        // Initialize an array to store error messages from the form
        $form_errors = [];

        // Form validation
        $required_fields = ['email', 'username'];
        $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

        // Fields that require checking for minimum length
        $fields_to_check_length = ['username' => 4];
        $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

        // Email validation
        $form_errors = array_merge($form_errors, check_email($_POST));

        // Validate if the file has a valid extension
        $avatar = $_FILES['avatar']['name'] ?? null;
        if ($avatar) {
            $form_errors = array_merge($form_errors, isValidImage($avatar));
        }

        // Collect form data
        $email = $_POST['email'];
        $username = $_POST['username'];
        $hidden_id = $_POST['hidden_id'];

        $result = false;
        $query = "SELECT email FROM users WHERE email = :email AND id <> :id";
        $emailExistStatement = $db->prepare($query);
        $emailExistStatement->execute([':email' => $email, ':id' => $hidden_id]);

        if ($emailExistStatement->fetch()) {
            $result = flashMessage("Email is already used by another user");
        }

        if (empty($form_errors) && !$result) {
            try {
                $query = "SELECT avatar FROM users WHERE id = :id";
                $oldAvatarStatement = $db->prepare($query);
                $oldAvatarStatement->execute([':id' => $hidden_id]);

                $oldAvatar = $oldAvatarStatement->fetch()['avatar'] ?? null;

                // Update SQL query
                if ($avatar) {
                    $avatar_path = uploadAvatar($username);
                    if (!$avatar_path) {
                        $avatar_path = "uploads/default.jpg";
                    }

                    $sqlUpdate = "UPDATE users SET username = :username, email = :email, avatar = :avatar WHERE id = :id";
                    $statement = $db->prepare($sqlUpdate);
                    $statement->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':avatar' => $avatar_path,
                        ':id' => $hidden_id,
                    ]);

                    if ($oldAvatar) {
                        unlink($oldAvatar);
                    }
                } else {
                    $sqlUpdate = "UPDATE users SET username = :username, email = :email WHERE id = :id";
                    $statement = $db->prepare($sqlUpdate);
                    $statement->execute([
                        ':username' => $username,
                        ':email' => $email,
                        ':id' => $hidden_id,
                    ]);
                }

                // Check if a new row was updated
                if ($statement->rowCount() === 1) {
                    $result = "<script type=\"text/javascript\">
                        swal({title:\"Updated!\", text:\"Profile Update Successfully.\", type:\"success\"}, 
                            function() { window.location.replace(window.location.href); });
                    </script>";
                } else {
                    $result = "<script type=\"text/javascript\">
                        swal({title:\"Nothing Happened\", text:\"You have not made any changes.\"}, 
                            function() { window.location.replace(window.location.href); });
                    </script>";
                }
            } catch (PDOException $ex) {
                $result = flashMessage("An error occurred: " . $ex->getMessage());
            }
        } else {
            if (!$result) {
                $error_count = count($form_errors);
                $result = flashMessage("There " . ($error_count === 1 ? "was 1 error" : "were $error_count errors") . " in the form.<br>");
            }
        }
    } else {
        $result = "<script type='text/javascript'>
            swal('Error', 'This request originates from an unknown source, possible attack', 'error');
        </script>";
    }
}
?>
