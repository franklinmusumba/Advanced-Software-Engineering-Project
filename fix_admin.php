<?php
require_once 'config/config.php';

$email = 'admin@su.edu';
$newPassword = 'password';

// 1. Generate a REAL hash using your PHP version
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

try {
    // 2. Update the database with the real hash
    $stmt = $pdo->prepare("UPDATE userlogin SET PasswordHash = ? WHERE Username = ?");
    $stmt->execute([$newHash, $email]);
    
    echo "<h2>✅ Success!</h2>";
    echo "<p>Your admin password has been securely reset to: <strong>password</strong></p>";
    echo "<p><strong>New Hash in Database:</strong> " . $newHash . "</p>";
    echo "<br><a href='auth/login.php' style='background:#bce344; padding:10px 20px; text-decoration:none; color:#1c2b59; font-weight:bold; border-radius:20px;'>Go to Login Page and Try Again</a>";
    
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>