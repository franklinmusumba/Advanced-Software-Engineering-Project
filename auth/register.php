<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <!-- Left Side -->
        <div class="login-left" style="background-color: var(--card-purple);">
            <div class="brand">
                <span>🌿</span> <?= APP_NAME ?>
            </div>
            <div>
                <h1>Start your journey to better well-being.</h1>
                <p>Create an account to book sessions and track your mood securely.</p>
            </div>
            <div></div>
        </div>

        <!-- Right Side (Form) -->
        <div class="login-right">
            <div class="login-form-container">
                <h2>Create an account</h2>
                <p>Join <?= APP_NAME ?> today.</p>

                <?php if(isset($_GET['error'])): ?>
                    <p style="color:var(--text-red); background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem;">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </p>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>functions/process_register.php" method="POST">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>University Email</label>
                        <input type="email" name="email" class="form-control" placeholder="name@su.edu" required>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn-lime" style="width: 100%; margin-top: 10px;">Register</button>
                </form>

                <p style="text-align: center; margin-top: 24px; font-size: 0.875rem; color: var(--text-muted);">
                    Already have an account? <a href="login.php" style="color: var(--slot-selected); font-weight: 600; text-decoration: none;">Log in here</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>