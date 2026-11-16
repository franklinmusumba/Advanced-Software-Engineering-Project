<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "admin/users.php");
    exit();
}

// Get form data
$firstName = trim($_POST['first_name']);
$lastName = trim($_POST['last_name']);
$email = trim($_POST['email']);
$specialization = trim($_POST['specialization']);
$licenseNumber = trim($_POST['license_number']);
$phone = trim($_POST['phone']);
$hireDate = $_POST['hire_date'];
$password = $_POST['password'];

try {
    // 1. Check if email already exists in user_logins
    $stmt = $pdo->prepare("SELECT UserID FROM user_logins WHERE Username = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: " . BASE_URL . "admin/add_counsellor.php?error=Email already exists in the system.");
        exit();
    }

    // 2. Start a transaction to ensure both tables get populated
    $pdo->beginTransaction();

    // 3. Insert into therapists table
    $stmt = $pdo->prepare("
        INSERT INTO therapists (FirstName, LastName, Specialization, LicenseNumber, Email, PhoneNumber, HireDate) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$firstName, $lastName, $specialization, $licenseNumber, $email, $phone, $hireDate]);
    
    // Get the newly created TherapistID
    $therapistID = $pdo->lastInsertId();

    // 4. Insert into user_logins table
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO user_logins (TherapistID, Username, PasswordHash, Role) 
        VALUES (?, ?, ?, 'Therapist')
    ");
    $stmt->execute([$therapistID, $email, $hashedPassword]);

    // Commit the transaction
    $pdo->commit();

    // Redirect back to users page with success message
    header("Location: " . BASE_URL . "admin/users.php?msg=added");
    exit();

} catch (Exception $e) {
    // Rollback if anything fails
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error adding counsellor: " . $e->getMessage());
}