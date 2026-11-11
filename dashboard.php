<?php
require_once 'config.php';

if (!isset($_SESSION['role']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id  = (int) $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$role     = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_now']) && $role !== 'admin') {
    $product_id    = (int) $_POST['product_id'];
    $product_name  = trim($_POST['product_name']);
    $product_image = trim($_POST['product_image']);
    $price         = (float) $_POST['price'];
    $quantity      = max(1, (int) ($_POST['quantity'] ?? 1));
    $total_price   = $price * $quantity;

    $stockCheck = $conn->query("SELECT quantity FROM products WHERE id = $product_id")->fetch_assoc();
    if ($stockCheck && $stockCheck['quantity'] >= $quantity) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, username, product_name, product_image, price, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdid", $user_id, $username, $product_name, $product_image, $price, $quantity, $total_price);

        if ($stmt->execute()) {
            $conn->query("UPDATE products SET quantity = quantity - $quantity WHERE id = $product_id");
            $stmt->close();
            echo "<script>alert('Successfully purchased " . $quantity . "x " . htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') . "!'); window.location.href='products.php';</script>";
            exit();
        }
        $stmt->close();
    } else {
        echo "<script>alert('Insufficient stock available!');</script>";
    }
}

$productsQuery = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinkSpace - Dashboard</title>
    <link rel="stylesheet" href="dashboard.css?v=<?= time(); ?>">
</head>
<body>

    <nav class="navbar">
        <div class="logo">PinkSpace</div>

        <div class="search-box">
            <input type="text" id="searchInput" onkeyup="searchProducts()" placeholder="Search inspiration...">
        </div>

        <div class="nav-right">
            <span class="user-greeting">
                Hello, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>
            </span>

            <a href="daashboard.php" class="nav-link active">Dashboard</a>

            <?php if ($role === 'admin'): ?>
                <a href="manage_products.php" class="nav-link">Manage Products</a>
                <a href="products.php" class="nav-link">User Orders</a>
                <a href="users.php" class="nav-link">User Management</a>
            <?php else: ?>
                <a href="products.php" class="nav-link">My Products</a>
            <?php endif; ?>

            <a href="index.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div class="section-title-wrapper">
            <h2 class="section-main-title">Your Pink Paradise</h2>
            <hr class="section-divider">
        </div>

        <section class="gallery" id="productGallery">
            <?php if ($productsQuery && mysqli_num_rows($productsQuery) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($productsQuery)): ?>
                    <div class="card">
                        <img src="<?= htmlspecialchars($item['image'] ?: 'img/fashion.jpg', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                        <h3><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="item-price">₱<?= number_format($item['price'], 2); ?></p>
                        <small class="item-meta">
                            Stock: <?= (int) $item['quantity']; ?> | <?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?>
                        </small>

                        <?php if ($role !== 'admin'): ?>
                            <?php if ((int) $item['quantity'] > 0): ?>
                                <form method="POST" class="buy-form">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id']; ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="product_image" value="<?= htmlspecialchars($item['image'] ?: 'img/fashion.jpg', ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="price" value="<?= htmlspecialchars($item['price'], ENT_QUOTES, 'UTF-8'); ?>">

                                    <div class="qty-row">
                                        <label>Qty:</label>
                                        <input type="number" name="quantity" value="1" min="1" max="<?= (int) $item['quantity']; ?>" class="qty-input" required>
                                    </div>

                                    <button type="submit" name="buy_now" class="buy-now-btn" onclick="return confirm('Confirm purchase of <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>?')">
                                        Buy Now
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="purchased-badge">Out of Stock</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-orders">No products available yet.</p>
            <?php endif; ?>
        </section>
    </main>

    <script>
        function searchProducts() {
            const filter = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('#productGallery .card');

            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                card.style.display = title.includes(filter) ? '' : 'none';
            });
        }
    </script>

</body>
</html>
