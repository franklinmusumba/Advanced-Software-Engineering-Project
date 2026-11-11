<?php 
require_once '../config/config.php'; 
require_once '../classes/Auth.php';

// 1. Enforce Admin Role
$auth = new Auth();
$auth->requireRole('Admin');

// 2. Fetch all users from the database
try {
    $query = "
        SELECT 
            u.UserID, 
            u.Username, 
            u.Role, 
            u.LastLogin,
            COALESCE(p.FirstName, t.FirstName, 'Admin') as FirstName,
            COALESCE(p.LastName, t.LastName, 'User') as LastName
        FROM user_logins u
        LEFT JOIN patients p ON u.PatientID = p.PatientID
        LEFT JOIN therapists t ON u.TherapistID = t.TherapistID
        ORDER BY u.Role ASC, u.UserID DESC
    ";
    $stmt = $pdo->query($query);
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <style>
        /* Small style to make the role badges look nice */
        .role-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .role-admin { background-color: #e2dcf5; color: #1c2b59; }
        .role-patient { background-color: #e7f3d3; color: #1c2b59; }
        .role-therapist { background-color: #abcbf3; color: #1c2b59; }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav">
        <div class="nav-brand">
            <span>🌿</span> <?= APP_NAME ?>
        </div>
        <div class="nav-links">
            <a href="dashboard.php">Overview</a>
            <a href="users.php" class="active">Manage users</a>
            <a href="reports.php">Reports</a>
        </div>
        <div class="nav-user">
            <div class="avatar">JA</div>
            <div class="user-info">
                <span class="name">Jordan Admin</span>
                <span class="role">Admin</span>
            </div>
            <a href="<?= BASE_URL ?>functions/process_logout.php" class="btn-logout">Log out</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="dashboard-container">
        
        <div class="page-header" style="margin-top: 20px;">
            <div>
                <h1>Manage Users</h1>
                <p class="text-muted" style="margin-top: 8px;">View and manage all students, counsellors, and admins.</p>
            </div>
            <!-- If you want an "Add User" button, it would go here -->
            <a href="dashboard.php" class="btn-outline" style="background: white;">← Back to Overview</a>
        </div>
        <?php if(isset($_GET['msg'])): ?>
    <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-top: 20px; font-weight: 500;">
        <?php 
            if($_GET['msg'] == 'updated') echo "✅ User details successfully updated.";
            if($_GET['msg'] == 'deleted') echo "🗑️ User successfully deleted.";
        ?>
    </div>
<?php endif; ?>

        <!-- Users Table -->
        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email (Username)</th>
                        <th>Role</th>
                        <th>Last Login</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 32px;">
                                No users found in the system.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($users as $user): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></strong></td>
                                <td><?= htmlspecialchars($user['Username']) ?></td>
                                <td>
                                    <span class="role-badge role-<?= strtolower($user['Role']) ?>">
                                        <?= htmlspecialchars($user['Role']) ?>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?= $user['LastLogin'] ? date('M j, Y g:i A', strtotime($user['LastLogin'])) : 'Never' ?>
                                </td>
                                <td>
                                    <a href="edit_user.php?id=<?= $user['UserID'] ?>" class="btn-outline" style="padding: 6px 12px; font-size: 0.75rem; text-decoration: none; display: inline-block;">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>