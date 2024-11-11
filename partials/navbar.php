<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container-fluid">
  <nav class="navbar navbar-inverse">
    <div class="container-fluid">
      <ul class="nav navbar-nav">
        <li><a id="len1" class="hoverable" href="index.php">Home</a></li>
        <?php if (isset($_SESSION['username'])): ?>
          <li><a id="len2" class="hoverable" href="myprofile.php">My Profile</a></li>
          <li><a id="len3" class="hoverable" href="#">Blank</a></li>
          <li><a id="len4" class="hoverable" href="#">Blank</a></li>
          <li><a id="len5" class="hoverable" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a id="len2" class="hoverable" href="login.php">Login</a></li>
          <li><a id="len3" class="hoverable" href="signup.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
</div>
