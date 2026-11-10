<?php 
require_once '../config/config.php'; 
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Admin');

// Check if an ID was provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$userId = $_GET['id'];

// Fetch the user data
$stmt = $pdo->prepare("
    SELECT u.UserID, u.Username, u.Role, 
           COALESCE(p.FirstName, t.FirstName, 'Admin') as FirstName,
           COALESCE(p.LastName, t.LastName, 'User') as LastName
    FROM user_logins u
    LEFT JOIN patients p ON u.PatientID = p.PatientID
    LEFT JOIN therapists t ON u.TherapistID = t.TherapistID
    WHERE u.UserID = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <nav class="top-nav">
        <div class="nav-brand"><span>🌿</span> <?= APP_NAME ?></div>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="users.php" class="active">Manage users</a>
            <a href="reports.php">Reports</a>
        </div>
        <div class="nav-user">
            <a href="<?= BASE_URL ?>functions/process_logout.php" class="btn-logout">Log out</a>
        </div>
    </nav>

    <div class="dashboard-container" style="max-width: 600px; margin-top: 40px;">
        <div class="card card-white" style="display: block;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <h2>Edit User</h2>
                <span class="role-badge role-<?= strtolower($user['Role']) ?>" style="padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: bold;">
                    <?= htmlspecialchars($user['Role']) ?>
                </span>
            </div>

            <form action="<?= BASE_URL ?>functions/process_edit_user.php" method="POST">
                <!-- Hidden ID -->
                <input type="hidden" name="user_id" value="<?= $user['UserID'] ?>">

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($user['FirstName']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($user['LastName']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Email / Username</label>
                    <input type="email" name="username" class="form-control" value="<?= htmlspecialchars($user['Username']) ?>" required>
                </div>

                <div class="form-group">
                    <label>New Password <span style="color:var(--text-muted); font-weight:normal;">(Leave blank to keep current)</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 32px;">
                    <a href="users.php" class="btn-outline" style="text-decoration: none;">Cancel</a>
                    
                    <div style="display: flex; gap: 12px;">
                        <!-- Delete Button -->
                        <button type="submit" name="action" value="delete" class="btn-outline" style="color: var(--text-red); border-color: var(--text-red);" onclick="return confirm('Are you sure you want to permanently delete this user? This cannot be undone.');">
                            Delete User
                        </button>
                        
                        <!-- Update Button -->
                        <button type="submit" name="action" value="update" class="btn-lime">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>