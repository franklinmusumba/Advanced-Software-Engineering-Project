<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Counsellor');

if (!isset($_GET['id'])) {
    header("Location: schedule.php");
    exit();
}

$apptId = $_GET['id'];
$counsellorId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, p.FirstName, p.LastName 
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE a.AppointmentID = ? AND a.CounsellorID = ?
");
$stmt->execute([$apptId, $counsellorId]);
$appt = $stmt->fetch();

if (!$appt) die("Appointment not found or unauthorized.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointment - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand"><span>🌿</span> <?= APP_NAME ?></div>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="schedule.php" class="active">My Schedule</a>
            <a href="notes.php">All Notes</a>
            <a href="profile.php">My Profile</a>
        </div>
    </nav>

    <div class="dashboard-container" style="max-width: 700px; margin-top: 30px;">
        <?php if(isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
            <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 20px;">Status updated successfully.</div>
        <?php endif; ?>

        <div class="card card-white" style="margin-bottom: 20px;">
            <h2>Patient: <?= htmlspecialchars($appt['FirstName'] . ' ' . $appt['LastName']) ?></h2>
            <p class="text-muted">Date: <?= date('l, F j, Y', strtotime($appt['AppointmentDate'])) ?> at <?= date('g:i A', strtotime($appt['AppointmentTime'])) ?></p>
        </div>

        <div class="card card-white" style="margin-bottom: 20px;">
            <h3>Update Status</h3>
            <form action="<?= BASE_URL ?>functions/process_counsellor.php" method="POST" style="display: flex; gap: 10px; margin-top: 10px;">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="appointment_id" value="<?= $apptId ?>">
                
                <select name="status" class="form-control" style="max-width: 200px;">
                    <option value="Booked" <?= $appt['Status'] == 'Booked' ? 'selected' : '' ?>>Booked</option>
                    <option value="Completed" <?= $appt['Status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="No-Show" <?= $appt['Status'] == 'No-Show' ? 'selected' : '' ?>>No-Show</option>
                    <option value="Cancelled" <?= $appt['Status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                
                <button type="submit" class="btn-lime">Update</button>
            </form>
            <p class="text-muted text-sm" style="margin-top: 15px;">
                Need to write a session note? <a href="notes.php?appointment_id=<?= $apptId ?>" style="color: var(--primary);">Go to Notes</a>
            </p>
        </div>
    </div>
</body>
</html>