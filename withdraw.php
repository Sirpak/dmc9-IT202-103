<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Ensure the user is logged in
ensureLoggedIn();

// Initialize result message
$result = "";

// Fetch user accounts
$user_id = $_SESSION['id'];
$stmt = $db->prepare("SELECT id, account_number, balance FROM accounts WHERE user_id = :user_id AND account_type != 'world'");
$stmt->execute([':user_id' => $user_id]);
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = $_POST['account_id'];
    $amount = (float)$_POST['amount'];
    $memo = $_POST['memo'] ?? '';

    // Validate input
    if ($amount <= 0) {
        $result = flashMessage("Withdrawal amount must be greater than $0.");
    } else {
        try {
            $db->beginTransaction();

            // Fetch selected account
            $accountQuery = "SELECT id, balance FROM accounts WHERE id = :account_id AND user_id = :user_id FOR UPDATE";
            $accountStmt = $db->prepare($accountQuery);
            $accountStmt->execute([
                ':account_id' => $account_id,
                ':user_id' => $user_id
            ]);
            $account = $accountStmt->fetch(PDO::FETCH_ASSOC);

            if (!$account) {
                throw new Exception("Account not found or access denied.");
            }

            // Check if sufficient balance is available
            if ($account['balance'] < $amount) {
                throw new Exception("Insufficient funds in the account.");
            }

            // Fetch world account ID
            $worldAccountQuery = "SELECT id FROM accounts WHERE account_number = '000000000000' LIMIT 1";
            $worldAccountStmt = $db->prepare($worldAccountQuery);
            $worldAccountStmt->execute();
            $worldAccount = $worldAccountStmt->fetch(PDO::FETCH_ASSOC);

            if (!$worldAccount) {
                throw new Exception("World account not found.");
            }
            $world_account_id = $worldAccount['id'];

            // Record transaction
            $transactionQuery = "
                INSERT INTO transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) VALUES
                (:account_src, :account_dest, :amount, 'withdraw', :memo, :expected_total_src),
                (:account_src, :account_dest, :amount, 'deposit', :memo, :expected_total_dest)
            ";

            $newBalance = $account['balance'] - $amount;
            $transactionStmt = $db->prepare($transactionQuery);
            $transactionStmt->execute([
                ':account_src' => $account['id'],
                ':account_dest' => $world_account_id,
                ':amount' => -$amount,
                ':memo' => $memo,
                ':expected_total_src' => $newBalance,
                ':expected_total_dest' => $amount, // For world account
            ]);

            // Update account balance
            $updateQuery = "UPDATE accounts SET balance = :balance WHERE id = :account_id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([
                ':balance' => $newBalance,
                ':account_id' => $account['id']
            ]);

            $db->commit();
            $result = flashMessage("Withdrawal of $" . number_format($amount, 2) . " was successful!", "Pass");
        } catch (Exception $e) {
            $db->rollBack();
            $result = flashMessage("Error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/resources/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Withdraw Funds</h1>
    <?php if ($result) echo $result; ?>

    <?php if (!empty($accounts)): ?>
        <form action="" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="account_id" class="form-label">Select Account</label>
                <select name="account_id" id="account_id" class="form-select" required>
                    <option value="">-- Select Account --</option>
                    <?php foreach ($accounts as $account): ?>
                        <option value="<?= $account['id'] ?>">
                            <?= htmlspecialchars($account['account_number']) ?> (Balance: $<?= number_format($account['balance'], 2) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount to Withdraw</label>
                <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0.01" required>
            </div>
            <div class="mb-3">
                <label for="memo" class="form-label">Memo (Optional)</label>
                <input type="text" name="memo" id="memo" class="form-control">
            </div>
            <button type="submit" class="btn btn-danger w-100">Withdraw</button>
        </form>
    <?php else: ?>
        <p class="text-center">No accounts available for withdrawal.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
