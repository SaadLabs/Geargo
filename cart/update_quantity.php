<?php
// Path: cart/update_quantity.php

// 1. Include Session and Database functions
// (Adjust these paths if your folders are different)
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

// 2. Security: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: ../Login/user/login_user.php");
    exit();
}

// 3. Handle the Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and Sanitize Inputs
    $cart_item_id = isset($_POST['cart_item_id']) ? intval($_POST['cart_item_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $user_id = $_SESSION['user_id'];

    // Ensure valid inputs
    if ($cart_item_id > 0 && $quantity > 0) {
        $conn = dbConnect();

        // --- SECURITY CHECK ---
        // Ensure this cart item actually belongs to the logged-in user.
        // We join CartItem with Cart to check the user_id.
        $checkSql = "SELECT ci.cart_item_id 
                     FROM CartItem ci
                     JOIN Cart c ON ci.cart_id = c.cart_id
                     WHERE ci.cart_item_id = ? AND c.user_id = ?";
        
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("ii", $cart_item_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Ownership confirmed. Proceed to update.
            $updateSql = "UPDATE CartItem SET quantity = ? WHERE cart_item_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ii", $quantity, $cart_item_id);
            
            if ($updateStmt->execute()) {
                // Success
            } else {
                // Optional: Handle error logging here
            }
            $updateStmt->close();
        } 
        
        $stmt->close();
        $conn->close();
    }
}

// 4. Redirect Back
// Redirect the user back to the page they came from (Home, Products, etc.)
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    // Fallback if referrer is missing
    header("Location: ../index.php"); 
}
exit();
?>