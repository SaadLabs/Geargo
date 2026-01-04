<?php
// 1. Start Session and Include Functions
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

$conn = dbConnect();

// === AJAX HANDLER ===
if (isset($_GET['ajax_query'])) {
    $query = $_GET['ajax_query'];
    $suggestions = getSearchSuggestions($conn, $query);
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit();
}

// 2. Check Login Status
$isLoggedIn = isset($_SESSION['user_id']);
if (!$isLoggedIn) {
    header("Location: ../Login/user/login_user.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// 3. Define Paths
$loginPagePath = "../Login/user/login_user.php";
$profilePagePath = "../user profile/user.php";
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// 4. Fetch Cart & User Data
$cartItems = getCartItems($conn, $user_id);
$userData = ['name' => '', 'email' => '', 'phone' => ''];

// Fetch User Details
$userSql = "SELECT name, email, phone FROM user WHERE user_id = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $userData = $row;
}
$stmt->close();

// 5. FETCH SAVED CARDS (To check if user has any)
$savedCards = getUserPaymentMethods($conn, $user_id);
$hasCards = count($savedCards) > 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - GearGo</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="../cart/cart.css">
</head>

<body>
    <div id="cart-overlay" class="cart-overlay" onclick="closeCart()"></div>

    <div id="cart-sidebar" class="cart-sidebar">
        <div class="cart-header">
            <h2>Your Cart</h2>
            <span class="close-cart" onclick="closeCart()">×</span>
        </div>

        <div class="cart-items" id="cartItems">
            <?php if ($isLoggedIn && count($cartItems) > 0): ?>
                <?php foreach ($cartItems as $item): ?>
                    <?php
                    $imgSrc = !empty($item['image']) ? "../" . $item['image'] : "../headphone1.png";
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="cart-item-details">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Rs. <?php echo number_format($item['price']); ?></p>
                            <form action="../cart/update_quantity.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1"
                                    onchange="this.form.submit()" style="width: 50px; padding: 5px;">
                            </form>
                        </div>
                        <form action="../cart/remove_cart_item.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                            <button type="submit" class="remove-btn">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!$isLoggedIn): ?>
                <p class="empty-cart" style="display:block;">Please <a href="<?php echo $loginPagePath; ?>">login</a> to
                    view your cart.</p>
            <?php else: ?>
                <p class="empty-cart" style="display:block;">Your cart is empty</p>
            <?php endif; ?>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <strong id="cartTotal">Rs.
                    <?php echo number_format(array_sum(array_map(function ($i) {
                        return $i['price'] * $i['quantity'];
                    }, $cartItems))); ?></strong>
            </div>
            <button class="checkout-btn" onclick="closeCart()">Close</button>
        </div>
    </div>
    <header>
        <nav>
            <div class="menu-icon" id="menuIcon">☰</div>
            <div class="left-section">
                <div class="nav-links-container" id="navContainer">
                    <ul class="nav-links">
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../category/category.php">Products</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="../orders/orders.php">My Orders</a></li>
                    </ul>
                    <div class="mobile-menu-icons">
                        <a href="<?php echo $accountLink; ?>" class="mobile-icon-link">
                            <img height="25px" src="../assets/svg/user.svg" alt="User">
                            <span>My Account</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="center-section">
                <div class="logo">
                    <img src="../assets/logo/logo_blue_bgr.png" alt="Logo" />
                </div>
            </div>

            <div class="right-section">
                <form action="../search/search.php" method="GET" class="search-box desktop-search"
                    style="position:relative;">
                    <input name="search" id="searchInput" placeholder="Search" autocomplete="off">
                    <button type="button" id="clearBtn"><span class="material-symbols-outlined">close</span></button>
                    <div class="vline"></div>
                    <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>
                    <div id="searchResultsList" class="search-suggestions-box"></div>
                </form>

                <span class="material-symbols-outlined mobile-search-icon">search</span>

                <a class="nav-svg no-show-svg" href="<?php echo $accountLink; ?>">
                    <img src="../assets/svg/user.svg" alt="User Profile">
                </a>

                <a class="nav-svg" href="javascript:void(0)"
                    onclick="<?php echo $isLoggedIn ? 'openCart()' : "window.location.href='$loginPagePath'"; ?>">
                    <img src="../assets/svg/cart.svg" alt="Cart">
                </a>
            </div>
        </nav>

        <div class="mobile-search-bar">
            <form action="../search/search.php" method="GET" class="search-box" style="position:relative;">
                <input name="search" id="mobileSearchInput" placeholder="Search" autocomplete="off" />
                <button type="button" id="mobileClearBtn"><span class="material-symbols-outlined">close</span></button>
                <div class="vline"></div>
                <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>
                <div id="mobileSearchResultsList" class="search-suggestions-box"></div>
            </form>
        </div>
    </header>
    <div class="heading">
        <h1>Checkout</h1>
    </div>

    <div class="checkout-container">

        <div class="checkout-cart">
            <h2>Cart Summary</h2>
            <?php
            $finalTotal = 0;
            if (count($cartItems) > 0):
                foreach ($cartItems as $item):
                    $lineTotal = $item['price'] * $item['quantity'];
                    $finalTotal += $lineTotal;
                    $imgSrc = !empty($item['image']) ? "../" . $item['image'] : "../headphone1.png";
                    ?>
                    <div class="checkout-item">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="item-info">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Rs. <?php echo number_format($item['price']); ?> × <?php echo $item['quantity']; ?></p>
                        </div>
                    </div>
                    <?php
                endforeach;
            else:
                ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
            <h3>Total: Rs. <?php echo number_format($finalTotal); ?></h3>
        </div>

        <form id="checkoutForm" class="checkout-form" action="process_checkout.php" method="POST">

            <input type="hidden" name="total_amount" value="<?php echo $finalTotal; ?>">

            <h2>Shipping Details</h2>

            <input type="text" id="fullName" name="full_name" placeholder="Full Name"
                value="<?php echo htmlspecialchars($userData['name']); ?>" required>
            <span class="error" id="fullNameError"></span> <input type="email" id="email" name="email"
                placeholder="Email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            <span class="error" id="emailError"></span> <input type="tel" id="phone" name="phone"
                placeholder="Phone Number" value="<?php echo htmlspecialchars($userData['phone']); ?>" required>
            <span class="error" id="phoneError"></span> <textarea id="address" name="address" placeholder="Address"
                rows="3" required></textarea>
            <span class="error" id="addressError"></span>
            <h2>Payment Method</h2>
            <select id="paymentMethod" name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="COD">Cash on Delivery</option>
                <option value="Card">Card</option>
            </select>
            <span class="error" id="paymentMethodError"></span>
            <div id="cardDetails" style="display:none; margin-top:15px; border-top: 1px solid #ddd; padding-top: 15px;">

                <?php if ($hasCards): ?>
                    <div
                        style="background: #e8f4fd; padding: 10px; border-radius: 5px; margin-bottom: 10px; font-size: 0.9em; color: #0056b3;">
                        <i class="bi bi-info-circle"></i> Verification Required: Verify your card details.
                    </div>

                    <input type="text" id="cardName" name="card_name" placeholder="Name on Card">
                    <span class="error" id="cardNameError"></span> <input type="text" id="cardNumber" name="card_number"
                        placeholder="Card Number (Full)" maxlength="19">
                    <span class="error" id="cardNumberError"></span>
                    <div style="display: flex; gap: 10px;">
                        <div style="flex:1;">
                            <input type="text" id="cardExpiry" name="card_expiry" placeholder="Expiry (MM/YY)"
                                maxlength="5">
                            <span class="error" id="cardExpiryError"></span>
                        </div>
                        <div style="flex:1;">
                            <input type="password" id="cardCVV" name="card_cvv" placeholder="CVV" maxlength="3">
                            <span class="error" id="cardCVVError"></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div
                        style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; border: 1px solid #ffeeba;">
                        <strong>No Payment Methods Found</strong><br>
                        Please add a card in your profile first.
                        <br><br>
                        <a href="../user profile/user.php" class="btn-primary"
                            style="text-decoration:none; padding: 5px 10px; font-size: 0.9em;">Go to Profile</a>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" id="placeOrderBtn" style="margin-top: 20px;">Place Order</button>
        </form>

    </div>

    <script>
        function toggleCardDetails() {
            const method = document.getElementById('paymentMethod').value;
            const cardDiv = document.getElementById('cardDetails');
            const btn = document.getElementById('placeOrderBtn');
            const hasCards = <?php echo $hasCards ? 'true' : 'false'; ?>;

            if (method === 'Card') {
                cardDiv.style.display = 'block';
                // If user has no cards, disable the Place Order button so they can't submit empty data
                if (!hasCards) {
                    btn.disabled = true;
                    btn.style.opacity = "0.5";
                    btn.style.cursor = "not-allowed";
                } else {
                    btn.disabled = false;
                    btn.style.opacity = "1";
                    btn.style.cursor = "pointer";
                }
            } else {
                cardDiv.style.display = 'none';
                // Re-enable button for COD
                btn.disabled = false;
                btn.style.opacity = "1";
                btn.style.cursor = "pointer";
            }
        }
    </script>
    <script src="checkout.js"></script>
    <script src="../cart/cart.js"></script>

    <?php if (isset($_SESSION['error'])): ?>
        <script>alert("<?php echo addslashes($_SESSION['error']); ?>"); <?php unset($_SESSION['error']); ?></script>
    <?php endif; ?>

</body>

</html>