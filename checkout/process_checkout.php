<?php
// checkout/process_checkout.php
session_start();
require_once '../Backend/config/functions.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: ../index.php");
    exit();
}

$conn = dbConnect();
$user_id = $_SESSION['user_id'];

// 1. Capture Inputs
$full_name = $_POST['full_name'];
$address = $_POST['address']; 
$payment_method = $_POST['payment_method']; // 'Card' or 'COD'
$total_amount = $_POST['total_amount'];

// Variable to store the ID of the card used (if paying by card)
$verified_card_id = null;

// 2. PAYMENT VERIFICATION (Card Only)
if ($payment_method === 'Card') {
    $input_number = str_replace(' ', '', $_POST['card_number']); 
    $input_cvv = $_POST['card_cvv'];
    
    // Fetch user's saved cards
    $savedCards = getUserPaymentMethods($conn, $user_id);
    $isVerified = false;

    foreach ($savedCards as $card) {
        // Match user input against hashed database values
        if (password_verify($input_number, $card['card_number']) && 
            password_verify($input_cvv, $card['cvv'])) {
            
            $isVerified = true;
            $verified_card_id = $card['card_id']; // CAPTURE THE ID!
            break; 
        }
    }

    if (!$isVerified) {
        $_SESSION['error'] = "Card verification failed! Details do not match your saved cards.";
        header("Location: checkout.php");
        exit();
    }
}

// 3. PLACE ORDER
// ==============================================================================
// CHANGE 1: Order Status is now 'Processing' for new orders (not 'paid' or 'shipped')
// ==============================================================================
$order_status = 'Processing'; 

// Insert Order
$sql = "INSERT INTO `order` (user_id, total_amount, order_status, shipping_address) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['error'] = "SQL Error: " . $conn->error;
    header("Location: checkout.php");
    exit();
}

$stmt->bind_param("idss", $user_id, $total_amount, $order_status, $address,);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();

    // ==============================================================================
    // CHANGE 2: Record the Payment in 'payment' table (Only for Card)
    // ==============================================================================
    if ($payment_method === 'Card' && $verified_card_id !== null) {
        $payment_status = 'Completed'; // Since verification passed
        
        // Columns: payment_id (Auto), order_id, card_id, amount, payment_status, paid_at (Default NOW)
        $paySql = "INSERT INTO payment (order_id, card_id, amount, payment_status) VALUES (?, ?, ?, ?)";
        $payStmt = $conn->prepare($paySql);
        
        if ($payStmt) {
            // Types: i (int), i (int), d (decimal), s (string)
            $payStmt->bind_param("iids", $order_id, $verified_card_id, $total_amount, $payment_status);
            $payStmt->execute();
            $payStmt->close();
        }
    }

    // ==============================================================================
    // CHANGE 3: Move Items & Clear Cart (Same as before)
    // ==============================================================================
    
    // Move Cart Items -> Order Items
    $cartItems = getCartItems($conn, $user_id);
    $itemSql = "INSERT INTO orderitem (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)";
    $itemStmt = $conn->prepare($itemSql);

    foreach ($cartItems as $item) {
        $itemStmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $itemStmt->execute();
    }
    $itemStmt->close();

    // Empty Cart (Delete Children First logic)
    // 1. Get Cart ID
    $getCartIdSql = "SELECT cart_id FROM Cart WHERE user_id = ?";
    $stmtCart = $conn->prepare($getCartIdSql);
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $cartResult = $stmtCart->get_result();
    
    if ($cartRow = $cartResult->fetch_assoc()) {
        $cart_id = $cartRow['cart_id'];
        
        // 2. Delete items
        $clearItemsSql = "DELETE FROM CartItem WHERE cart_id = ?";
        $stmtItems = $conn->prepare($clearItemsSql);
        $stmtItems->bind_param("i", $cart_id);
        $stmtItems->execute();
        $stmtItems->close();
    }
    $stmtCart->close();
    header("Location: ../orders/orders.php");
    exit();

} else {
    $_SESSION['error'] = "Database Error: " . $conn->error;
    header("Location: checkout.php");
    exit();
}

$conn->close();
?>