<?php
require_once "config.php";

$mess = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uname = trim($_POST['uname']);
    $password = trim($_POST['password']);
    $password2 = trim($_POST['password2']);

    if ($password !== $password2) {
        $errors[] = "Passwords do not match.";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $uname);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "Username already exists.";
        } else {

            $status = "pending";
            $role = "user";

            $stmt = $conn->prepare("INSERT INTO users (username,password,status,role) VALUES (?,?,?,?)");
            $stmt->bind_param("ssss", $uname, $password, $status, $role);

            if ($stmt->execute()) {
                $mess[] = "Registered successfully. Please wait for admin approval.";
            } else {
                $errors[] = "Registration failed.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="index.css">
<title>Register</title>
</head>
<body>

<div class="container">
    <div class="card">

        <h2>Create Account</h2>

        <?php foreach($mess as $m): ?>
            <div class="message"><?= h($m) ?></div>
        <?php endforeach; ?>

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

            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="password2" required>
            </div>

            <button type="submit" class="btn">
                Create Account
            </button>

        </form>

        <div class="link">
            Already have an account?
            <a href="index.php">Sign In</a>
        </div>

    </div>
</div>

</body>
</html>