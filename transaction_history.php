<?php
include_once 'partials/headers.php';
include_once 'resource/guard.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

// Ensure the user is logged in
ensureLoggedIn();

// Fetch the user ID from the session
$user_id = $_SESSION['id'];

// Pagination settings
$limit = 10; // Number of transactions per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch the user's accounts
$accountQuery = "SELECT id, account_number FROM accounts WHERE user_id = :user_id";
$accountStmt = $db->prepare($accountQuery);
$accountStmt->execute([':user_id' => $user_id]);
$accounts = $accountStmt->fetchAll(PDO::FETCH_ASSOC);

// Extract account IDs for transactions
$account_ids = array_column($accounts, 'id');

// If the user has no accounts, display a message
if (empty($account_ids)) {
    echo flashMessage("No accounts found for this user.", "Fail");
    include_once 'partials/footers.php';
    exit;
}

// Fetch transaction history for the user's accounts
$transactionQuery = "
    SELECT t.id, t.account_src, t.account_dest, t.balance_change, t.transaction_type, 
           t.memo, t.expected_total, t.created, a_src.account_number AS src_account_number, 
           a_dest.account_number AS dest_account_number
    FROM transactions AS t
    JOIN accounts AS a_src ON t.account_src = a_src.id
    JOIN accounts AS a_dest ON t.account_dest = a_dest.id
    WHERE t.account_src IN (" . implode(',', $account_ids) . ")
       OR t.account_dest IN (" . implode(',', $account_ids) . ")
    ORDER BY t.created DESC
    LIMIT :limit OFFSET :offset
";
$transactionStmt = $db->prepare($transactionQuery);
$transactionStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$transactionStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$transactionStmt->execute();
$transactions = $transactionStmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total number of transactions for pagination
$countQuery = "
    SELECT COUNT(*) 
    FROM transactions AS t
    WHERE t.account_src IN (" . implode(',', $account_ids) . ")
       OR t.account_dest IN (" . implode(',', $account_ids) . ")
";
$countStmt = $db->prepare($countQuery);
$countStmt->execute();
$totalTransactions = $countStmt->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalTransactions / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/resources/css/style.css">
</head>
<body>
<?php include 'partials/navbar.php'; ?>

<div class="container mt-5">
    <h1 class="text-center">Transaction History</h1>

    <?php if (empty($transactions)): ?>
        <p class="text-center">No transactions found.</p>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Source Account</th>
                    <th>Destination Account</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Memo</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['src_account_number']) ?></td>
                        <td><?= htmlspecialchars($transaction['dest_account_number']) ?></td>
                        <td><?= htmlspecialchars(number_format($transaction['balance_change'], 2)) ?></td>
                        <td><?= htmlspecialchars(ucwords(str_replace('-', ' ', $transaction['transaction_type']))) ?></td>
                        <td><?= htmlspecialchars($transaction['memo']) ?></td>
                        <td><?= htmlspecialchars(date("F j, Y, g:i a", strtotime($transaction['created']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
