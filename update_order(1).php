<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
    $order_id = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? 'Pending';

    $stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
    $stmt->execute([$status, $order_id]);
}
header('Location: dashboard.php');
exit;