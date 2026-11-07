<?php require_once '../config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-left">
            <div class="brand"><span>🌿</span> <?= APP_NAME ?></div>
            <div>
                <p class="text-uppercase" style="color: var(--primary-navy); margin-bottom: 10px;">A Private Space For You</p>
                <h1>Welcome back. Take a breath.</h1>
                <p>Your appointments, mood check-ins and support — all in one calm place.</p>
            </div>
            <div></div>
        </div>

        <div class="login-right">
            <div class="login-form-container">
                <p class="text-uppercase" style="color: var(--primary-navy); margin-bottom: 8px;">Sign In</p>
                <h2>Good to see you again</h2>
                <p>Enter your university email to continue.</p>

                <?php if(isset($_GET['error'])): ?>
                    <p style="color:var(--text-red); background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem;">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </p>
                <?php endif; ?>
                <?php if(isset($_GET['registered'])): ?>
                    <p style="color:#166534; background: #dcfce7; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem;">
                        Registration successful! Please log in.
                    </p>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>functions/process_login.php" method="POST">
                    <div class="form-group">
                        <label>University email</label>
                        <input type="email" name="email" class="form-control" placeholder="name@su.edu" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn-lime" style="width: 100%; margin-top: 10px;">Log in</button>
                </form>

                <p style="text-align: center; margin-top: 24px; font-size: 0.875rem; color: var(--text-muted);">
                    New to <?= APP_NAME ?>? <a href="register.php" style="color: var(--slot-selected); font-weight: 600; text-decoration: none;">Create an account</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>