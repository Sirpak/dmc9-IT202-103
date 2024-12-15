<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';

// Ensure the user is logged in
ensureLoggedIn();

// Fetch user's accounts
$stmt = $db->prepare("SELECT account_number, account_type, balance, created FROM accounts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['id']]);
$accounts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">My Accounts</h1>
    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>Account Number</th>
                <th>Account Type</th>
                <th>Balance</th>
                <th>Date Opened</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?= $account['account_number'] ?></td>
                    <td><?= ucfirst($account['account_type']) ?></td>
                    <td>$<?= $account['balance'] ?></td>
                    <td><?= $account['created'] ?></td>
                    <td>
                        <a href="transaction_history.php?account=<?= $account['account_number'] ?>" class="btn btn-info btn-sm">View Transactions</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
