
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['role']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #ffffff;">

    <!-- Navigation Bar -->
    <div style="display: flex; gap: 20px; margin-bottom: 30px; font-size: 18px;">
        <a href="dashboard.php" style="text-decoration: none; color: #000000;">Dashboard</a>
        <a href="products.php" style="text-decoration: none; color: #000000;">Products</a>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="users.php" style="text-decoration: none; color: #000000;">User Management</a>
        <?php endif; ?>
        
        <a href="index.php" style="text-decoration: none; color: #000000;">Logout</a>
    </div>

    <!-- Welcome Heading -->
    <h1 style="font-size: 32px; font-weight: bold; margin-bottom: 30px;">Welcome!</h1>

    <!-- Cards Container -->
    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
        
        <!-- Total of Users Box -->
        <div style="background-color: blue; color: white; padding: 30px 20px; width: 250px; text-align: center; font-size: 18px; font-weight: bold; border: 1px solid #000;">
            Total of Users
        </div>

        <!-- Total of Not Approved Users Box -->
        <div style="background-color: orange; color: white; padding: 30px 20px; width: 280px; text-align: center; font-size: 18px; font-weight: bold; border: 1px solid #000;">
            Total of Not Approved Users
        </div>

        <!-- Total of Products Box -->
        <div style="background-color: green; color: white; padding: 30px 20px; width: 250px; text-align: center; font-size: 18px; font-weight: bold; border: 1px solid #000;">
            Total of Products
        </div>

    </div>

</body>
</html>