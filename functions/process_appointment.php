<?php
// functions/process_appointment.php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Patient');

$patientId = $_SESSION['patient_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $patientId) {
    $action = $_POST['action'] ?? '';

    if ($action === 'book') {
        $therapistId = isset($_POST['therapist_id']) ? (int)$_POST['therapist_id'] : 0;
        $date        = $_POST['appointment_date'] ?? '';
        $time        = $_POST['appointment_time'] ?? '';
        $notes       = mb_substr(trim($_POST['notes_for_session'] ?? ''), 0, 255);

        if ($therapistId <= 0 || !$date || !$time) {
            header("Location: " . BASE_URL . "student/book.php?error=Please select a counsellor, date, and time slot.");
            exit();
        }

        try {
            // Prevent double-booking the same counsellor at the same date & time
            $check = $pdo->prepare("
                SELECT AppointmentID FROM appointments 
                WHERE TherapistID = ? AND AppointmentDate = ? AND AppointmentTime = ? AND Status = 'Booked'
            ");
            $check->execute([$therapistId, $date, $time]);
            if ($check->fetch()) {
                header("Location: " . BASE_URL . "student/book.php?error=That time slot is already booked. Please choose another.");
                exit();
            }

            $stmt = $pdo->prepare("
                INSERT INTO appointments (PatientID, TherapistID, AppointmentDate, AppointmentTime, DurationMinutes, Status, NotesForSession)
                VALUES (?, ?, ?, ?, 50, 'Booked', ?)
            ");
            $stmt->execute([$patientId, $therapistId, $date, $time, $notes !== '' ? $notes : null]);

            header("Location: " . BASE_URL . "student/appointments.php?booked=1");
            exit();
        } catch (PDOException $e) {
            header("Location: " . BASE_URL . "student/book.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    if ($action === 'cancel') {
        $apptId = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
        try {
            $stmt = $pdo->prepare("
                UPDATE appointments SET Status = 'Cancelled' 
                WHERE AppointmentID = ? AND PatientID = ? AND Status = 'Booked'
            ");
            $stmt->execute([$apptId, $patientId]);

            header("Location: " . BASE_URL . "student/appointments.php?cancelled=1");
            exit();
        } catch (PDOException $e) {
            header("Location: " . BASE_URL . "student/appointments.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}
?>
