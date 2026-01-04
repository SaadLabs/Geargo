<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['product_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category_id = $_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $price = $_POST['price'];
    $stock = $_POST['quantity'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);

    // 1. Check if a new image was uploaded
    if (!empty($_FILES["product_image"]["name"])) {
        $targetDir = "../../assets/products/";
        $fileName = basename($_FILES["product_image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $dbImagePath = "assets/products/" . $fileName;

        move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath);

        // Update with new image
        $sql = "UPDATE product SET 
                title='$title', category_id='$category_id', brand='$brand', color='$color', 
                price='$price', stock_quantity='$stock', description='$desc', image='$dbImagePath' 
                WHERE product_id='$id'";
    } else {
        // Update WITHOUT changing the image
        $sql = "UPDATE product SET 
                title='$title', category_id='$category_id', brand='$brand', color='$color', 
                price='$price', stock_quantity='$stock', description='$desc' 
                WHERE product_id='$id'";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php?msg=Product updated successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>