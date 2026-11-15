<?php
// student/appointments.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Patient');

$patientId = $_SESSION['patient_id'];
$stmt = $pdo->prepare("SELECT FirstName, LastName FROM patients WHERE PatientID = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

$fullName = $patient ? $patient['FirstName'] . ' ' . $patient['LastName'] : 'Student';
$initials = $patient ? strtoupper(substr($patient['FirstName'], 0, 1) . substr($patient['LastName'], 0, 1)) : 'ST';

// Fetch all appointments for this student joined with counsellor details
$allStmt = $pdo->prepare("
    SELECT a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.DurationMinutes, a.Status, a.NotesForSession,
           t.FirstName AS TFirst, t.LastName AS TLast, t.Specialization
    FROM appointments a
    JOIN therapists t ON a.TherapistID = t.TherapistID
    WHERE a.PatientID = ?
    ORDER BY a.AppointmentDate ASC, a.AppointmentTime ASC
");
$allStmt->execute([$patientId]);
$allAppointments = $allStmt->fetchAll();

$today = date('Y-m-d');
$upcoming = [];
$past     = [];

foreach ($allAppointments as $appt) {
    if ($appt['Status'] === 'Booked' && $appt['AppointmentDate'] >= $today) {
        $upcoming[] = $appt;
    } else {
        $past[] = $appt;
    }
}
$past = array_reverse($past); // Most recent past sessions first

function relativeDayLabel($dateStr) {
    $diff = (int)((strtotime($dateStr) - strtotime(date('Y-m-d'))) / 86400);
    if ($diff === 0) return 'Today';
    if ($diff === 1) return 'Tomorrow';
    if ($diff > 1) return "In {$diff} days";
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student.css">
    <style>
        .appt-table { width: 100%; border-collapse: collapse; }
        .appt-table th { text-align: left; font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: var(--grey); padding: 0 12px 14px; border-bottom: 1px solid var(--line); }
        .appt-table td { padding: 18px 12px; border-bottom: 1px solid var(--line); vertical-align: middle; font-size: .92rem; }
        .appt-table tr:last-child td { border-bottom: none; }
        .btn-cancel { background: #f1f5f9; color: var(--navy); border: 1px solid var(--line); border-radius: 999px; padding: 8px 20px; font-size: .82rem; font-weight: 600; transition: .15s; }
        .btn-cancel:hover { background: #fee2e2; color: var(--red); border-color: #fecaca; }
        .badge.completed { background: #dcfce7; color: #166534; }
        .badge.cancelled { background: #fee2e2; color: var(--red); }
    </style>
</head>
<body>
<div class="app-shell">
    <nav class="app-nav">
        <a href="dashboard.php" class="logo">
            <span class="logo-mark">🌿</span>
            <span class="wordmark"><b>SU</b><span>Care Tracker</span></span>
        </a>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="book.php">Book</a>
            <a href="appointments.php" class="active">Appointments</a>
            <a href="mood.php">Mood check-in</a>
            <a href="history.php">History</a>
        </div>
        <div class="user-pill">
            <span><?= htmlspecialchars($initials) ?></span>
            <div><b><?= htmlspecialchars($fullName) ?></b><small>Student</small></div>
            <a href="<?= BASE_URL ?>functions/process_logout.php">Log out</a>
        </div>
    </nav>

    <main class="app-main">
        <div class="page-heading">
            <div>
                <span class="eyebrow">My appointments</span>
                <h1>Your time, in one place.</h1>
            </div>
            <a href="book.php" class="btn primary">Book appointment</a>
        </div>

        <?php if (isset($_GET['booked'])): ?>
            <div class="alert-success">✓ Your counselling session is booked!</div>
        <?php endif; ?>
        <?php if (isset($_GET['cancelled'])): ?>
            <div class="alert-success">Appointment cancelled.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <!-- Upcoming Appointments -->
        <section class="panel" style="margin-bottom: 28px;">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Scheduled</span>
                    <h2>Upcoming (<?= count($upcoming) ?>)</h2>
                </div>
            </div>

            <?php if (count($upcoming) > 0): ?>
                <table class="appt-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Counsellor</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming as $u): $rel = relativeDayLabel($u['AppointmentDate']); ?>
                            <tr>
                                <td>
                                    <b><?= date('d M Y', strtotime($u['AppointmentDate'])) ?></b>
                                    <?php if ($rel): ?><br><small style="color: var(--grey);"><?= $rel ?></small><?php endif; ?>
                                </td>
                                <td>
                                    <b><?= htmlspecialchars($u['TFirst'] . ' ' . $u['TLast']) ?></b>
                                    <br><small style="color: var(--grey);"><?= htmlspecialchars($u['Specialization'] ?? 'Counsellor') ?></small>
                                </td>
                                <td><?= date('g:i A', strtotime($u['AppointmentTime'])) ?></td>
                                <td><span class="badge"><?= htmlspecialchars($u['Status']) ?></span></td>
                                <td style="text-align: right;">
                                    <form action="<?= BASE_URL ?>functions/process_appointment.php" method="POST" onsubmit="return confirm('Cancel this appointment?');" style="display:inline;">
                                        <input type="hidden" name="action" value="cancel">
                                        <input type="hidden" name="appointment_id" value="<?= (int)$u['AppointmentID'] ?>">
                                        <button type="submit" class="btn-cancel">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="font-size: .9rem;">You have no upcoming sessions booked. Click <strong>Book appointment</strong> to schedule one.</p>
            <?php endif; ?>
        </section>

        <!-- Past & Cancelled Appointments -->
        <section class="panel">
            <div class="panel-head">
                <div>
                    <span class="eyebrow">Archive</span>
                    <h2>Past &amp; cancelled (<?= count($past) ?>)</h2>
                </div>
            </div>

            <?php if (count($past) > 0): ?>
                <table class="appt-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Counsellor</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($past as $p): 
                            $statusClass = strtolower($p['Status']);
                        ?>
                            <tr>
                                <td><b><?= date('d M Y', strtotime($p['AppointmentDate'])) ?></b></td>
                                <td><?= htmlspecialchars($p['TFirst'] . ' ' . $p['TLast']) ?></td>
                                <td><?= date('g:i A', strtotime($p['AppointmentTime'])) ?></td>
                                <td><span class="badge <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($p['Status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="font-size: .9rem;">No past appointments yet.</p>
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
