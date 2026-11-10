<?php 
require_once '../config/config.php'; 
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Admin');

// Fetch Session Notes with Student and Counsellor details
try {
    $query = "
        SELECT 
            sn.SessionNoteID,
            sn.NoteContent,
            sn.CreatedDate,
            sn.IsConfidential,
            a.AppointmentDate,
            p.FirstName AS PatientFirst,
            p.LastName AS PatientLast,
            t.FirstName AS TherapistFirst,
            t.LastName AS TherapistLast
        FROM session_notes sn
        JOIN appointments a ON sn.AppointmentID = a.AppointmentID
        JOIN patients p ON a.PatientID = p.PatientID
        JOIN therapists t ON sn.TherapistID = t.TherapistID
        ORDER BY sn.CreatedDate DESC
    ";
    $stmt = $pdo->query($query);
    $reports = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Reports - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <style>
        /* Print-specific styles */
        @media print {
            .top-nav, .btn-print, .no-print { display: none !important; }
            body { background: white; }
            .dashboard-container { padding: 0; max-width: 100%; }
            .card { box-shadow: none; border: 1px solid #ccc; padding: 15px; page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav no-print">
        <div class="nav-brand"><span>🌿</span> <?= APP_NAME ?></div>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="users.php">Manage users</a>
            <a href="reports.php" class="active">Session Reports</a>
        </div>
        <div class="nav-user">
            <a href="<?= BASE_URL ?>functions/process_logout.php" class="btn-logout">Log out</a>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="page-header" style="margin-top: 20px;">
            <div>
                <h1>Session Reports</h1>
                <p class="text-muted" style="margin-top: 8px;">Read-only view of notes logged by counsellors.</p>
            </div>
            <button onclick="window.print()" class="btn-lime btn-print" style="border: none; cursor: pointer;">
                🖨️ Print Report
            </button>
        </div>

        <?php if(empty($reports)): ?>
            <div class="card card-white" style="text-align: center; padding: 40px;">
                <p class="text-muted">No session notes have been recorded yet.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach($reports as $report): ?>
                    <div class="card card-white" style="display: block; border-left: 6px solid var(--slot-selected);">
                        
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                            <div>
                                <h3 style="font-size: 1.1rem; margin-bottom: 4px;">
                                    Student: <?= htmlspecialchars($report['PatientFirst'] . ' ' . $report['PatientLast']) ?>
                                </h3>
                                <p class="text-muted text-sm">
                                    Counsellor: <strong>Dr. <?= htmlspecialchars($report['TherapistFirst'] . ' ' . $report['TherapistLast']) ?></strong>
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p class="text-uppercase text-muted" style="font-size: 0.7rem;">Session Date</p>
                                <p style="font-weight: 600; font-size: 0.9rem;">
                                    <?= date('M j, Y', strtotime($report['AppointmentDate'])) ?>
                                </p>
                                <?php if($report['IsConfidential']): ?>
                                    <span style="background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: bold; margin-top: 4px; display: inline-block;">
                                        🔒 CONFIDENTIAL
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-sm);">
                            <p style="font-size: 0.75rem; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;">Session Note:</p>
                            <p style="font-size: 0.95rem; line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($report['NoteContent']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>