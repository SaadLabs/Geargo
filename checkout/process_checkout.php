<?php
session_start();
require_once '../Backend/config/functions.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../index.php");
    exit();
}

$conn = dbConnect();
$user_id = $_SESSION['user_id'];

// Capture Inputs
$full_name = $_POST['full_name'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method']; 
$total_amount = $_POST['total_amount'];

// Variable to store the ID of the card used
$verified_card_id = null;

// Get all cart items first
$cartItems = getCartItems($conn, $user_id);

if (empty($cartItems)) {
    $_SESSION['error'] = "Your cart is empty.";
    header("Location: ../index.php");
    exit();
}

// Loop through each item to check stock availability
foreach ($cartItems as $item) {
    // Fetch current stock for this product
    $stockStmt = $conn->prepare("SELECT title, stock_quantity FROM product WHERE product_id = ?");
    $stockStmt->bind_param("i", $item['product_id']);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();
    
    if ($productRow = $stockResult->fetch_assoc()) {
        $current_stock = $productRow['stock_quantity'];
        $product_name = $productRow['title'];

        // Is the order quantity more than available stock
        if ($item['quantity'] > $current_stock) {
            if ($current_stock == 0) {
                $_SESSION['error'] = "Sorry, '$product_name' is currently Out of Stock. Please remove it from your cart.";
            } else {
                $_SESSION['error'] = "Sorry, we only have $current_stock unit(s) of '$product_name' left. Please lower the quantity.";
            }
            
            // Redirect user back to Cart page to fix the issue
            header("Location: ../index.php?msg=error in checkout");
            exit(); 
        }
    }
    $stockStmt->close();
}

// Payment Verification
if ($payment_method === 'Card') {
    $input_number = str_replace(' ', '', $_POST['card_number']);
    $input_cvv = $_POST['card_cvv'];

    // Fetch user's saved cards
    $savedCards = getUserPaymentMethods($conn, $user_id);
    $isVerified = false;

    foreach ($savedCards as $card) {
        // Match user input against hashed database values
        if (
            password_verify($input_number, $card['card_number']) &&
            password_verify($input_cvv, $card['cvv'])
        ) {

            $isVerified = true;
            $verified_card_id = $card['card_id']; 
            break;
        }
    }

    if (!$isVerified) {
        $_SESSION['error'] = "Card verification failed, Details do not match your saved cards.";
        header("Location: checkout.php");
        exit();
    }
}

// PLACE ORDER
$order_status = 'Processing';

$sql = "INSERT INTO `order` (user_id, total_amount, order_status, shipping_address) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['error'] = "SQL Error: " . $conn->error;
    header("Location: checkout.php");
    exit();
}

$stmt->bind_param("idss", $user_id, $total_amount, $order_status, $address);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Record Payment
    if ($payment_method === 'Card' && $verified_card_id !== null) {
        $payment_status = 'Completed'; 
        $paySql = "INSERT INTO payment (order_id, card_id, amount, payment_status) VALUES (?, ?, ?, ?)";
        $payStmt = $conn->prepare($paySql);

        if ($payStmt) {
            $payStmt->bind_param("iids", $order_id, $verified_card_id, $total_amount, $payment_status);
            $payStmt->execute();
            $payStmt->close();
        }
    }

    // Update stock quantity after order
    foreach ($cartItems as $item) {
        $updateStockSql = "UPDATE product SET stock_quantity = stock_quantity - ? WHERE product_id = ?";
        $updateStmt = $conn->prepare($updateStockSql);
        $updateStmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $updateStmt->execute();
        $updateStmt->close();
    }

    // Move Cart Items -> Order Items
    $itemSql = "INSERT INTO orderitem (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
    $itemStmt = $conn->prepare($itemSql);

    foreach ($cartItems as $item) {
        $itemStmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $itemStmt->execute();
    }
    $itemStmt->close();

    // Empty Cart
    $getCartIdSql = "SELECT cart_id FROM Cart WHERE user_id = ?";
    $stmtCart = $conn->prepare($getCartIdSql);
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $cartResult = $stmtCart->get_result();

    if ($cartRow = $cartResult->fetch_assoc()) {
        $cart_id = $cartRow['cart_id'];

        $clearItemsSql = "DELETE FROM CartItem WHERE cart_id = ?";
        $stmtItems = $conn->prepare($clearItemsSql);
        $stmtItems->bind_param("i", $cart_id);
        $stmtItems->execute();
        $stmtItems->close();
    }
    $stmtCart->close();
    
    $_SESSION['message'] = "Order placed successfully!"; // Use session message for alert
    header("Location: ../orders/orders.php");
    exit();

} else {
    $_SESSION['error'] = "Database Error: " . $conn->error;
    header("Location: checkout.php");
    exit();
}

$conn->close();
?>