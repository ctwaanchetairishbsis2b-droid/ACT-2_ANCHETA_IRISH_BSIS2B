<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$product_id = $_GET['productid'];

$fetchproduct = mysqli_query($conn, "SELECT * FROM products WHERE id = '$product_id'");

if(mysqli_num_rows($fetchproduct) === 0){
    echo "
    <script>
    alert('No product found with that ID!.');
    window.location.href='products.php';
    </script>
     ";
    exit();
}

$product = mysqli_fetch_assoc($fetchproduct);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>
<body style="font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #fff0f5; color: #4a353d;">
     <div style="max-width: 400px; margin: 40px auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(219, 112, 147, 0.15); border: 1px solid #f8bbd0;">
            <h3 style="color: #880e4f; font-size: 20px; margin-bottom: 15px;">Edit Product <?php echo $product['id']; ?></h3>
            
            <form action="" method="post" style="max-width: 100%; margin: auto;">

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: #70525d; margin-bottom: 5px;">Product Name:</label>
                    <input type="text" name="product_name" required value='<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>' style="width: 100%; padding: 10px; border: 1px solid #dcbcc6; border-radius: 6px; outline: none; font-size: 14px; background: #fff; color: #4a353d;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: #70525d; margin-bottom: 5px;">Description:</label>
                    <textarea name="description" required style="width: 100%; padding: 10px; border: 1px solid #dcbcc6; border-radius: 6px; outline: none; font-size: 14px; background: #fff; color: #4a353d; min-height: 80px; font-family: inherit; resize: vertical;"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: #70525d; margin-bottom: 5px;">Quantity:</label>
                    <input type="number" name="quantity" required value='<?php echo $product['quantity']; ?>' style="width: 100%; padding: 10px; border: 1px solid #dcbcc6; border-radius: 6px; outline: none; font-size: 14px; background: #fff; color: #4a353d;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-weight: 600; font-size: 14px; color: #70525d; margin-bottom: 5px;">Price:</label>
                    <input type="number" step="0.01" name="price" required value='<?php echo $product['price']; ?>' style="width: 100%; padding: 10px; border: 1px solid #dcbcc6; border-radius: 6px; outline: none; font-size: 14px; background: #fff; color: #4a353d;">
                </div>

                <div style="margin-top: 20px;">
                    <input type="submit"
                           name="savebtn"
                           value="Save Product"
                           style="width: 100%; padding: 12px; background: #ff1493; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; transition: background 0.2s ease;">
                </div>

            </form>
     </div>
</body>
</html>

<?php
if(isset($_POST['savebtn'])){
    $name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);  
    $q = intval($_POST['quantity']);
    $price = floatval($_POST['price']);

    $updatequery = "UPDATE products SET name='$name', description='$desc', quantity='$q', price='$price' WHERE id=$product_id";
    $updatefunc = mysqli_query($conn, $updatequery);

    if($updatefunc){
        echo "
        <script>
        alert('Product saved Successfully');
        window.location.href='products.php';
        </script>
        ";
    }else{
        die("MySQL Error: " . mysqli_error($conn));
    }
} 
?>
