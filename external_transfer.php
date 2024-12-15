<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Ensure the user is logged in
ensureLoggedIn();

$result = ""; // For success/error messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source_account = $_POST['source_account'];
    $dest_last_name = trim($_POST['dest_last_name']);
    $dest_account_last4 = trim($_POST['dest_account_last4']);
    $amount = (float) $_POST['amount'];
    $memo = $_POST['memo'] ?? '';

    // Validation
    if ($amount <= 0) {
        $result = flashMessage("Amount must be greater than zero.");
    } elseif (strlen($dest_account_last4) !== 4 || !is_numeric($dest_account_last4)) {
        $result = flashMessage("Destination account last 4 digits must be numeric and 4 digits long.");
    } else {
        try {
            $db->beginTransaction();

            // Validate source account
            $stmt = $db->prepare("SELECT id, balance FROM accounts WHERE id = :id AND user_id = :user_id");
            $stmt->execute([':id' => $source_account, ':user_id' => $_SESSION['id']]);
            $source = $stmt->fetch();

            if (!$source) {
                throw new Exception("Invalid source account.");
            }

            if ($source['balance'] < $amount) {
                throw new Exception("Insufficient funds in source account.");
            }

            // Validate destination account
            $stmt = $db->prepare("
                SELECT accounts.id, accounts.balance 
                FROM accounts 
                INNER JOIN users ON accounts.user_id = users.id 
                WHERE users.last_name = :last_name AND 
                      RIGHT(accounts.account_number, 4) = :last4
                LIMIT 1
            ");
            $stmt->execute([':last_name' => $dest_last_name, ':last4' => $dest_account_last4]);
            $destination = $stmt->fetch();

            if (!$destination) {
                throw new Exception("Destination account not found.");
            }

            // Record transactions
            $expected_source_balance = $source['balance'] - $amount;
            $expected_dest_balance = $destination['balance'] + $amount;

            $stmt = $db->prepare("INSERT INTO transactions (account_src, account_dest, balance_change, transaction_type, memo, expected_total) 
                                  VALUES 
                                  (:src, :dest, -:amount, 'ext-transfer', :memo, :expected_src_total),
                                  (:src, :dest, :amount, 'ext-transfer', :memo, :expected_dest_total)");
            $stmt->execute([
                ':src' => $source_account,
                ':dest' => $destination['id'],
                ':amount' => $amount,
                ':memo' => $memo,
                ':expected_src_total' => $expected_source_balance,
                ':expected_dest_total' => $expected_dest_balance,
            ]);

            // Update balances
            $stmt = $db->prepare("UPDATE accounts SET balance = :balance WHERE id = :id");
            $stmt->execute([':balance' => $expected_source_balance, ':id' => $source_account]);
            $stmt->execute([':balance' => $expected_dest_balance, ':id' => $destination['id']]);

            $db->commit();
            $result = flashMessage("External transfer successful!", "Pass");
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
    <title>External Transfer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">External Transfer</h1>
    <p class="text-center">Transfer money to another user's account.</p>

    <?php if ($result) echo $result; ?>

    <form action="" method="POST" class="mt-4">
        <div class="mb-3">
            <label for="source_account" class="form-label">Source Account</label>
            <select name="source_account" id="source_account" class="form-select" required>
                <option value="">-- Select Source Account --</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= $account['id'] ?>">Account <?= $account['account_number'] ?> (Balance: $<?= $account['balance'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="dest_last_name" class="form-label">Recipient's Last Name</label>
            <input type="text" name="dest_last_name" id="dest_last_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="dest_account_last4" class="form-label">Recipient's Account Last 4 Digits</label>
            <input type="text" name="dest_account_last4" id="dest_account_last4" class="form-control" maxlength="4" required>
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" name="amount" id="amount" class="form-control" min="1" step="0.01" required>
        </div>
        <div class="mb-3">
            <label for="memo" class="form-label">Memo (Optional)</label>
            <input type="text" name="memo" id="memo" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Transfer</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
