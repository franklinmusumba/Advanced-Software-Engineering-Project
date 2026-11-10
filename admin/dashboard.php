<?php 
require_once '../config/config.php'; 
require_once '../classes/Auth.php';

// 1. Enforce Admin Role (Redirects to login if not admin)
$auth = new Auth();
$auth->requireRole('Admin');

// 2. Fetch Data for Admin Dashboard
try {
    // Total Students
    $stmt = $pdo->query("SELECT COUNT(*) FROM Patient");
    $totalStudents = $stmt->fetchColumn();

    // Total Counsellors
    $stmt = $pdo->query("SELECT COUNT(*) FROM Therapist");
    $totalCounsellors = $stmt->fetchColumn();

    // Appointments this month
    $stmt = $pdo->query("SELECT COUNT(*) FROM Appointment WHERE MONTH(AppointmentDate) = MONTH(CURRENT_DATE()) AND YEAR(AppointmentDate) = YEAR(CURRENT_DATE())");
    $appointmentsThisMonth = $stmt->fetchColumn();

        // Appointments per counsellor
    $query = "
        SELECT t.FirstName, t.LastName, t.Specialization, 
               COUNT(a.AppointmentID) as Total,
               SUM(CASE WHEN a.Status = 'Booked' THEN 1 ELSE 0 END) as Upcoming,
               SUM(CASE WHEN a.Status = 'Completed' THEN 1 ELSE 0 END) as Completed
        FROM therapist t
        LEFT JOIN appointment a ON t.TherapistID = a.TherapistID
        GROUP BY t.TherapistID
    ";
    $stmt = $pdo->query($query);
    $counsellorStats = $stmt->fetchAll();

} catch (PDOException $e) {
    // If tables are missing, default to 0/mock data for UI testing
    $totalStudents = 0;
    $totalCounsellors = 0;
    $appointmentsThisMonth = 0;
    $counsellorStats = [];
}

// Get Admin's first name for the greeting
$adminName = "Jordan"; // Default fallback
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-brand">
            <span>🌿</span> <?= APP_NAME ?>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Overview</a>
            <a href="users.php">Manage users</a>
            <a href="reports.php">Reports</a>
        </div>
        <div class="nav-user">
            <div class="avatar">JA</div>
            <div class="user-info">
                <span class="name">Jordan Admin</span>
                <span class="role">Admin</span>
            </div>
            <a href="<?= BASE_URL ?>functions/process_logout.php" class="btn-logout">Log out</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        
        <div class="page-header" style="margin-top: 20px;">
            <div>
                <h1>Care service at a glance.</h1>
                <p class="text-muted" style="margin-top: 8px;">Operational information only — private notes and mood logs are never shown.</p>
            </div>
            <a href="users.php" class="btn-lime">Manage users</a>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-content">
                    <h2><?= number_format($totalStudents) ?></h2>
                    <p>Total students</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">♡</div>
                <div class="stat-content">
                    <h2><?= number_format($totalCounsellors) ?></h2>
                    <p>Counsellors</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">📅</div>
                <div class="stat-content">
                    <h2><?= number_format($appointmentsThisMonth) ?></h2>
                    <p>Appointments this month</p>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="table-container">
            <div class="table-header">
                <div>
                    <p class="text-uppercase text-muted" style="margin-bottom: 4px;">Service activity</p>
                    <h2 style="font-size: 1.5rem;">Appointments per counsellor</h2>
                </div>
                <span style="background: var(--card-purple-light); padding: 6px 16px; border-radius: 50px; font-size: 0.875rem; font-weight: 500;">
                    <?= date('F Y') ?>
                </span>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Counsellor</th>
                        <th>Specialty</th>
                        <th>Upcoming</th>
                        <th>Completed</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($counsellorStats)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">
                                No counsellor data available yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($counsellorStats as $stat): ?>
                            <tr>
                                <td>Dr. <?= htmlspecialchars($stat['FirstName'] . ' ' . $stat['LastName']) ?></td>
                                <td><?= htmlspecialchars($stat['Specialty'] ?? 'General Counselling') ?></td>
                                <td><?= $stat['Upcoming'] ?></td>
                                <td><?= $stat['Completed'] ?></td>
                                <td><strong><?= $stat['Total'] ?></strong></td>
                                <td><?= htmlspecialchars($stat['Specialization'] ?? 'General Counselling') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>