<?php
// student/book.php
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

// Fetch all counsellors
$therapists = $pdo->query("SELECT TherapistID, FirstName, LastName, Specialization FROM therapists ORDER BY FirstName ASC")->fetchAll();

// Selected filters (for checking already-booked slots)
$selectedTherapist = isset($_GET['therapist_id']) ? (int)$_GET['therapist_id'] : ($therapists ? (int)$therapists['TherapistID'] : 0);
$selectedDate      = $_GET['date'] ?? date('Y-m-d', strtotime('+1 day'));

// Find booked times for the chosen counsellor & date
$bookedTimes = [];
if ($selectedTherapist > 0 && $selectedDate) {
    $bStmt = $pdo->prepare("SELECT AppointmentTime FROM appointments WHERE TherapistID = ? AND AppointmentDate = ? AND Status = 'Booked'");
    $bStmt->execute([$selectedTherapist, $selectedDate]);
    $bookedTimes = $bStmt->fetchAll(PDO::FETCH_COLUMN);
}

$timeSlots = [
    '09:00:00' => '9:00 AM',
    '10:30:00' => '10:30 AM',
    '12:00:00' => '12:00 PM',
    '14:00:00' => '2:00 PM',
    '15:30:00' => '3:30 PM'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student.css">
    <style>
        .slot-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin-top: 8px; }
        .slot-opt input { position: absolute; opacity: 0; pointer-events: none; }
        .slot-pill { display: block; text-align: center; padding: 12px 10px; border-radius: 14px; border: 1.5px solid var(--line); background: white; font-size: .88rem; font-weight: 600; cursor: pointer; transition: .15s; }
        .slot-opt input:checked + .slot-pill { background: var(--navy); color: white; border-color: var(--navy); }
        .slot-opt input:disabled + .slot-pill { background: #f1f5f9; color: #94a3b8; text-decoration: line-through; cursor: not-allowed; }
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
            <a href="book.php" class="active">Book</a>
            <a href="appointments.php">Appointments</a>
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
                <span class="eyebrow">Book a session</span>
                <h1>Choose a time that feels right.</h1>
                <p>Sessions are 50 minutes, confidential, and free for all SU students.</p>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="dashboard-grid" style="grid-template-columns: 1.25fr .75fr; align-items: start;">
            <section class="panel">
                <!-- Filter form to refresh available slots -->
                <form method="GET" action="book.php" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 18px;">
                    <label class="field" style="margin: 0;">
                        <span>1. Select Counsellor</span>
                        <select name="therapist_id" onchange="this.form.submit()">
                            <?php foreach ($therapists as $t): ?>
                                <option value="<?= (int)$t['TherapistID'] ?>" <?= (int)$t['TherapistID'] === $selectedTherapist ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['FirstName'] . ' ' . $t['LastName']) ?> (<?= htmlspecialchars($t['Specialization'] ?? 'General') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="field" style="margin: 0;">
                        <span>2. Select Date</span>
                        <input type="date" name="date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($selectedDate) ?>" onchange="this.form.submit()">
                    </label>
                </form>

                <!-- Booking submission form -->
                <form action="<?= BASE_URL ?>functions/process_appointment.php" method="POST">
                    <input type="hidden" name="action" value="book">
                    <input type="hidden" name="therapist_id" value="<?= $selectedTherapist ?>">
                    <input type="hidden" name="appointment_date" value="<?= htmlspecialchars($selectedDate) ?>">

                    <div class="field">
                        <span>3. Choose an Open Time Slot (<?= date('D, d M Y', strtotime($selectedDate)) ?>)</span>
                        <div class="slot-grid">
                            <?php foreach ($timeSlots as $dbTime => $label): 
                                $isTaken = in_array($dbTime, $bookedTimes, true);
                            ?>
                                <label class="slot-opt">
                                    <input type="radio" name="appointment_time" value="<?= $dbTime ?>" <?= $isTaken ? 'disabled' : '' ?> required>
                                    <span class="slot-pill"><?= $label ?><?= $isTaken ? ' (Booked)' : '' ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <label class="field">
                        <span>Anything you'd like your counsellor to know beforehand? <small>Optional</small></span>
                        <textarea name="notes_for_session" maxlength="255" placeholder="e.g. Exam stress, trouble sleeping..."></textarea>
                    </label>

                    <button type="submit" class="btn primary">Confirm booking</button>
                </form>
            </section>

            <aside class="panel" style="background: var(--mist);">
                <span class="eyebrow">Before your visit</span>
                <h3>What to expect</h3>
                <p style="font-size: .88rem;">Your counsellor only sees your student name and any optional note you share above. Your daily mood tracker logs remain 100% private to you.</p>
                <p style="font-size: .85rem; margin-bottom: 0;">Need to reschedule? You can cancel any upcoming session from the <strong>Appointments</strong> tab.</p>
            </aside>
        </div>
    </main>

    <footer class="slim-footer">
        <span>🌿 <b>SU</b> Care Tracker</span>
        <span>© 2026 SU Care Tracker</span>
    </footer>
</div>
</body>
</html>
