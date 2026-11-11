<?php
session_start();
require_once 'config.php';


if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");

}

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    
}
    $userid = $_GET['userid'];

    $approveuser = mysqli_query($conn, "UPDATE users SET status = 'approved' WHERE id = '$userid'");

    if ($approveuser) {

    echo "
    <script>
     alert('User is approved');

        window.location.href='users.php';
        
    </script>
       ";
        
    } 
    else {

        echo "
        <script>
        alert('Something went wrong. Please try again later.');
        
        window.location.href='users.php';
       
        </script>
         ";
    }
        
?>