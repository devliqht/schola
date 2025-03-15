<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get theme from cookie or default to 'dark'
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
?>