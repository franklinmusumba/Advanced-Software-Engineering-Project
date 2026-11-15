<?php
// student/history.php
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

$logsStmt = $pdo->prepare("
    SELECT MoodEntryID, MoodDate, MoodScore, EnergyLevel, SleepHours, TriggerEvents, Notes 
    FROM moodtrackers 
    WHERE PatientID = ? 
    ORDER BY MoodDate DESC, MoodEntryID DESC 
    LIMIT 14
");
$logsStmt->execute([$patientId]);
$logs = $logsStmt->fetchAll();

$recent7 = array_slice($logs, 0, 7);
$avgMood = count($recent7) > 0 
    ? number_format(array_sum(array_column($recent7, 'MoodScore')) / count($recent7), 1) 
    : '—';

$chartData = array_reverse($recent7);
$points = [];
$areaPoints = "10 180 ";
if (count($chartData) > 0) {
    $step = count($chartData) > 1 ? 600 / (count($chartData) - 1) : 0;
    foreach ($chartData as $idx => $row) {
        $x = round(10 + ($idx * $step));
        $y = round(160 - (($row['MoodScore'] - 1) * 30));
        $points[] = ['x' => $x, 'y' => $y, 'score' => $row['MoodScore'], 'date' => date('D', strtotime($row['MoodDate']))];
        $areaPoints .= "$x $y ";
    }
    $lastX = end($points)['x'];
    $areaPoints .= "$lastX 180Z";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood History - <?= APP_NAME ?></title>
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
            <a href="mood.php">Mood check-in</a>
            <a href="history.php" class="active">History</a>
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
                <span class="eyebrow">Mood history</span>
                <h1>Notice your patterns, gently.</h1>
                <p>Your check-ins are yours. There is no good or bad score.</p>
            </div>
            <a href="mood.php" class="btn secondary">Log today's mood</a>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div class="alert-success">✓ Your mood check-in has been saved privately.</div>
        <?php endif; ?>

        <div class="history-grid">
            <article class="brain-panel">
                <div>
                    <span class="eyebrow">Your inner weather</span>
                    <h2>A record of how you've been.</h2>
                </div>
                <div class="brain" style="margin: 12px 0; border-radius: 22px; overflow: hidden;">
                    <video src="<?= BASE_URL ?>assets/img/brain.mp4" 
                           autoplay loop muted playsinline 
                           style="width: 100%; max-height: 290px; object-fit: cover; display: block; mix-blend-mode: lighten;">
                    </video>
                </div>
                <p>Every check-in helps you notice, not judge.</p>
            </article>

            <article class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Last 7 entries</span>
                        <h2>Your mood rhythm</h2>
                    </div>
                    <span class="score-chip">Avg. <?= $avgMood ?> / 5</span>
                </div>

                <?php if (count($points) > 0): ?>
                    <div class="chart">
                        <div class="chart-grid"><i></i><i></i><i></i><i></i></div>
                        <svg viewBox="0 0 620 180" preserveAspectRatio="none">
                            <defs>
                                <linearGradient id="area" x1="0" y1="0" x2="0" y2="1">
                                    <stop stop-color="#B0A6DF" stop-opacity=".6"/>
                                    <stop offset="1" stop-color="#B0A6DF" stop-opacity=".05"/>
                                </linearGradient>
                            </defs>
                            <?php if (count($points) > 1): ?>
                                <path class="area" d="M<?= $areaPoints ?>"/>
                                <path class="line" d="M<?= implode(' ', array_map(fn($p) => "{$p['x']} {$p['y']}", $points)) ?>"/>
                            <?php endif; ?>
                            <?php foreach ($points as $p): ?>
                                <circle class="<?= $p['score'] >= 4 ? 'peak' : '' ?>" cx="<?= $p['x'] ?>" cy="<?= $p['y'] ?>" r="5"/>
                            <?php endforeach; ?>
                        </svg>
                        <div class="chart-labels">
                            <?php foreach ($points as $p): ?>
                                <span><?= $p['date'] ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <p>No mood entries logged yet. Click <strong>Log today's mood</strong> to record your first check-in!</p>
                <?php endif; ?>

                <div class="history-list">
                    <?php foreach ($logs as $log): ?>
                        <div class="history-item">
                            <span class="mood-score"><?= (int)$log['MoodScore'] ?></span>
                            <div class="history-item-body">
                                <b><?= date('D, d M Y', strtotime($log['MoodDate'])) ?></b>
                                <small><?= !empty($log['Notes']) ? htmlspecialchars($log['Notes']) : 'No note added.' ?></small>
                                <div class="meta-tags">
                                    <?php if (!empty($log['EnergyLevel'])): ?>
                                        <span class="meta-tag">⚡ Energy: <?= (int)$log['EnergyLevel'] ?>/5</span>
                                    <?php endif; ?>
                                    <?php if ($log['SleepHours'] !== null): ?>
                                        <span class="meta-tag">🌙 Sleep: <?= htmlspecialchars($log['SleepHours']) ?>h</span>
                                    <?php endif; ?>
                                    <?php if (!empty($log['TriggerEvents'])): ?>
                                        <span class="meta-tag">🏷️ <?= htmlspecialchars($log['TriggerEvents']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </article>
        </div>
    </main>

    <footer class="slim-footer">
        <span>🌿 <b>SU</b> Care Tracker</span>
        <span>© 2026 SU Care Tracker</span>
    </footer>
</div>
</body>
</html>
