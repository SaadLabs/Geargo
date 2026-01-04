<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    // Check if we are Deleting or Restoring
    $action = isset($_POST['action']) ? $_POST['action'] : 'delete';

    if ($action == 'restore') {
        // Activate
        $sql = "UPDATE product SET is_active = 1 WHERE product_id = '$product_id'";
        $msg = "Product restored successfully";
    } else {
        // Soft Delete
        $sql = "UPDATE product SET is_active = 0 WHERE product_id = '$product_id'";
        $msg = "Product deleted successfully";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: admin.php?msg=" . urlencode($msg));
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>