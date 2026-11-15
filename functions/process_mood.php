<?php
require_once '../config/config.php';
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Patient');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patientId     = $_SESSION['patient_id'] ?? null;
    $moodScore     = isset($_POST['mood_score']) ? (int)$_POST['mood_score'] : 0;
    $energyLevel   = (isset($_POST['energy_level']) && $_POST['energy_level'] !== '') ? (int)$_POST['energy_level'] : null;
    $sleepHours    = (isset($_POST['sleep_hours']) && $_POST['sleep_hours'] !== '') ? round((float)$_POST['sleep_hours'], 1) : null;
    $triggerEvents = mb_substr(trim($_POST['trigger_events'] ?? ''), 0, 200);
    $notes         = mb_substr(trim($_POST['notes'] ?? ''), 0, 255);

    if (!$patientId || $moodScore < 1 || $moodScore > 5) {
        header("Location: " . BASE_URL . "student/mood.php?error=Please select a valid mood score between 1 and 5.");
        exit();
    }

    try {
        $stmt = $pdo->prepare("
            INSERT INTO moodtrackers (PatientID, MoodDate, MoodScore, EnergyLevel, SleepHours, TriggerEvents, Notes)
            VALUES (:patient_id, CURDATE(), :mood_score, :energy_level, :sleep_hours, :trigger_events, :notes)
        ");
        $stmt->execute([
            ':patient_id'     => $patientId,
            ':mood_score'     => $moodScore,
            ':energy_level'   => $energyLevel,
            ':sleep_hours'    => $sleepHours,
            ':trigger_events' => $triggerEvents !== '' ? $triggerEvents : null,
            ':notes'          => $notes !== '' ? $notes : null
        ]);

        header("Location: " . BASE_URL . "student/history.php?saved=1");
        exit();
    } catch (PDOException $e) {
        header("Location: " . BASE_URL . "student/mood.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>
