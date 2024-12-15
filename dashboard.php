<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';

// Ensure the user is logged in
ensureLoggedIn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/resources/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Welcome to Your Dashboard</h1>
    <p class="text-center">Choose an option to get started:</p>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <a href="create_account.php" class="btn btn-primary w-100">Create Account</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="my_accounts.php" class="btn btn-secondary w-100">View My Accounts</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <a href="deposit.php" class="btn btn-success w-100">Deposit</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="withdraw.php" class="btn btn-danger w-100">Withdraw</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <a href="internal_transfer.php" class="btn btn-info w-100">Internal Transfer</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="external_transfer.php" class="btn btn-warning w-100">External Transfer</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <a href="transaction_history.php" class="btn btn-dark w-100">Transaction History</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="profile.php" class="btn btn-outline-primary w-100">Profile</a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-4">
            <a href="logout.php" class="btn btn-outline-danger w-100">Logout</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
