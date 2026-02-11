<?php
// One-time helper: generate a password hash and optionally set admin password in DB.
// WARNING: This script is intended for local development only. Remove it after use.

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$hash = null;
$msg = null;
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $pwd = $_POST['password'] ?? '';
        if (empty($pwd)) {
            $errors[] = 'Provide a password.';
        } else {
            $hash = password_hash($pwd, PASSWORD_DEFAULT);

            if (!empty($_POST['apply']) && $_POST['apply'] === '1') {
                // Update or create admin user with email admin@example.com
                $email = 'admin@example.com';
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                if ($user) {
                    $stmt = $pdo->prepare('UPDATE users SET password = ? WHERE email = ?');
                    $stmt->execute([$hash, $email]);
                    $msg = 'Password updated for ' . e($email);
                } else {
                    $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
                    $stmt->execute(['Admin', $email, $hash, 'admin']);
                    $msg = 'Admin user created with email ' . e($email);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hash Admin Password</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;padding:20px;} .box{max-width:700px;margin:auto} label{display:block;margin-top:8px} textarea{width:100%;height:80px}</style>
</head>
<body>
<div class="box">
    <h2>Generate Admin Password Hash (One-time)</h2>
    <p><strong>Note:</strong> This tool is for local development. Remove this file after use for security.</p>

    <?php if (!empty($errors)): foreach ($errors as $e): ?><p style="color:red"><?php echo e($e); ?></p><?php endforeach; endif; ?>
    <?php if ($msg): ?><p style="color:green"><?php echo e($msg); ?></p><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Plain password</label>
        <input type="text" name="password" required>
        <label><input type="checkbox" name="apply" value="1"> Update / create admin user <code>admin@example.com</code> with this password</label>
        <br>
        <button type="submit">Generate Hash</button>
    </form>

    <?php if ($hash): ?>
        <h3>Resulting Hash</h3>
        <textarea readonly><?php echo e($hash); ?></textarea>
        <p>You can now copy this hash into <code>sql/schema.sql</code> or use it to update your database.</p>
    <?php endif; ?>

    <p style="margin-top:20px">After use, remove this file: <code>admin/hash_password.php</code></p>
</div>
</body>
</html>