<?php
require_once __DIR__ . '/includes/header.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    }

    $name = trim($_POST['name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (empty($name) || !$email || strlen($password) < 6) {
        $errors[] = 'Please provide a valid name, email and a password (6+ chars).';
    }

    if (empty($errors)) {
        // Check unique email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, $hash, 'user']);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>

<section class="container">
    <h2>Register</h2>

    <?php if (!empty($errors)): ?>
        <div class="errors">
            <?php foreach ($errors as $err): ?>
                <p><?php echo e($err); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required minlength="6">
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>