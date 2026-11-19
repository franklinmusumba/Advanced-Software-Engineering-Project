<?php
// counsellor/dashboard.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Counsellor'); // Updated to match new role

$counsellorId = $_SESSION['counsellor_id']; // Updated session variable
$cStmt = $pdo->prepare("SELECT FirstName, LastName, Specialization FROM counsellors WHERE CounsellorID = ?"); // Updated table and column
$cStmt->execute([$counsellorId]);
$counsellor = $cStmt->fetch();

$firstName = $counsellor ? $counsellor['FirstName'] : 'Counsellor';
$fullName  = $counsellor ? $counsellor['FirstName'] . ' ' . $counsellor['LastName'] : 'Counsellor';
$initials  = $counsellor ? strtoupper(substr($counsellor['FirstName'], 0, 1) . substr($counsellor['LastName'], 0, 1)) : 'CO';

// Count upcoming booked sessions
$upcomingStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE CounsellorID = ? AND AppointmentDate >= CURDATE() AND Status = 'Booked'"); // Updated column
$upcomingStmt->execute([$counsellorId]);
$upcomingCount = (int)$upcomingStmt->fetchColumn();

// Count completed sessions
$completedStmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE CounsellorID = ? AND Status = 'Completed'"); // Updated column
$completedStmt->execute([$counsellorId]);
$completedCount = (int)$completedStmt->fetchColumn();

// Count total notes authored by this counsellor
$notesCountStmt = $pdo->prepare("SELECT COUNT(*) FROM session_notes WHERE CounsellorID = ?"); // Updated column
$notesCountStmt->execute([$counsellorId]);
$notesCount = (int)$notesCountStmt->fetchColumn();

// Fetch upcoming & recent appointments for this counsellor
$scheduleStmt = $pdo->prepare("
    SELECT a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.DurationMinutes, a.Status,
           p.FirstName AS PFirst, p.LastName AS PLast,
           sn.SessionNoteID
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID
    LEFT JOIN session_notes sn ON a.AppointmentID = sn.AppointmentID
    WHERE a.CounsellorID = ?  -- Updated column
    ORDER BY a.AppointmentDate ASC, a.AppointmentTime ASC
    LIMIT 10
");
$scheduleStmt->execute([$counsellorId]);
$schedule = $scheduleStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counsellor Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student.css">
</head>
<body>
<div class="app-shell">
    <nav class="app-nav">
        <a href="dashboard.php" class="logo">
            <span class="logo-mark">🌿</span>
            <span class="wordmark"><b>SU</b><span>Care Tracker</span></span>
        </a>
        <div class="nav-links">
            <a href="dashboard.php" class="active">Schedule</a>
            <a href="notes.php">Session notes</a>
        </div>
        <div class="user-pill">
            <span><?= htmlspecialchars($initials) ?></span>
            <div><b><?= htmlspecialchars($fullName) ?></b><small>Counsellor</small></div>
            <a href="<?= BASE_URL ?>functions/process_logout.php">Log out</a>
        </div>
    </nav>

    <main class="app-main">
        <div class="page-heading">
            <div>
                <span class="eyebrow"><?= date('l, j F') ?></span>
                <h1>Good day, <?= htmlspecialchars($firstName) ?>.</h1>
                <p>You have <strong><?= $upcomingCount ?></strong> upcoming session(s) scheduled.</p>
            </div>
            <a href="notes.php" class="btn primary">Write session note</a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 24px;">
            <article class="panel" style="display: flex; align-items: center; gap: 16px; padding: 22px;">
                <span style="width: 48px; height: 48px; display: grid; place-items: center; background: var(--sky); border-radius: 14px; font-size: 1.3rem;">📅</span>
                <div>
                    <b style="font-size: 1.6rem; display: block;"><?= $upcomingCount ?></b>
                    <small style="color: var(--grey);">Upcoming sessions</small>
                </div>
            </article>

            <article class="panel" style="display: flex; align-items: center; gap: 16px; padding: 22px;">
                <span style="width: 48px; height: 48px; display: grid; place-items: center; background: var(--lime); border-radius: 14px; font-size: 1.3rem;">✅</span>
                <div>
                    <b style="font-size: 1.6rem; display: block;"><?= $completedCount ?></b>
                    <small style="color: var(--grey);">Completed sessions</small>
                </div>
            </article>

            <article class="panel" style="display: flex; align-items: center; gap: 16px; padding: 22px;">
                <span style="width: 48px; height: 48px; display: grid; place-items: center; background: var(--indigo); border-radius: 14px; font-size: 1.3rem;">📝</span>
                <div>
                    <b style="font-size: 1.6rem; display: block;"><?= $notesCount ?></b>
                    <small style="color: var(--grey);">Private notes saved</small>
                </div>
            </article>
        </div>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Your caseload</span>
                    <h2>Scheduled &amp; recent sessions</h2>
                </div>
            </div>

            <?php if (count($schedule) > 0): ?>
                <?php foreach ($schedule as $row): ?>
                    <div class="appointment-item" style="grid-template-columns: 56px 1fr auto auto;">
                        <div class="date-tile">
                            <b><?= date('d', strtotime($row['AppointmentDate'])) ?></b>
                            <span><?= date('M', strtotime($row['AppointmentDate'])) ?></span>
                        </div>
                        <div>
                            <b><?= htmlspecialchars($row['PFirst'] . ' ' . $row['PLast']) ?></b>
                            <span><?= date('g:i A', strtotime($row['AppointmentTime'])) ?> · <?= (int)$row['DurationMinutes'] ?> mins</span>
                        </div>
                        <span class="badge"><?= htmlspecialchars($row['Status']) ?></span>
                        <a href="notes.php" class="btn secondary" style="min-height: 38px; padding: 0 16px; font-size: .8rem;">
                            <?= $row['SessionNoteID'] ? 'Edit note' : 'Write note' ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="font-size: .9rem;">No appointments assigned to your schedule yet.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="slim-footer">
        <span>🌿 <b>SU</b> Care Tracker</span>
        <span>© 2026 SU Care Tracker</span>
    </footer>
</div>
</body>
</html>