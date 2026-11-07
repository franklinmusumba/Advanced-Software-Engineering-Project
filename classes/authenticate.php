<?php
// classes/Auth.php
require_once '../config/config.php';

class Auth {
    private $pdo;

    public function __construct() {
        // Bring the $pdo variable from config.php into this class
        global $pdo;
        $this->pdo = $pdo;
    }

    // Register Patient
    public function registerPatient($firstName, $lastName, $email, $dob, $password) {
        try {
            $this->pdo->beginTransaction();

            // 1. Insert into Patient table
            $stmt = $this->pdo->prepare("INSERT INTO Patient (FirstName, LastName, Email, DateOfBirth) VALUES (?, ?, ?, ?)");
            $stmt->execute([$firstName, $lastName, $email, $dob]);
            $patientId = $this->pdo->lastInsertId();

            // 2. Insert into UserLogin table
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = $this->pdo->prepare("INSERT INTO UserLogin (Username, PasswordHash, Role, PatientID) VALUES (?, ?, 'Patient', ?)");
            $stmt2->execute([$email, $hash, $patientId]);

            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            if ($e->getCode() == 23000) return "Email already registered.";
            return "Registration failed: " . $e->getMessage();
        }
    }

    // Login
    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM UserLogin WHERE Username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['PasswordHash'])) {
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['role'] = $user['Role'];
            $_SESSION['username'] = $user['Username'];
            if ($user['PatientID']) $_SESSION['patient_id'] = $user['PatientID'];
            if ($user['TherapistID']) $_SESSION['therapist_id'] = $user['TherapistID'];

            $update = $this->pdo->prepare("UPDATE UserLogin SET LastLogin = NOW() WHERE UserID = ?");
            $update->execute([$user['UserID']]);

            return $user['Role']; 
        }
        return false;
    }

    // Check Roles
    public function requireRole($role) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . "auth/login.php");
            exit();
        }
        if ($_SESSION['role'] !== $role) {
            die("Access Denied: You do not have permission to view this page.");
        }
    }

    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}
?>