<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Function to check if a user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['username']);
}

/**
 * Redirect user to the login page if not logged in
 */
function ensureLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
