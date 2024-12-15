<?php
$current_page = basename($_SERVER['PHP_SELF']); // Get the current page name
?>


<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">DC Bank</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Home</a>
                </li>
                <?php if (isset($_SESSION['username'])): ?>
                    <!-- Dashboard link for logged-in users -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'myprofile.php') ? 'active' : ''; ?>" href="myprofile.php">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'members.php') ? 'active' : ''; ?>" href="members.php">Members</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <!-- Login and Signup links for guests -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'login.php') ? 'active' : ''; ?>" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'signup.php') ? 'active' : ''; ?>" href="signup.php">Signup</a>
                    </li>
                <?php endif; ?>
            </ul>
            <!-- Add the logo -->
            <a href="index.php" class="navbar-logo">
                <img src="/registersystem/auth/uploads/bank_logo.jpg" alt="Bank Logo" style="height: 40px; width: auto;">
            </a>
        </div>
    </div>
</nav>

