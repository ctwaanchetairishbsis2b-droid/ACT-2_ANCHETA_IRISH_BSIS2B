<?php
require_once "config.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uname = trim($_POST['uname']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $uname);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    if ($user) {

        if ($password === $user['password']) {

            if ($user['status'] != "approved") {

                $errors[] = "Your account has not been approved by the admin.";

            } else {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            }

        } else {
            $errors[] = "Invalid username or password.";
        }

    } else {
        $errors[] = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="index.css">
<title>Sign In</title>
</head>
<body>

<div class="container">
    <div class="card">

        <h2>Welcome Back</h2>

        <?php foreach($errors as $e): ?>
            <div class="error"><?= h($e) ?></div>
        <?php endforeach; ?>

        <form method="POST">

            <div class="input-group">
                <label>Username</label>
                <input type="text" name="uname" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn">
                Sign In
            </button>

        </form>

        <div class="link">
            No account?
            <a href="register.php">Register</a>
        </div>

    </div>
</div>

</body>
</html>