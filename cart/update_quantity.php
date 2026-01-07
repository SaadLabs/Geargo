<?php
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: ../Login/user/login_user.php");
    exit();
}

//Handle the Update Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get and Sanitize Inputs
    $cart_item_id = isset($_POST['cart_item_id']) ? intval($_POST['cart_item_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $user_id = $_SESSION['user_id'];

    // Ensure valid inputs
    if ($cart_item_id > 0 && $quantity > 0) {
        $conn = dbConnect();

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
            
            $updateStmt->execute();
            $updateStmt->close();
        } 
        
        $stmt->close();
        $conn->close();
    }
}

// Get the previous page URL
$url = $_SERVER['HTTP_REFERER'];

// Check if URL already has query parameters (contains '?')
if (strpos($url, '?') !== false) {
    // If yes, append with '&'
    $url .= '&open_cart=1';
} else {
    // If no, start with '?'
    $url .= '?open_cart=1';
}

header("Location: $url");
exit();
?>