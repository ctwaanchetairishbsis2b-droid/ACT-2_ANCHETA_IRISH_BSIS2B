<?php
require_once 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: daashboard.php");
    exit();
}

$totaluser = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'] ?? 0;
$totalpending = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status = 'pending'")->fetch_assoc()['total'] ?? 0;
$totalproduct = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'] ?? 0;

if (isset($_POST['addproductbtn'])) {
    $name   = mysqli_real_escape_string($conn, trim($_POST['product_name']));
    $desc   = mysqli_real_escape_string($conn, trim($_POST['description']));
    $q      = intval($_POST['quantity']);
    $price  = floatval($_POST['price']);
    $image  = mysqli_real_escape_string($conn, trim($_POST['image'] ?: 'img/fashion.jpg'));
    $userid = (int) $_SESSION['user_id'];

    $insertquery = "INSERT INTO products (name, description, quantity, price, image, user_id) 
                    VALUES ('$name', '$desc', '$q', '$price', '$image', '$userid')";

    if (mysqli_query($conn, $insertquery)) {
        echo "<script>alert('Product saved successfully!'); window.location.href = 'manage_products.php';</script>";
        exit();
        
    } else {
        die(mysqli_error($conn));
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="products.css"> 
    <title>PinkSpace - Manage Products</title>
</head>
<body>

    <nav class="navbar">
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="manage_products.php" class="nav-link active">Manage Products</a>
            <a href="products.php" class="nav-link">User Orders</a>
            <a href="users.php" class="nav-link">User Management</a>
        </div>
        <a href="index.php" class="nav-link logout">Logout</a>
    </nav>

    <main class="container">
        <h1 class="welcome-heading">
            Welcome, <span><?php echo htmlspecialchars($_SESSION['role']); ?></span>!
        </h1>

        <div class="stats-grid">
            <div class="card card-pink-dark">
                <h3>Total Users</h3>
                <h1><?php echo $totaluser; ?></h1>
            </div>

            <div class="card card-pink-rose">
                <h3>Not Approved Users</h3>
                <h1><?php echo $totalpending; ?></h1>
            </div>

            <div class="card card-pink-bright">
                <h3>Total Products</h3>
                <h1><?php echo $totalproduct; ?></h1>
            </div>
        </div>

        <div class="dashboard-layout admin-view">
            <section class="form-section">
                <div class="card-header">
                    <h3>Add New Product</h3>
                </div>

                <form action="manage_products.php" method="post" class="product-form">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Image Path (e.g. img/fashion.jpg)</label>
                        <input type="text" name="image" value="img/fashion.jpg" required>
                    </div>

                    <div class="form-group-row">
                        <div class="form-group">
                            <label>Quantity (Stock)</label>
                            <input type="number" name="quantity" min="0" required>
                        </div>

                        <div class="form-group">
                            <label>Price (₱)</label>
                            <input type="number" step="0.01" name="price" min="0" required>
                        </div>
                    </div>

                    <input type="submit" name="addproductbtn" value="Add Product" class="btn-submit">
                </form>
            </section>
            
            <section class="table-section">
                <div class="card-header">
                    <h3>Product Inventory List</h3>
                </div>
                <div class="table-wrapper">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product Name</th>
                                <th>Description</th>
                                <th>Stock</th>
                                <th>Price</th>  
                                <th>Date Saved</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php
                            $fetchproducts = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                            if (mysqli_num_rows($fetchproducts) > 0) {
                                while ($product = mysqli_fetch_assoc($fetchproducts)) {
                            ?>
                                <tr>
                                    <td><strong>#<?php echo $product['id']; ?></strong></td>
                                    <td class="text-bold"><?php echo htmlspecialchars($product['name']); ?></td>

                                    <td class="text-muted"><?php echo htmlspecialchars($product['description']); ?></td>

                                    <td><span class="badge-qty"><?php echo $product['quantity']; ?></span></td>

                                    <td class="text-pink">₱<?php echo number_format($product['price'], 2); ?></td>

                                    <td class="text-small"><?php echo $product['created_at']; ?></td>
                                    <td class="actions-cell">

                                        <button class="btn-action btn-edit"
                                            onclick="window.location.href='edit_product.php?productid=<?php echo $product['id']; ?>'">
                                            Edit
                                        </button>

                                        <button class="btn-action btn-delete"
                                            onclick="if(confirm('Are you sure you want to delete this product?')) window.location.href='delete_product.php?productid=<?php echo $product['id']; ?>'">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                            <?php

                                }
                            } else {

                            ?>
                                <tr>
                                    <td colspan="7" class="no-records">No inventory records found.</td>
                                </tr>
                            <?php } ?>
                            
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>

</body>
</html>
