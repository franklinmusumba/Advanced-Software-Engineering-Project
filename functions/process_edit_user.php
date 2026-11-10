<?php
// functions/process_edit_user.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $action = $_POST['action'];

    // PREVENT ADMIN FROM DELETING THEMSELVES
    if ($action === 'delete' && $userId == $_SESSION['user_id']) {
        die("Error: You cannot delete your own admin account. Please log in as another admin to do this.");
    }

    try {
        $pdo->beginTransaction();

        // GET THE PATIENT OR THERAPIST ID ASSOCIATED WITH THIS USER
        $stmt = $pdo->prepare("SELECT PatientID, TherapistID FROM userlogin WHERE UserID = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($action === 'delete') {
            // Deleting from Patient or Therapist will automatically delete the UserLogin (Cascade)
            if ($user['PatientID']) {
                $del = $pdo->prepare("DELETE FROM patient WHERE PatientID = ?");
                $del->execute([$user['PatientID']]);
            } elseif ($user['TherapistID']) {
                $del = $pdo->prepare("DELETE FROM therapist WHERE TherapistID = ?");
                $del->execute([$user['TherapistID']]);
            } else {
                // If it's a pure Admin (no patient/therapist ID), just delete from userlogin
                $del = $pdo->prepare("DELETE FROM userlogin WHERE UserID = ?");
                $del->execute([$userId]);
            }
            $pdo->commit();
            header("Location: " . BASE_URL . "admin/users.php?msg=deleted");
            exit();
        } 
        
        elseif ($action === 'update') {
            $firstName = trim($_POST['first_name']);
            $lastName = trim($_POST['last_name']);
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // 1. Update the First and Last Name in Patient or Therapist table
            if ($user['PatientID']) {
                $upd = $pdo->prepare("UPDATE patient SET FirstName = ?, LastName = ?, Email = ? WHERE PatientID = ?");
                $upd->execute([$firstName, $lastName, $username, $user['PatientID']]);
            } elseif ($user['TherapistID']) {
                $upd = $pdo->prepare("UPDATE therapist SET FirstName = ?, LastName = ?, Email = ? WHERE TherapistID = ?");
                $upd->execute([$firstName, $lastName, $username, $user['TherapistID']]);
            }

            // 2. Update the UserLogin table (Username)
            $updLogin = $pdo->prepare("UPDATE userlogin SET Username = ? WHERE UserID = ?");
            $updLogin->execute([$username, $userId]);

            // 3. Update Password ONLY IF a new one was typed in
            if (!empty($password)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $updPass = $pdo->prepare("UPDATE userlogin SET PasswordHash = ? WHERE UserID = ?");
                $updPass->execute([$newHash, $userId]);
            }

            $pdo->commit();
            header("Location: " . BASE_URL . "admin/users.php?msg=updated");
            exit();
        }

    } catch (PDOException $e) {
        $pdo->rollBack();
        // If the email is already used by someone else, this will catch it
        if ($e->getCode() == 23000) {
            die("Error: That Email/Username is already registered to another account.");
        }
        die("Database Error: " . $e->getMessage());
    }
} else {
    header("Location: " . BASE_URL . "admin/users.php");
    exit();
}
?>