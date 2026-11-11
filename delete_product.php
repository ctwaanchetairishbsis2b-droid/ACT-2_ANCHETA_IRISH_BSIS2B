<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");

}

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    
}
    
    $productid = $_GET['productid'];

    $deleteproduct = mysqli_query($conn, "DELETE FROM products WHERE id = '$productid'");

    if ($deleteproduct) {

    echo "
    <script>
     alert('Deleted successfully!');

        window.location.href='products.php';
        
    </script>
       ";
        
    } 
    else {

        echo "
        <script>
        alert('Something went wrong. Please try again later.');
        
        window.location.href='products.php';
       
        </script>
         ";
    }  
    
?>