<?php
session_start();
require_once '../../Backend/config/functions.php';
$conn = dbConnect();

if ($_SESSION['role'] !== 'admin')
    die("Unauthorized");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status']; // 'Shipped' or 'Delivered'

    // We need this to check if it was already cancelled to avoid adding stock twice
    $checkSql = "SELECT order_status FROM `order` WHERE order_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $order_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $current_data = $result->fetch_assoc();

    if (!$current_data) {
        die("Order not found.");
    }

    $current_status = $current_data['order_status'];

    if ($new_status != 'Cancelled' && $current_status === 'Cancelled'){
        header("location: admin.php?msg=order already canceld, Cant changes it status");
        exit();
    }

    // Inventory Update if orderis cancelled
    if ($new_status === 'Cancelled' && $current_status !== 'Cancelled') {

        //Get all items in this order
        $itemsSql = "SELECT product_id, quantity FROM orderitem WHERE order_id = ?";
        $itemStmt = $conn->prepare($itemsSql);
        $itemStmt->bind_param("i", $order_id);
        $itemStmt->execute();
        $itemsResult = $itemStmt->get_result();

        // Loop through items and add stock back to inventory
        $restockSql = "UPDATE product SET stock_quantity = stock_quantity + ? WHERE product_id = ?";
        $restockStmt = $conn->prepare($restockSql);

        while ($item = $itemsResult->fetch_assoc()) {
            $restockStmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $restockStmt->execute();
        }
        $itemStmt->close();
        $restockStmt->close();

        // Update Payment Status in DB
        $new_payment_status = "Refunded";
        $paySql = "UPDATE `payment` SET payment_status = ? WHERE order_id = ?";
        $payStmt = $conn->prepare($paySql);
        $payStmt->bind_param("si", $new_payment_status, $order_id);
        $payStmt->execute();
        $payStmt->close();
    }

    // Update the Order Status
    $updateSql = "UPDATE `order` SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("si", $new_status, $order_id);

    if ($stmt->execute()) {
        $msg = "Order updated successfully to $new_status";
        // If it was a cancellation, add that to the message
        if ($new_status === 'Cancelled' && $current_status !== 'Cancelled') {
            $msg .= " (Stock Restored)";
        }
        header("Location: admin.php?msg=" . urlencode($msg));
    } else {
        header("Location: admin.php?error=" . urlencode("Database error: " . $conn->error));
    }

    $stmt->close();
} else {
    // If accessed directly without POST
    header("Location: admin.php");
}

$conn->close();
?>