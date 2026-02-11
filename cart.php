<?php
require_once __DIR__ . '/includes/header.php';

// Add to cart: via GET add=ID and optional qty
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    $addQty = max(1, (int)($_GET['qty'] ?? 1));
    $stmt = $pdo->prepare('SELECT id, name, price FROM foods WHERE id = ?');
    $stmt->execute([$id]);
    $food = $stmt->fetch();
    if ($food) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $addQty;
        } else {
            $_SESSION['cart'][$id] = ['id' => $id, 'name' => $food['name'], 'price' => $food['price'], 'qty' => $addQty];
        }
    }
    header('Location: cart.php');
    exit;
}

// Update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id = (int)$id;
        $qty = (int)$qty;
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
    }
}

// Checkout
if (isset($_POST['checkout'])) {
    require_login();
    if (empty($_SESSION['cart'])) {
        $error = 'Cart is empty.';
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, status, created_at) VALUES (?, ?, NOW())');
            $stmt->execute([$_SESSION['user_id'], 'Pending']);
            $orderId = $pdo->lastInsertId();
            $stmtItem = $pdo->prepare('INSERT INTO order_items (order_id, food_id, qty, price) VALUES (?, ?, ?, ?)');
            foreach ($_SESSION['cart'] as $item) {
                $stmtItem->execute([$orderId, $item['id'], $item['qty'], $item['price']]);
            }
            $pdo->commit();
            unset($_SESSION['cart']);
            header('Location: orders.php?placed=1');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Order failed.';
        }
    }
}
?>

<section class="container">
    <h2>Your Cart</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo e($error); ?></p>
    <?php endif; ?>

    <form method="POST" action="cart.php">
        <table class="cart">
            <thead><tr><th>Food</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php $total = 0; if (!empty($_SESSION['cart'])): foreach ($_SESSION['cart'] as $item): $subtotal = $item['price'] * $item['qty']; $total += $subtotal; ?>
                <tr>
                    <td><?php echo e($item['name']); ?></td>
                    <td>$<?php echo number_format($item['price'],2); ?></td>
                    <td><input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="0"></td>
                    <td>$<?php echo number_format($subtotal,2); ?></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4">Cart is empty</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr><td colspan="3" class="text-right">Total:</td><td>$<?php echo number_format($total,2); ?></td></tr>
            </tfoot>
        </table>

        <button type="submit" name="update" class="btn">Update Cart</button>
        <button type="submit" name="checkout" class="btn btn-primary">Place Order</button>
    </form>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>