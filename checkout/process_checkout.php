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

// 1. Capture Common Inputs
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];
$payment_method = $_POST['payment_method'];
$total_amount = $_POST['total_amount'];

// 2. PAYMENT VERIFICATION LOGIC
if ($payment_method === 'Card') {

    // Inputs from form
    //$input_number = str_replace(' ', '', $_POST['card_number']); // Remove spaces
    $input_number = $_POST['card_number'];
    $input_cvv = $_POST['card_cvv'];

    // Fetch all saved cards for this user
    $savedCards = getUserPaymentMethods($conn, $user_id);

    $isVerified = false;

    // Loop through saved cards and try to find a match
    foreach ($savedCards as $card) {
        // Since we stored cards using password_hash, we use password_verify
        // Verify Card Number AND CVV
        if (
            password_verify($input_number, $card['card_number']) &&
            password_verify($input_cvv, $card['cvv'])
        ) {

            $isVerified = true;
            break; // Found a match, stop looking
        }
    }

    if (!$isVerified) {
        $_SESSION['error'] = "Card verification failed. The details entered do not match any saved card in your profile.";
        header("Location: checkout.php");
        exit();
    }
}

// 3. IF VERIFIED (OR COD), PLACE ORDER
// A. Insert into Orders Table
// Note: Ensure your 'Orders' table columns match this!
$order_status = ($payment_method == 'Card') ? 'paid' : 'pending'; // Example logic

$sql = "INSERT INTO `order` (user_id, total_amount, order_status, shipping_address) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $_SESSION['error'] = "Order Error: " . $conn->error;
    header("Location: checkout.php");
    exit();
}

$stmt->bind_param("idss", $user_id, $total_amount, $order_status, $address);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id; // Get the ID of the new order
    $stmt->close();

    // B. Move Items from Cart to Order_Items
    $cartItems = getCartItems($conn, $user_id);

    $itemSql = "INSERT INTO orderitem (order_id, product_id, price_at_purchase, quantity) VALUES (?, ?, ?, ?)";
    $itemStmt = $conn->prepare($itemSql);

    foreach ($cartItems as $item) {
        $itemStmt->bind_param("iidi", $order_id, $item['product_id'], $item['price'], $item['quantity']);
        $itemStmt->execute();
    }
    $itemStmt->close();

    // C. Clear User's Cart (Empty items only)
    // ---------------------------------------------------------

    // 1. Find the User's Cart ID
    $getCartIdSql = "SELECT cart_id FROM cart WHERE user_id = ?";
    $stmtCart = $conn->prepare($getCartIdSql);
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $cartResult = $stmtCart->get_result();

    if ($cartRow = $cartResult->fetch_assoc()) {
        $cart_id = $cartRow['cart_id'];

        // 2. Delete ONLY the items inside this cart
        // We do NOT delete the Cart row itself
        $clearItemsSql = "DELETE FROM cartitem WHERE cart_id = ?";
        $stmtItems = $conn->prepare($clearItemsSql);
        $stmtItems->bind_param("i", $cart_id);
        $stmtItems->execute();
        $stmtItems->close();
    }
    $stmtCart->close();

    // ---------------------------------------------------------

    $_SESSION['success'] = "Order #$order_id placed successfully!";
    header("Location: ../orders/orders.php");
    exit();

    // D. Success!
    $_SESSION['success'] = "Order placed successfully! Order ID: #GG" . $order_id;
    header("Location: ../orders/orders.php"); // Send them to My Orders page
    exit();

} else {
    $_SESSION['error'] = "Database Error: " . $conn->error;
    header("Location: checkout.php");
    exit();
}

$conn->close();
?>