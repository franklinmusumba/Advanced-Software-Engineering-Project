<?php
// index.php
require_once 'config/config.php';

// Redirect to the login page automatically
header("Location: " . BASE_URL . "auth/login.php");
exit();
?>