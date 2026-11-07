<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$step = 1;
$error = $success = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'])) {
        $username = trim($_POST['username']);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $step = 2;
        } else {
            $error = 'Username not found.';
        }
    } elseif (isset($_POST['new_password']) && isset($_POST['username_hidden'])) {
        $username = $_POST['username_hidden'];
        $newPass = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if (strlen($newPass) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($newPass !== $confirm) {
            $error = 'Passwords do not match.';
        } else {
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt->execute([$hash, $username]);
            $success = 'Password reset successfully. <a href="login.php">Login here</a>.';
            $step = 3;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AcadeMix · Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Same base styles as login */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .auth-container h1 { color: #2c3e50; margin-bottom: 10px; font-size: 32px; }
        .auth-container h1 i { color: #667eea; margin-right: 10px; }
        .auth-container h2 { color: #555; margin-bottom: 25px; font-weight: 400; }
        .input-field {
            width: 100%;
            height: 48px;
            padding: 0 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            margin-bottom: 20px;
            outline: none;
            transition: border 0.3s;
        }
        .input-field:focus { border-color: #667eea; }
        .password-wrapper { position: relative; }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 14px;
            cursor: pointer;
            color: #777;
        }
        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .auth-links { margin-top: 20px; }
        .auth-links a { color: #667eea; text-decoration: none; font-weight: 500; }
        .auth-links a:hover { text-decoration: underline; }
        .error { color: #e74c3c; margin-bottom: 15px; font-size: 14px; }
        .success { color: #27ae60; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1><i class="fas fa-graduation-cap"></i>AcadeMix</h1>
        <h2>Reset Password</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php elseif ($step == 1): ?>
        <form method="post">
            <input type="text" name="username" class="input-field" placeholder="Enter your username" required autofocus>
            <button type="submit">Continue</button>
        </form>
        <?php elseif ($step == 2): ?>
        <form method="post">
            <input type="hidden" name="username_hidden" value="<?php echo htmlspecialchars($username); ?>">
            <div class="password-wrapper">
                <input type="password" name="new_password" class="input-field" placeholder="New Password" id="new_password" required>
                <span class="toggle-password" onclick="togglePassword('new_password')"><i class="far fa-eye" id="toggleIcon1"></i></span>
            </div>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" class="input-field" placeholder="Confirm New Password" id="confirm_password" required>
                <span class="toggle-password" onclick="togglePassword('confirm_password')"><i class="far fa-eye" id="toggleIcon2"></i></span>
            </div>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
        <div class="auth-links">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
    <script>
        function togglePassword(fieldId) {
            const pwd = document.getElementById(fieldId);
            const icon = fieldId === 'new_password' ? document.getElementById('toggleIcon1') : document.getElementById('toggleIcon2');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>