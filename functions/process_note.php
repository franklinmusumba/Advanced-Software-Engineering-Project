<?php
// functions/process_note.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Therapist');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $therapistId   = $_SESSION['therapist_id'] ?? null;
    $appointmentId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $noteContent   = trim($_POST['note_content'] ?? '');
    $isConfidential = isset($_POST['is_confidential']) ? 1 : 0;

    if (!$therapistId || $appointmentId <= 0 || $noteContent === '') {
        header("Location: " . BASE_URL . "counsellor/notes.php?error=Please select an appointment and enter your session note.");
        exit();
    }

    try {
        // Verify this appointment actually belongs to the logged-in counsellor
        $checkStmt = $pdo->prepare("SELECT AppointmentID FROM appointments WHERE AppointmentID = ? AND TherapistID = ?");
        $checkStmt->execute([$appointmentId, $therapistId]);
        if (!$checkStmt->fetch()) {
            header("Location: " . BASE_URL . "counsellor/notes.php?error=Unauthorized appointment selection.");
            exit();
        }

        // Insert or update note (since AppointmentID is UNIQUE in session_notes)
        $stmt = $pdo->prepare("
            INSERT INTO session_notes (AppointmentID, TherapistID, NoteContent, CreatedDate, IsConfidential)
            VALUES (:appt_id, :therapist_id, :content, NOW(), :confidential)
            ON DUPLICATE KEY UPDATE 
                NoteContent = VALUES(NoteContent),
                IsConfidential = VALUES(IsConfidential)
        ");
        $stmt->execute([
            ':appt_id'      => $appointmentId,
            ':therapist_id' => $therapistId,
            ':content'      => $noteContent,
            ':confidential' => $isConfidential
        ]);

        // Mark appointment as Completed if requested
        if (isset($_POST['mark_completed'])) {
            $upd = $pdo->prepare("UPDATE appointments SET Status = 'Completed' WHERE AppointmentID = ? AND TherapistID = ?");
            $upd->execute([$appointmentId, $therapistId]);
        }

        header("Location: " . BASE_URL . "counsellor/notes.php?saved=1");
        exit();
    } catch (PDOException $e) {
        header("Location: " . BASE_URL . "counsellor/notes.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
