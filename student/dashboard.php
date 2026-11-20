<?php
// student/dashboard.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Patient');

$patientId = $_SESSION['patient_id'];
$stmt = $pdo->prepare("SELECT FirstName, LastName FROM patients WHERE PatientID = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

$firstName = $patient ? $patient['FirstName'] : 'Student';
$fullName  = $patient ? $patient['FirstName'] . ' ' . $patient['LastName'] : 'Student';
$initials  = $patient ? strtoupper(substr($patient['FirstName'], 0, 1) . substr($patient['LastName'], 0, 1)) : 'ST';

$apptStmt = $pdo->prepare("
    SELECT a.AppointmentDate, a.AppointmentTime, a.DurationMinutes, a.Status,
           c.FirstName AS CFirst, c.LastName AS CLast, c.Specialization
    FROM appointments a
    JOIN counsellors c ON a.CounsellorID = c.CounsellorID
    WHERE a.PatientID = ? AND a.AppointmentDate >= CURDATE() AND a.Status = 'Booked'
    ORDER BY a.AppointmentDate ASC, a.AppointmentTime ASC
    LIMIT 3
");
$apptStmt->execute([$patientId]);
$appointments = $apptStmt->fetchAll();

$moodStmt = $pdo->prepare("
    SELECT MoodScore FROM moodtrackers 
    WHERE PatientID = ? 
    ORDER BY MoodDate DESC, MoodEntryID DESC 
    LIMIT 7
");
$moodStmt->execute([$patientId]);
$recentMoods = $moodStmt->fetchAll();
$avgMood = count($recentMoods) > 0 
    ? number_format(array_sum(array_column($recentMoods, 'MoodScore')) / count($recentMoods), 1) 
    : '—';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student.css">
    <style>
        /* Digital Desk Clock Encapsulating Frame */
        .desk-clock {
            display: inline-flex;
            align-items: stretch;
            gap: 8px;
            background: var(--navy);
            padding: 8px;
            border-radius: 16px;
            margin-bottom: 18px;
            box-shadow: 0 10px 24px rgba(31, 58, 95, 0.14);
            font-variant-numeric: tabular-nums;
        }
        .clock-cell {
            background: rgba(255, 255, 255, 0.09);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 11px;
            padding: 8px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 74px;
            text-align: center;
        }
        .clock-cell .cell-label {
            font-size: 0.62rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--sky);
            font-weight: 600;
            margin-bottom: 2px;
        }
        .clock-cell .cell-val {
            font-family: "Plus Jakarta Sans", sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.15;
        }
        .clock-cell.time-box {
            background: rgba(162, 207, 254, 0.16);
            border-color: rgba(162, 207, 254, 0.35);
            min-width: 132px;
        }
        .clock-cell.time-box .cell-val {
            color: var(--lime);
            letter-spacing: 0.04em;
        }
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
            <a href="dashboard.php" class="active">Overview</a>
            <a href="book.php">Book</a>
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
                <div class="desk-clock" aria-label="Digital date and time">
                    <div class="clock-cell">
                        <span class="cell-label">Day</span>
                        <span class="cell-val" id="clock-day"><?= strtoupper(date('D')) ?></span>
                    </div>
                    <div class="clock-cell">
                        <span class="cell-label">Date</span>
                        <span class="cell-val" id="clock-date"><?= date('d M') ?></span>
                    </div>
                    <div class="clock-cell time-box">
                        <span class="cell-label">Time</span>
                        <span class="cell-val" id="clock-time"><?= date('H:i:s') ?></span>
                    </div>
                </div>
                <h1>Hello, <?= htmlspecialchars($firstName) ?>.</h1>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="book.php" class="btn primary">Book session</a>
                <a href="mood.php" class="btn secondary">Daily check-in</a>
            </div>
        </div>

        <?php if (count($appointments) > 0): $next = $appointments[0]; ?>
            <section class="reminder">
                <span class="icon-box">📅</span>
                <div>
                    <span>Coming up next</span>
                    <h3>Session with <?= htmlspecialchars($next['CFirst'] . ' ' . $next['CLast']) ?></h3>
                    <p><?= date('l, j F', strtotime($next['AppointmentDate'])) ?> · <?= date('g:i A', strtotime($next['AppointmentTime'])) ?> (<?= (int)$next['DurationMinutes'] ?> mins)</p>
                </div>
                <a href="appointments.php" class="btn secondary" style="background: rgba(255,255,255,.55); border: 0;">View all</a>
            </section>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="quick-grid">
                <a href="mood.php" class="quick-card indigo">
                    <span>💜</span>
                    <div><h3>Log your mood</h3><p>A gentle check-in takes one minute</p></div>
                    <b>→</b>
                </a>
                <a href="book.php" class="quick-card lime">
                    <span>🗓️</span>
                    <div><h3>Book a session</h3><p>Schedule 50 minutes with a counsellor</p></div>
                    <b>→</b>
                </a>
            </div>

            <article class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Your week</span><h2>Mood check-in summary</h2></div>
                    <span class="score-chip">Avg. <?= $avgMood ?> / 5</span>
                </div>
                <p>You have logged <strong><?= count($recentMoods) ?></strong> check-in(s) in the last 7 entries. Regular check-ins help you notice patterns gently.</p>
                <a href="history.php" class="btn secondary">Open full history</a>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <div><span class="eyebrow">Your care</span><h2>Next appointments</h2></div>
                    <a href="appointments.php">Manage</a>
                </div>
                <?php if (count($appointments) > 0): ?>
                    <?php foreach ($appointments as $appt): ?>
                        <div class="appointment-item">
                            <div class="date-tile">
                                <b><?= date('d', strtotime($appt['AppointmentDate'])) ?></b>
                                <span><?= date('M', strtotime($appt['AppointmentDate'])) ?></span>
                            </div>
                            <div>
                                <b><?= htmlspecialchars($appt['CFirst'] . ' ' . $appt['CLast']) ?></b>
                                <span><?= htmlspecialchars($appt['Specialization'] ?? 'Counselling') ?> · <?= date('g:i A', strtotime($appt['AppointmentTime'])) ?></span>
                            </div>
                            <span class="badge"><?= htmlspecialchars($appt['Status']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="font-size: .88rem;">No upcoming sessions scheduled right now.</p>
                <?php endif; ?>
            </article>
        </div>
    </main>

    <footer class="slim-footer">
        <span>🌿 <b>SU</b> Care Tracker</span>
        <span>© 2026 SU Care Tracker</span>
    </footer>
</div>

<script>
    function updateDeskClock() {
        const now = new Date();
        const dayStr = now.toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase();
        const dateStr = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short' }).toUpperCase();
        const hrs = String(now.getHours()).padStart(2, '0');
        const mins = String(now.getMinutes()).padStart(2, '0');
        const secs = String(now.getSeconds()).padStart(2, '0');

        const dayEl = document.getElementById('clock-day');
        const dateEl = document.getElementById('clock-date');
        const timeEl = document.getElementById('clock-time');

        if (dayEl) dayEl.textContent = dayStr;
        if (dateEl) dateEl.textContent = dateStr;
        if (timeEl) timeEl.textContent = `${hrs}:${mins}:${secs}`;
    }
    updateDeskClock();
    setInterval(updateDeskClock, 1000);
</script>
</body>
</html>