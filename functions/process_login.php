<?php
// functions/process_login.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new Auth();
    $role = $auth->login($_POST['email'], $_POST['password']);

    if ($role) {
        if ($role === 'Patient') {
            header("Location: " . BASE_URL . "student/dashboard.php");
        } elseif ($role === 'Therapist') {
            header("Location: " . BASE_URL . "counsellor/dashboard.php");
        } elseif ($role === 'Admin') {
            header("Location: " . BASE_URL . "admin/dashboard.php");
        }
    } else {
        header("Location: " . BASE_URL . "auth/login.php?error=Invalid credentials");
    }
    exit();
}
?>