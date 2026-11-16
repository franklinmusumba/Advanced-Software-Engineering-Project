<?php 
require_once '../config/config.php'; 
require_once '../classes/Auth.php';

$auth = new Auth();
$auth->requireRole('Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Counsellor - <?= APP_NAME ?></title>
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
            <h2 style="margin-bottom: 24px;">Add New Counsellor</h2>

            <?php if(isset($_GET['error'])): ?>
                <p style="color:var(--text-red); background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem;">
                    <?= htmlspecialchars($_GET['error']) ?>
                </p>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>functions/process_add_counsellor.php" method="POST">
                
                <div style="display: flex; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Email / Username</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div style="display: flex; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Specialization</label>
                        <input type="text" name="specialization" class="form-control" placeholder="e.g. Clinical Psychology">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>License Number</label>
                        <input type="text" name="license_number" class="form-control" required>
                    </div>
                </div>

                <div style="display: flex; gap: 16px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Hire Date</label>
                        <input type="date" name="hire_date" class="form-control" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 16px;">
                    <label>Temporary Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 32px;">
                    <a href="users.php" class="btn-outline" style="text-decoration: none;">Cancel</a>
                    <button type="submit" class="btn-lime">Create Counsellor Account</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>