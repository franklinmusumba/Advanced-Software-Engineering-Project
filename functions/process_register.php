<?php
// functions/process_register.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    
    $result = $auth->registerPatient(
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['dob'],
        $_POST['password']
    );

    if ($result === true) {
        header("Location: " . BASE_URL . "auth/login.php?registered=1");
    } else {
        header("Location: " . BASE_URL . "auth/register.php?error=" . urlencode($result));
    }
    exit();
}
?>