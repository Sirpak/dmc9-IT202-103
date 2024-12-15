<?php
//add our database connection script
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

//process the form
if(isset($_POST['signupBtn'])){
    //initialize an array to store any error message from the form
    $form_errors = array();

    //Form validation
    $required_fields = array('email', 'username', 'password');

    //call the function to check empty field and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_empty_fields($required_fields));

    //Fields the at requires checking for minimum length
    $fields_to_check_length = array('username' => 4, 'password' => 6);

    //call the function to check minimum required length and merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_min_length($fields_to_check_length));

    //email validation / merge the return data into form_error array
    $form_errors = array_merge($form_errors, check_email($_POST));

    //check if error array is empty, if yes process form data and insert record
    if(empty($form_errors)){
        //collect form data and store in variables
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        //hashing the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try{

            //create SQL insert statement
            $sqlInsert = "INSERT INTO users (username, email, password, join_date)
              VALUES (:username, :email, :password, now())";

            //use PDO prepared to sanitize data
            $statement = $db->prepare($sqlInsert);

            //add the data into the database
            $statement->execute(array(':username' => $username, ':email' => $email, ':password' => $hashed_password));

            //check if one new row was created
            if($statement->rowCount() == 1){
                $result = "<p style='padding:20px; border: 1px solid gray; color: green;'> Registration Successful</p>";
            }
        }catch (PDOException $ex){
            $result = "<p style='padding:20px; border: 1px solid gray; color: red;'> An error occurred: ".$ex->getMessage()."</p>";
        }
    }
    else{
        if(count($form_errors) == 1){
            $result = "<p style='color: red;'> There was 1 error in the form<br>";
        }else{
            $result = "<p style='color: red;'> There were " .count($form_errors). " errors in the form <br>";
        }
    }

}

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/resources/css/style.css">
    <link rel="stylesheet" href="/resources/css/custom.css">
    <title>Register Page</title>
</head>
<body>
<?php include 'partials/navbar.php'; ?>
    <!-- Page Content -->
    <div class="container">
<h2>User Authentication System </h2><hr>

<h3>Registration Form</h3>

<?php if(isset($result)) echo $result; ?>
<?php if(!empty($form_errors)) echo show_errors($form_errors); ?>
<form method="post" action="">
    <table>
        <tr><td>Email:</td> <td><input type="text" value="" name="email"></td></tr>
        <tr><td>Username:</td> <td><input type="text" value="" name="username"></td></tr>
        <tr><td>Password:</td> <td><input type="password" value="" name="password"></td></tr>
        <tr><td></td><td><input style="float: right;" type="submit" name="signupBtn" value="Signup"></td></tr>
    </table>
</form>
<p><a href="index.php">Back</a> </p>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>