<?php
$page_title = "User Authentication - Profile";
include_once 'partials/headers.php';
include_once 'partials/navbar.php';
include_once 'resource/Database.php';
include_once 'resource/utilities.php';

$username = isset($_GET['u']) ? trim($_GET['u']) : null;

if ($username) {
    // Fetch user details based on username
    $stmt = $db->prepare("SELECT username, email, status, profile_picture, date_joined, is_public FROM users WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        if (!$user['is_public']) {
            $error = "This profile is private.";
        } else {
            // Assign fetched data to variables
            $profile_picture = $user['profile_picture'] ?? 'default-avatar.png';
            $status = $user['status'] ?? 'No status set';
            $date_joined = date('F j, Y', strtotime($user['date_joined']));
        }
    } else {
        $error = "User not found.";
    }
} else {
    $error = "Invalid request.";
}
?>

<div class="container">
    <?php if (isset($error)): ?>
        <h1>Error</h1>
        <p><?= $error ?></p>
    <?php else: ?>
        <h1><?= htmlspecialchars($username) ?>'s Profile</h1>
        <section class="col col-lg-7">
            <div class="row col-lg-3" style="margin-bottom: 10px;">
                <img src="uploads/avatars/<?= htmlspecialchars($profile_picture) ?>" class="img img-rounded" width="200" />
            </div>
            <table class="table table-bordered table-condensed">
                <tr>
                    <th style="width: 20%;">Username:</th>
                    <td><?= htmlspecialchars($username) ?></td>
                </tr>
                <tr>
                    <th>Status:</th>
                    <td><?= htmlspecialchars($status) ?></td>
                </tr>
                <tr>
                    <th>Date Joined:</th>
                    <td><?= htmlspecialchars($date_joined) ?></td>
                </tr>
            </table>
        </section>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="/resources/css/style.css">
<link rel="stylesheet" href="/resources/css/custom.css">
<?php include_once 'partials/footers.php'; ?>
