<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$username = $_SESSION['username'] ?? 'User';

$totaluser = 0;
$totalpending = 0;
$totalproduct = 0;

if (isset($conn) && $conn) {
    $result_user = $conn->query("SELECT COUNT(*) AS total FROM users");
    if ($result_user) {
        $totaluser = $result_user->fetch_assoc()['total'];
    }

    $result_pending = $conn->query("SELECT COUNT(*) AS total FROM users WHERE status='pending'");
    if ($result_pending) {
        $totalpending = $result_pending->fetch_assoc()['total'];
    }

    $result_product = $conn->query("SELECT COUNT(*) AS total FROM products");
    if ($result_product) {
        $totalproduct = $result_product->fetch_assoc()['total'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinkSpace - Admin Management</title>
    <style>
    
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #fff0f5; 
            color: #4a353d; 
            line-height: 1.5;
            padding-bottom: 40px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 40px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            height: 70px; 
        }

        .logo {
            display: none; 
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 30px;
            width: 100%;
        }

        .nav-right a {
            text-decoration: none;
            color: #666666;
            font-weight: 500;
            font-size: 15px;
            transition: color 0.2s ease;
            padding: 8px 0; 
            display: inline-block;
        }

        .nav-right a:hover {
            color: #d63384;
        }

        .nav-right a.active-link {
            color: #d63384;
            font-weight: 600;
            border-bottom: 2px solid #d63384;
        }

        .user-greeting {
            color: #666666;
            font-weight: 500;
            font-size: 14px;
            margin-left: auto; 
            display: inline-block;
        }

        .logout-btn {
            text-decoration: none;
            background: transparent;
            color: #d63384 !important;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 18px;
            border-radius: 4px;
            border: 1px solid #d63384;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .logout-btn:hover {
            background-color: #c2185b;
    color: white;
            
        }

.nav-right a.active-link {
    color: #d63384;
    font-weight: 600;
    border-bottom: 2px solid #d63384;
    padding-bottom: 4px;
}

.user-greeting {
    color: #666666;
    font-weight: 500;
    font-size: 14px;
    margin-left: auto; 
}

.logout-btn {
    text-decoration: none;
    background: transparent;
    color: #d63384 !important;
    font-weight: 500;
    font-size: 14px;
    padding: 6px 18px;
    border-radius: 4px;
    border: 1px solid #d63384;
    transition: all 0.2s ease;
}

.logout-btn:hover {
    background: #fff5f8;
    color: #880e4f !important;
    border-color: #880e4f;
}

        .btn-action {
            padding: 7px 16px;
            border: 1px solid transparent;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }

        .btn-approve { 
            background-color: #e3f2fd; 
            color: #0d47a1;
            border: 1px solid #bbdefb;
        }
        .btn-approve:hover { 
            background-color: #0d47a1; 
            color: white; 
        }

        .btn-disable { 
            background-color: #fff0f3; 
            color: #d32f2f;
            border: 1px solid #ffccd5;
        }
        .btn-disable:hover { 
            background-color: #d32f2f; 
            color: white; 
        }

        .welcome-title {
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 4px;
            color: #3a222c;
            margin-top: 10px;
        }

        .welcome-subtitle {
            color: #8c737d;
            font-size: 15px;
            margin-bottom: 35px;
        }

        .section-title {
            font-size: 22px;
            color: #880e4f;
            font-weight: 600;
            margin: 25px 0 15px 0;
            letter-spacing: 0.3px;
        }

        .table-wrapper {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(219, 112, 147, 0.06);
            border: 1px solid #f8bbd0;
            margin-bottom: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        table th,
        table td {
            padding: 16px 20px;
            border-bottom: 1px solid #f8bbd0;
            vertical-align: middle;
        }

        table th {
            background: #fff5f8;
            color: #880e4f;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody tr:hover {
            background-color: #fffbfd;
        }

        table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-status.approved {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-status.pending {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .badge-role {
            background-color: #fce4ec;
            color: #c2185b;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
        }

        .no-user {
            text-align: center;
            padding: 45px !important;
            font-size: 16px;
            color: #a68d97;
            font-style: italic;
        }

        .analytics-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(219, 112, 147, 0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(219, 112, 147, 0.25);
        }

        .card h2 {
            font-size: 38px;
            font-weight: 700;
            margin-top: 8px;
            letter-spacing: 0px;
        }

        .users-card {
            background: linear-gradient(135deg, #880e4f, #ad1457);
        }

        .pending-card {
            background: linear-gradient(135deg, #d81b60, #ec407a);
        }

        .products-card {
            background: linear-gradient(135deg, #ff1493, #ff69b4);
        }
    </style>
</head>
<body>


    <nav class="navbar">
        <div class="logo">PinkSpace</div>

        <div class="nav-right">
            <span class="user-greeting">
                Hello, <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="users.php" class="active-link">User Management</a>
            
        </div>
    </nav>

    <main class="container">
    
    <div>
        <h2 class="section-title">System Insights</h2>
        
        <div class="analytics-container">
            <div class="card users-card">
                Total Users
                <h2><?php echo $totaluser; ?></h2>
            </div>

            <div class="card pending-card">
                Not Approved Users
                <h2><?php echo $totalpending; ?></h2>
            </div>

            <div class="card products-card">
                Total Products
                <h2><?php echo $totalproduct; ?></h2>
            </div>
        </div>

        <h2 class="section-title">User Registry</h2>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $fetchquery = "SELECT * FROM users";
                    $fetch = mysqli_query($conn, $fetchquery);

                    if (mysqli_num_rows($fetch) > 0) {
                        while ($user = mysqli_fetch_assoc($fetch)) {
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                            <td>
                                <span class="badge-status <?php echo $user['status']; ?>">
                                    <?php echo htmlspecialchars($user['status']); ?>
                                </span>
                            </td>

                            <td><span class="badge-role"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td><?php echo $user['created_at']; ?></td>
                            <td>
                                <?php if ($user['status'] == 'pending') : ?>
                                    <button class="btn-action btn-approve" onclick="window.location.href='approve_user.php?userid=<?php echo $user['id']; ?>'">
                                        Approve
                                    </button>

                                <?php elseif ($user['status'] == 'approved') : ?>
                                    <button class="btn-action btn-disable" onclick="window.location.href='disable_user.php?userid=<?php echo $user['id']; ?>'">
                                        Disable
                                    </button>

                                <?php else: ?>
                                    <span class="text-muted">No Actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="5" class="no-user">
                                No User Found.
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </main>

</body>
</html>
