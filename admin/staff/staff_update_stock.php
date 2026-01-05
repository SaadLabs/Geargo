<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if ($_SESSION['role'] !== 'staff') die("Unauthorized");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['product_id'];
    $qty = $_POST['quantity'];

    $stmt = $conn->prepare("UPDATE product SET stock_quantity = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $qty, $id);
    
    if ($stmt->execute()) {
        header("Location: staff.php?msg=Stock updated");
    } else {
        echo "Error updating stock.";
    }
}
?>