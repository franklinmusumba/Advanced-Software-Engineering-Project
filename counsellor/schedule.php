<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Counsellor');

$counsellorId = $_SESSION['user_id'];

$query = "
    SELECT a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.Status, 
           p.FirstName, p.LastName 
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE a.CounsellorID = ?
    ORDER BY a.AppointmentDate DESC, a.AppointmentTime DESC
";
$stmt = $pdo->prepare($query);
$stmt->execute([$counsellorId]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Schedule - <?= APP_NAME ?></title>
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
        <div class="nav-user">
            <a href="<?= BASE_URL ?>functions/process_logout.php" class="btn-logout">Log out</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="page-header" style="margin-top: 20px;">
            <h1>My Schedule</h1>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr><th>Date</th><th>Time</th><th>Patient Name</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($appointments)): ?>
                        <tr><td colspan="5" style="text-align:center;">No appointments found.</td></tr>
                    <?php else: ?>
                        <?php foreach($appointments as $appt): ?>
                            <tr>
                                <td><?= date('M j, Y', strtotime($appt['AppointmentDate'])) ?></td>
                                <td><?= date('g:i A', strtotime($appt['AppointmentTime'])) ?></td>
                                <td><?= htmlspecialchars($appt['FirstName'] . ' ' . $appt['LastName']) ?></td>
                                <td>
                                    <span class="role-badge" style="background: #e2dcf5; color: #1c2b59;">
                                        <?= htmlspecialchars($appt['Status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="manage_appointment.php?id=<?= $appt['AppointmentID'] ?>" class="btn-outline" style="padding: 4px 10px; font-size: 0.8rem; text-decoration: none;">Manage</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>