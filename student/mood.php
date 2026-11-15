<?php
// student/mood.php
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

$moods = [
    1 => 'Very low',
    2 => 'Low',
    3 => 'Okay',
    4 => 'Good',
    5 => 'Great'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Check-in - <?= APP_NAME ?></title>
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
            <a href="dashboard.php">Overview</a>
            <a href="book.php">Book</a>
            <a href="appointments.php">Appointments</a>
            <a href="mood.php" class="active">Mood check-in</a>
            <a href="history.php">History</a>
        </div>
        <div class="user-pill">
            <span><?= htmlspecialchars($initials) ?></span>
            <div><b><?= htmlspecialchars($fullName) ?></b><small>Student</small></div>
            <a href="<?= BASE_URL ?>functions/process_logout.php">Log out</a>
        </div>
    </nav>

    <main class="app-main">
        <div class="mood-page">
            <span class="eyebrow">A moment for you</span>
            <h1>How are you feeling today?</h1>
            <p>Choose what feels closest. There is no wrong answer.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>functions/process_mood.php" method="POST">
                <div class="mood-selector">
                    <?php foreach ($moods as $score => $label): ?>
                        <label class="mood-option">
                            <input type="radio" name="mood_score" value="<?= $score ?>" <?= $score === 4 ? 'checked' : '' ?> required>
                            <div class="mood-card">
                                <b><?= $score ?></b>
                                <span><?= $label ?></span>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="extra-metrics">
                    <label class="field">
                        <span>Energy level (1–5) <small>Optional</small></span>
                        <select name="energy_level">
                            <option value="">Select energy</option>
                            <option value="1">1 - Exhausted</option>
                            <option value="2">2 - Low</option>
                            <option value="3">3 - Moderate</option>
                            <option value="4">4 - Energized</option>
                            <option value="5">5 - Peak</option>
                        </select>
                    </label>

                    <label class="field">
                        <span>Sleep hours <small>Optional</small></span>
                        <input type="number" step="0.5" min="0" max="24" name="sleep_hours" placeholder="e.g. 7.5">
                    </label>

                    <label class="field">
                        <span>Main trigger <small>Optional</small></span>
                        <input type="text" maxlength="200" name="trigger_events" placeholder="e.g. Exams, Lack of sleep">
                    </label>
                </div>

                <label class="field">
                    <span>Want to add a note? <small>Optional</small></span>
                    <textarea maxlength="255" name="notes" placeholder="A few words about today..."></textarea>
                </label>

                <div class="button-row">
                    <button type="submit" class="btn primary">Save my check-in</button>
                    <a href="dashboard.php" class="btn ghost">Not right now</a>
                </div>
            </form>

            <small class="privacy">🔒 Only you can see your mood logs.</small>
        </div>
    </main>

    <footer class="slim-footer">
        <span>🌿 <b>SU</b> Care Tracker</span>
        <span>© 2026 SU Care Tracker</span>
    </footer>
</div>
</body>
</html>
