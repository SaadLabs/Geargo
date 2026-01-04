<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

// Security: Admin Only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category_id = $_POST['category_id'];
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $color = mysqli_real_escape_string($conn, $_POST['color']);
    $price = $_POST['price'];
    $stock = $_POST['quantity']; // Form input name is 'quantity'
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $is_active = 1;
    $user_id = $_SESSION['user_id'];

    // --- Image Upload Logic ---
    $targetDir = "../../assets/products/added/"; // Make sure this folder exists!
    $fileName = rand() . basename($_FILES["product_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $dbImagePath = "assets/products/added/" . $fileName; // Path to save in DB

    // Move file
    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFilePath)) {
        // Insert Query
        $sql = "INSERT INTO product (title, `description`, brand, color, price, stock_quantity, is_active, `image`, category_id, created_by) 
        VALUES ('$title', '$desc', '$brand', '$color', '$price', '$stock', '$is_active', '$dbImagePath', '$category_id', '$user_id')";

        if (mysqli_query($conn, $sql)) {
            header("Location: admin.php?msg=Product added successfully");
        } else {
            echo "Database Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error uploading image.";
    }
}
?>