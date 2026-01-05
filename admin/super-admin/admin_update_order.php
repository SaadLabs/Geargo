<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if ($_SESSION['role'] !== 'admin') die("Unauthorized");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status']; // 'Shipped' or 'Delivered'

    $stmt = $conn->prepare("UPDATE `order` SET order_status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    
    if ($stmt->execute()) {
        header("Location: admin.php?msg=Order status updated");
    } else {
        echo "Error updating order.";
    }
}
?>