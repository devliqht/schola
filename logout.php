<?php 
    require_once 'api/config.php';
    $_SESSION = [];
    session_destroy();
    header("Location: index.php");
    exit;
?>