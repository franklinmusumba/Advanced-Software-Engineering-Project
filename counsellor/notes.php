<?php
// counsellor/notes.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Therapist');

$therapistId = $_SESSION['therapist_id'];
$tStmt = $pdo->prepare("SELECT FirstName, LastName FROM therapists WHERE TherapistID = ?");
$tStmt->execute([$therapistId]);
$therapist = $tStmt->fetch();

$fullName = $therapist ? $therapist['FirstName'] . ' ' . $therapist['LastName'] : 'Counsellor';
$initials = $therapist ? strtoupper(substr($therapist['FirstName'], 0, 1) . substr($therapist['LastName'], 0, 1)) : 'CO';

// Fetch appointments belonging ONLY to this counsellor
$apptStmt = $pdo->prepare("
    SELECT a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.Status,
           p.FirstName AS PFirst, p.LastName AS PLast, p.AnonymousID
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE a.TherapistID = ?
    ORDER BY a.AppointmentDate DESC, a.AppointmentTime DESC
");
$apptStmt->execute([$therapistId]);
$appointments = $apptStmt->fetchAll();

// Fetch past session notes authored ONLY by this counsellor (FR-16)
$notesStmt = $pdo->prepare("
    SELECT sn.SessionNoteID, sn.NoteContent, sn.CreatedDate, sn.IsConfidential,
           a.AppointmentDate, a.AppointmentTime, p.FirstName AS PFirst, p.LastName AS PLast
    FROM session_notes sn
    JOIN appointments a ON sn.AppointmentID = a.AppointmentID
    JOIN patients p ON a.PatientID = p.PatientID
    WHERE sn.TherapistID = ?
    ORDER BY sn.CreatedDate DESC
");
$notesStmt->execute([$therapistId]);
$myNotes = $notesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Notes - <?= APP_NAME ?></title>
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
            <a href="dashboard.php">Schedule</a>
            <a href="notes.php" class="active">Session notes</a>
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
                <span class="eyebrow">Session notes</span>
                <h1>Write session note.</h1>
                <p>Capture only what is useful for continuity of care.</p>
            </div>
        </div>

        <?php if (isset($_GET['saved'])): ?>
            <div class="alert-success">✓ Session note saved securely (audit log recorded).</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="dashboard-grid" style="grid-template-columns: 1.1fr .9fr; align-items: start;">
            <section class="panel">
                <form action="<?= BASE_URL ?>functions/process_note.php" method="POST">
                    <label class="field">
                        <span>Select Student Appointment</span>
                        <select name="appointment_id" required>
                            <option value="">-- Choose a session --</option>
                            <?php foreach ($appointments as $a): ?>
                                <option value="<?= (int)$a['AppointmentID'] ?>">
                                    <?= htmlspecialchars($a['PFirst'] . ' ' . $a['PLast']) ?> — <?= date('d M Y', strtotime($a['AppointmentDate'])) ?> at <?= date('g:i A', strtotime($a['AppointmentTime'])) ?> (<?= htmlspecialchars($a['Status']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="field">
                        <span>Private session note</span>
                        <textarea name="note_content" rows="9" style="min-height: 200px;" placeholder="Write a clear, concise summary of the session, key themes and agreed next steps..." required></textarea>
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-size: .85rem; margin-bottom: 14px; cursor: pointer;">
                        <input type="checkbox" name="is_confidential" value="1" checked>
                        <span>Mark as strictly confidential</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 8px; font-size: .85rem; margin-bottom: 18px; cursor: pointer;">
                        <input type="checkbox" name="mark_completed" value="1" checked>
                        <span>Also mark appointment status as <b>Completed</b></span>
                    </label>

                    <div style="background: #e9e6f6; border-radius: 14px; padding: 14px; margin-bottom: 20px; font-size: .82rem;">
                        🔒 <b>Private, only visible to you</b><br>
                        <span style="color: #59647b;">Students, admins, and other counsellors cannot access this note.</span>
                    </div>

                    <button type="submit" class="btn primary">Save note</button>
                </form>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <div>
                        <span class="eyebrow">Your records</span>
                        <h2>My past notes (<?= count($myNotes) ?>)</h2>
                    </div>
                </div>
                <?php if (count($myNotes) > 0): ?>
                    <div class="history-list">
                        <?php foreach ($myNotes as $n): ?>
                            <div class="history-item">
                                <div class="history-item-body">
                                    <b><?= htmlspecialchars($n['PFirst'] . ' ' . $n['PLast']) ?> · <?= date('d M Y', strtotime($n['AppointmentDate'])) ?></b>
                                    <small style="color: var(--navy); margin-top: 6px; line-height: 1.55;"><?= nl2br(htmlspecialchars($n['NoteContent'])) ?></small>
                                    <div class="meta-tags">
                                        <span class="meta-tag">🕒 Logged <?= date('d M, H:i', strtotime($n['CreatedDate'])) ?></span>
                                        <?php if ($n['IsConfidential']): ?>
                                            <span class="meta-tag">🔒 Confidential</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="font-size: .88rem;">No session notes recorded yet.</p>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <footer class="slim-footer">
        <span>🌿 <b>SU</b> Care Tracker</span>
        <span>© 2026 SU Care Tracker</span>
    </footer>
</div>
</body>
</html>
