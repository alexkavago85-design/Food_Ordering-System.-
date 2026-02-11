<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    if (!$email || empty($password)) $errors[] = 'Provide credentials.';
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id, password, role, name FROM users WHERE email = ? AND role = ? LIMIT 1');
        $stmt->execute([$email, 'admin']);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid admin login.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title></head>
<body>
    <h2>Admin Login</h2>
    <?php if (!empty($errors)): foreach ($errors as $e) echo '<p>'.e($e).'</p>'; endif; ?>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Email</label><input type="email" name="email" required>
        <label>Password</label><input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>