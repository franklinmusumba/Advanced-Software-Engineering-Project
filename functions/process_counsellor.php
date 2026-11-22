<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Counsellor');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../counsellor/dashboard.php");
    exit();
}

$action = $_POST['action'];
$counsellorId = $_SESSION['user_id'];

try {
    if ($action === 'update_status') {
        $apptId = $_POST['appointment_id'];
        $status = $_POST['status'];
        
        $stmt = $pdo->prepare("UPDATE appointments SET Status = ? WHERE AppointmentID = ? AND CounsellorID = ?");
        $stmt->execute([$status, $apptId, $counsellorId]);
        
        header("Location: ../counsellor/manage_appointment.php?id=$apptId&msg=success");
        exit();
    }

    if ($action === 'update_profile') {
        $spec = trim($_POST['specialization']);
        $phone = trim($_POST['phone']);
        
        $stmt = $pdo->prepare("UPDATE counsellors SET Specialization = ?, PhoneNumber = ? WHERE CounsellorID = ?");
        $stmt->execute([$spec, $phone, $counsellorId]);
        
        header("Location: ../counsellor/profile.php?msg=updated");
        exit();
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}