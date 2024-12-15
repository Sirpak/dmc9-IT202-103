<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Ensure the user is logged in
ensureLoggedIn();

$result = ""; // Variable to hold success or error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize form input
    $account_type = $_POST['account_type'];
    $initial_deposit = (float) $_POST['initial_deposit'];

    // Validate form input
    if (!in_array($account_type, ['checking', 'savings', 'loan', 'brokerage_cash', 'business_checking', 'business_savings'])) {
        $result = flashMessage("Invalid account type selected.");
    } elseif ($initial_deposit < 5 && !in_array($account_type, ['loan'])) {
        $result = flashMessage("Minimum deposit for non-loan accounts is $5.");
    } else {
        try {
            $db->beginTransaction();

            // Fetch the world account ID
            $stmt = $db->prepare("SELECT id FROM accounts WHERE account_number = :account_number LIMIT 1");
            $stmt->execute([':account_number' => '000000000000']);
            $world_account = $stmt->fetch();

            if (!$world_account) {
                throw new Exception("World account not found.");
            }

            $world_account_id = $world_account['id'];

            // Generate unique 12-digit account number
            do {
                $account_number = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
                $stmt = $db->prepare("SELECT COUNT(*) FROM accounts WHERE account_number = :account_number");
                $stmt->execute([':account_number' => $account_number]);
            } while ($stmt->fetchColumn() > 0);

            // Create the account
            $stmt = $db->prepare("INSERT INTO accounts (account_number, user_id, balance, account_type) VALUES (:account_number, :user_id, 0, :account_type)");
            $stmt->execute([
                ':account_number' => $account_number,
                ':user_id' => $_SESSION['id'],
                ':account_type' => $account_type,
            ]);

            $account_id = $db->lastInsertId();

            if ($account_type !== 'loan') {
                // Create the transaction pair for initial deposit
                $stmt = $db->prepare("INSERT INTO transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) VALUES 
                    (:src, :dest, :balance_change, 'deposit', 'Initial Deposit', :expected_total),
                    (:src, :dest, :balance_change, 'withdraw', 'World Account Adjustment', :expected_total)");
                
                $stmt->execute([
                    ':src' => $world_account_id,
                    ':dest' => $account_id,
                    ':balance_change' => -$initial_deposit,
                    ':expected_total' => 0, // Adjusted balances after transactions
                ]);

                // Update the account balance
                $stmt = $db->prepare("UPDATE accounts SET balance = :balance WHERE id = :id");
                $stmt->execute([
                    ':balance' => $initial_deposit,
                    ':id' => $account_id,
                ]);
            }

            $db->commit();

            $result = flashMessage("Account created successfully! Your account number is $account_number.", "Pass");
        } catch (Exception $e) {
            $db->rollBack();
            $result = flashMessage("An error occurred: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/resources/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Create a New Account</h1>
    <p class="text-center">Fill out the form below to create an account.</p>

    <?php if ($result) echo $result; ?>

    <form action="" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="account_type" class="form-label">Account Type</label>
            <select name="account_type" id="account_type" class="form-select" required>
                <option value="">-- Select Account Type --</option>
                <option value="checking">Checking</option>
                <option value="savings">Savings</option>
                <option value="loan">Loan</option>
                <option value="brokerage_cash">Brokerage Cash</option>
                <option value="business_checking">Business Checking</option>
                <option value="business_savings">Business Savings</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="initial_deposit" class="form-label">Initial Deposit ($5 Minimum for Non-Loan Accounts)</label>
            <input type="number" name="initial_deposit" id="initial_deposit" class="form-control" min="5" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Create Account</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9Gkc"></script>
</body>
</html>
