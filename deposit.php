<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Ensure the user is logged in
ensureLoggedIn();

$result = ""; // For success/error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_id = $_POST['account_id'];
    $amount = (float) $_POST['amount'];
    $memo = $_POST['memo'] ?? '';

    // Validation
    if ($amount <= 0) {
        $result = flashMessage("Amount must be greater than zero.");
    } else {
        try {
            $db->beginTransaction();

            // Fetch account and validate ownership
            $stmt = $db->prepare("SELECT id, balance FROM accounts WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $account_id, ':user_id' => $_SESSION['id']]);
            $account = $stmt->fetch();

            if (!$account) {
                throw new Exception("Invalid account.");
            }

            // Fetch world account
            $stmt = $db->prepare("SELECT id FROM accounts WHERE account_number = '000000000000'");
            $stmt->execute();
            $world_account = $stmt->fetch();

            if (!$world_account) {
                throw new Exception("World account not found.");
            }

            $world_account_id = $world_account['id'];

            // Record transactions
            $expected_balance = $account['balance'] + $amount;
            $stmt = $db->prepare("INSERT INTO transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) 
                                  VALUES 
                                  (:src, :dest, :amount, 'deposit', :memo, :expected_total),
                                  (:src, :dest, :amount, 'withdraw', :memo, :expected_total)");
            $stmt->execute([
                ':src' => $world_account_id,
                ':dest' => $account_id,
                ':amount' => $amount,
                ':memo' => $memo,
                ':expected_total' => $expected_balance,
            ]);

            // Update account balance
            $stmt = $db->prepare("UPDATE accounts SET balance = :balance WHERE id = :id");
            $stmt->execute([
                ':balance' => $expected_balance,
                ':id' => $account_id,
            ]);

            $db->commit();
            $result = flashMessage("Deposit successful!", "Pass");
        } catch (Exception $e) {
            $db->rollBack();
            $result = flashMessage("Error: " . $e->getMessage());
        }
    }
}

// Fetch user's accounts
$stmt = $db->prepare("SELECT id, account_number, balance FROM accounts WHERE user_id = :user_id");
$stmt->execute([':user_id' => $_SESSION['id']]);
$accounts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Deposit</h1>
    <p class="text-center">Deposit money into your account.</p>

    <?php if ($result) echo $result; ?>

    <form action="" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="account_id" class="form-label">Select Account</label>
            <select name="account_id" id="account_id" class="form-select" required>
                <option value="">-- Select Account --</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= $account['id'] ?>">Account <?= $account['account_number'] ?> (Balance: $<?= $account['balance'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" min="1" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="memo" class="form-label">Memo (Optional)</label>
            <input type="text" name="memo" id="memo" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Deposit</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
