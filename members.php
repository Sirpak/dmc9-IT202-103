<?php
$page_title = "User Authentication - Members";
include_once 'partials/headers.php';
include_once 'partials/parseMembers.php';
include_once 'partials/navbar.php';

// Ensure $members is defined to avoid errors
$members = $members ?? [];
?>


<head lang="en">
    <meta charset="UTF-8">
    <title>Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/resources/css/style.css">
    <link rel="stylesheet" href="/resources/css/custom.css">
</head>

<div class="container">
    <div>
        <h1>Login System Members</h1>
        <section class="col col-lg-12">
            <?php if (count($members) > 0): ?>
                <?php foreach ($members as $member): ?>
                    <div class="row col-lg-4" style="margin-bottom: 10px;">
                        <div class="media">
                            <div class="media-left">
                                <a href="public_profile.php?u=<?= htmlspecialchars($member['username']) ?>">
                                    <img src="<?= !empty($member['avatar']) ? $member['avatar'] : 'uploads/default.jpg' ?>" class="media-object" style="width:60px">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">Username:
                                    <a href="public_profile.php?u=<?= htmlspecialchars($member['username']) ?>">
                                        <?= htmlspecialchars($member['username']) ?>
                                    </a>
                                </h4>
                                <p>Join Date: <?= (new DateTime($member['join_date']))->format('M d, Y') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No member found</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php include_once 'partials/footers.php'; ?>
</body>
</html>
