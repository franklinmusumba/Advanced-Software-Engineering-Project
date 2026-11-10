<?php
require_once 'config/config.php';

$email = 'admin@su.edu';
$password = 'password';

echo "<h2>Testing Database Connection & Login</h2>";

// 1. Check if user exists
$stmt = $pdo->prepare("SELECT * FROM userlogin WHERE Username = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p style='color:red;'>❌ User <strong>$email</strong> was NOT found in the database.</p>";
    echo "<p>Let's see what IS in the table:</p>";
    $all = $pdo->query("SELECT Username, Role FROM userlogin")->fetchAll();
    echo "<pre>"; print_r($all); echo "</pre>";
} else {
    echo "<p style='color:green;'>✅ User found in the database!</p>";
    echo "<p><strong>Role in DB:</strong> " . $user['Role'] . "</p>";
    echo "<p><strong>Hash in DB:</strong> " . $user['PasswordHash'] . "</p>";
    
    // 2. Check if the password matches the hash
    if (password_verify($password, $user['PasswordHash'])) {
        echo "<p style='color:green;'>✅ Password 'password' matches the hash!</p>";
        echo "<p style='color:orange;'>If you see this, your database is fine. The issue is inside <code>process_login.php</code> or <code>Auth.php</code>.</p>";
    } else {
        echo "<p style='color:red;'>❌ Password 'password' DOES NOT match the hash in the database.</p>";
    }
}
?>