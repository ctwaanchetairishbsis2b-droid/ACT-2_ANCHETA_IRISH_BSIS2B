<?php
require_once 'config.php';

if (!isset($_SESSION['role']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id  = (int) $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role     = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order']) && $role !== 'admin') {
    $order_id = (int) $_POST['order_id'];

    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $user_id);
    if ($stmt->execute()) {
        $stmt->close();

        echo "<script>alert('Order cancelled and removed.'); window.location.href='products.php';</script>";
        exit();
    }

    $stmt->close();
}

if ($role === 'admin') {
    $query = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
    $ordersList = [];
    $totalSales = 0;
    while ($r = mysqli_fetch_assoc($query)) {
        $ordersList[] = $r;
        $totalSales += (float) $r['total_price'];
    }
} else {
    $query = mysqli_query($conn, "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinkSpace - <?= ($role === 'admin') ? 'User Orders' : 'My Products'; ?></title>
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
</head>
<body>

    <nav class="navbar">
        <div class="logo">PinkSpace</div>

        <div class="nav-right">
            <span class="user-greeting">
                Hello, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
            </span>

            <a href="dashboard.php" class="nav-link">Dashboard</a>

            <?php if ($role === 'admin'): ?>
                <a href="manage_products.php" class="nav-link">Manage Products</a>
                <a href="products.php" class="nav-link active">User Orders</a>
                <a href="users.php" class="nav-link">User Management</a>

            <?php else: ?>
                <a href="products.php" class="nav-link active">My Products</a>
            <?php endif; ?>

            <a href="index.php" class="logout-btn">Log Out</a>
        </div>
    </nav>

    <main class="container">

        <div class="section-title-wrapper">
            <h2 class="section-main-title">
                <?= ($role === 'admin') ? 'All Purchased Products by Users' : 'My Purchased Products'; ?>
            </h2>
            <hr class="section-divider">
        </div>

        <?php if ($role === 'admin'): ?>
            <div class="admin-summary">
                <div class="summary-pill">
                    <span>Total Orders:</span>
                    <strong><?= count($ordersList) ?></strong>
                </div>
                <div class="summary-pill">
                    <span>Total Sales:</span>
                    <strong>₱<?= number_format($totalSales, 2) ?></strong>
                </div>
            </div>

            <div class="orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Buyer (User)</th>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Date Purchased</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($ordersList) > 0): ?>
                            <?php foreach ($ordersList as $row): ?>
                                <tr>
                                    <td>#<?= (int) $row['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ?></strong></td>

                                    <td class="product-cell">
                                        <img src="<?= htmlspecialchars($row['product_image'], ENT_QUOTES, 'UTF-8') ?>" alt="Item">
                                        <span><?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </td>

                                    <td>₱<?= number_format($row['price'], 2) ?></td>
                                    <td><?= (int) $row['quantity'] ?>x</td>
                                    <td class="price-col">₱<?= number_format($row['total_price'], 2) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($row['ordered_at'])) ?></td>
                                </tr>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="empty-orders">No orders from users yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <section class="gallery">
                <?php if ($query && mysqli_num_rows($query) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($query)): ?>
                        <div class="card">
                            <img src="<?= htmlspecialchars($row['product_image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?>">
                            <h3><?= htmlspecialchars($row['product_name'], ENT_QUOTES, 'UTF-8') ?></h3>\
                            
                            <p class="item-price">
                                ₱<?= number_format($row['total_price'], 2) ?>
                                <span class="qty-tag">(<?= (int) $row['quantity'] ?>x ₱<?= number_format($row['price'], 2) ?>)</span>
                            </p>
                            <span class="purchased-badge">Purchased • <?= date('M d, Y', strtotime($row['ordered_at'])) ?></span>

                            <form method="POST" class="buy-form">
                                <input type="hidden" name="order_id" value="<?= (int) $row['id'] ?>">
                                <button type="submit" name="cancel_order" class="cancel-order-btn" onclick="return confirm('Cancel and remove this order?')">
                                    Cancel Order
                                </button>
                            </form>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-orders">You haven't bought any products yet. Go back to <a href="dashboard.php">Dashboard</a> to shop!</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

    </main>

</body>
</html>
