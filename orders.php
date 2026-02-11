<?php
require_once __DIR__ . '/includes/header.php';
require_login();

// For admin view all orders
if (is_admin()) {
    $stmt = $pdo->query('SELECT o.*, u.name as user_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY created_at DESC');
    $orders = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll();
}

?>

<section class="container">
    <h2>Orders</h2>

    <?php if (!empty($orders)): ?>
        <table>
            <thead><tr><th>ID</th><th>User</th><th>Status</th><th>Created</th><th>Items</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo e($order['id']); ?></td>
                        <td><?php echo e($order['user_name'] ?? ($_SESSION['name'] ?? 'You')); ?></td>
                        <td><?php echo e($order['status']); ?></td>
                        <td><?php echo e($order['created_at']); ?></td>
                        <td>
                            <?php
                            $stmt = $pdo->prepare('SELECT oi.*, f.name FROM order_items oi JOIN foods f ON f.id = oi.food_id WHERE oi.order_id = ?');
                            $stmt->execute([$order['id']]);
                            $items = $stmt->fetchAll();
                            foreach ($items as $it) {
                                echo e($it['name']) . ' x ' . (int)$it['qty'] . '<br>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if (is_admin()): ?>
                                <form method="POST" action="admin/update_order.php" style="display:inline">
                                    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status">
                                        <option <?php echo $order['status']=='Pending'?'selected':''; ?>>Pending</option>
                                        <option <?php echo $order['status']=='Preparing'?'selected':''; ?>>Preparing</option>
                                        <option <?php echo $order['status']=='Delivered'?'selected':''; ?>>Delivered</option>
                                    </select>
                                    <button type="submit" class="btn">Update</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No orders yet.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>