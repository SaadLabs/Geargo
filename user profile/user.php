<?php
session_start();
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
$profilePagePath = "user.php";
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// 4. Fetch User Details
$sql = "SELECT * FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 5. Fetch Orders
$orders = getUserOrders($conn, $user_id);

// 6. Fetch Cart
$cartItems = getCartItems($conn, $user_id);
$cartTotal = 0;

// 7. Fetch Payment Methods (From usercard table)
$paymentMethods = getUserPaymentMethods($conn, $user_id);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>GearGo | User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="user.css">
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
            <?php if (count($cartItems) > 0): ?>
                <?php foreach ($cartItems as $item): ?>
                    <?php
                    $itemTotal = $item['price'] * $item['quantity'];
                    $cartTotal += $itemTotal;
                    $imgSrc = !empty($item['image']) ? "../" . $item['image'] : "../headphone1.png";
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="cart-item-details">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Rs. <?php echo number_format($item['price']); ?></p>
                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                        </div>
                        <form action="../cart/remove_cart_item.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                            <button type="submit" class="remove-btn">&times;</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-cart" style="display:block;">Your cart is empty</p>
            <?php endif; ?>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <strong id="cartTotal">Rs. <?php echo number_format($cartTotal); ?></strong>
            </div>
            <?php if (count($cartItems) > 0): ?>
                <form action="../checkout/checkout.php" method="POST">
                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            <?php else: ?>
                <button class="checkout-btn">Checkout</button>
            <?php endif; ?>
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
                <a class="nav-svg no-show-svg" href="<?php echo $accountLink; ?>"><img src="../assets/svg/user.svg"
                        alt="User Profile"></a>
                <a class="nav-svg" href="javascript:void(0)" onclick="openCart()"><img src="../assets/svg/cart.svg"
                        alt="Cart"></a>
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

    <section class="profile-page">
        <div class="container">

            <div class="sidebar">
                <?php
                $profileImg = !empty($user['profile_pic']) ? $user['profile_pic'] : '../assets/svg/user.svg';
                ?>
                <img src="<?php echo htmlspecialchars($profileImg); ?>" alt="User"
                    style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; margin-bottom: 15px;">

                <h2 id="displayName"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="email" id="displayEmail"><?php echo htmlspecialchars($user['email']); ?></p>

                <button class="btn-primary" onclick="openProfileEdit()">Edit Profile</button>
            </div>

            <div class="content">

                <div class="section" id="viewProfile">
                    <h3>Account Information</h3>
                    <div class="row">
                        <span>Name</span>
                        <span id="username"><?php echo htmlspecialchars($user['name']); ?></span>
                    </div>
                    <div class="row">
                        <span>Phone</span>
                        <span
                            id="phone"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not set'; ?></span>
                    </div>
                    <div class="row">
                        <span>Member Since</span>
                        <span><?php echo date("F Y", strtotime($user['created_at'])); ?></span>
                    </div>
                </div>

                <div class="section" id="editProfile" style="display:none;">
                    <h3>Edit Profile</h3>
                    <form action="update_profile_action.php" method="POST" enctype="multipart/form-data">
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                        <label>Phone</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                        <label>Profile_pic</label>
                        <input type="file" name="profile_pic">

                        <br><br>
                        <button type="submit" class="btn-primary">Save</button>
                        <button type="button" class="btn-outline" onclick="cancelProfile()">Cancel</button>
                    </form>
                </div>

                <div class="section" id="viewAddress">
                    <h3>Shipping Address</h3>
                    <p id="address">No saved address found (Address is saved per Order)</p>
                    <button class="btn-link" onclick="openAddressEdit()">Edit Address</button>
                </div>

                <div class="section" id="editAddress" style="display:none;">
                    <h3>Edit Address</h3>
                    <input type="text" id="editAddressInput" placeholder="Enter new address">
                    <br><br>
                    <button class="btn-primary" onclick="saveAddress()">Save</button>
                    <button class="btn-outline" onclick="cancelAddress()">Cancel</button>
                </div>

                <div class="section" id="viewPayments">
                    <h3>Payment Methods</h3>

                    <?php if (count($paymentMethods) > 0): ?>
                        <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 15px;">
                            <?php foreach ($paymentMethods as $pm): ?>
                                <?php
                                // 1. SIMPLE DISPLAY LOGIC
                                // We just use the plain text 'last_four' column
                                $display_number = "************" . htmlspecialchars($pm['last_four']);

                                // 2. Format Expiry
                                $formatted_month = str_pad($pm['exp_month'], 2, "0", STR_PAD_LEFT);
                                $formatted_year = substr($pm['exp_year'], -2);
                                ?>
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border: 1px solid #eee; border-radius: 5px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="bi bi-credit-card-2-front" style="font-size: 1.5rem; color: #555;"></i>
                                        <div>
                                            <strong style="display:block;">Card Details</strong>

                                            <small style="color: #555; font-size: 0.9rem;">
                                                <?php echo $display_number; ?>
                                            </small>
                                            <br>
                                            <small style="color: #888;">
                                                Expires <?php echo $formatted_month . '/' . $formatted_year; ?> |
                                                <?php echo htmlspecialchars($pm['card_holder_name']); ?>
                                            </small>
                                        </div>
                                    </div>

                                    <form action="delete_payment_action.php" method="POST"
                                        onsubmit="return confirm('Are you sure you want to remove this card?');">
                                        <input type="hidden" name="card_id" value="<?php echo $pm['card_id']; ?>">
                                        <button type="submit" style="background:none; border:none; cursor:pointer; color:red;"
                                            title="Delete Card">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="margin-bottom: 15px;">No payment methods saved.</p>
                    <?php endif; ?>

                    <button class="btn-link"
                        onclick="document.getElementById('addPaymentForm').style.display='block'; document.getElementById('viewPayments').style.display='none';">+
                        Add Payment Method</button>
                </div>

                <div class="section" id="addPaymentForm" style="display:none;">
                    <h3>Add New Card</h3>
                    <form action="add_payment_action.php" method="POST">
                        <label>Name on C1ard</label>
                        <input type="text" name="card_name" placeholder="John Doe" required>

                        <label>Card Number</label>
                        <input type="text" name="card_number" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" required>

                        <div style="display: flex; gap: 10px;">
                            <div style="flex:1;">
                                <label>Expiry (MM/YY)</label>
                                <input type="text" name="expiry" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div style="flex:1;">
                                <label>CVV</label>
                                <input type="password" name="cvv" placeholder="123" maxlength="3" required>
                            </div>
                        </div>

                        <br>
                        <button type="submit" class="btn-primary">Add Card</button>
                        <button type="button" class="btn-outline"
                            onclick="document.getElementById('addPaymentForm').style.display='none'; document.getElementById('viewPayments').style.display='block';">Cancel</button>
                    </form>
                </div>

                <div class="section">
                    <h3>Order History</h3>
                    <div class="table-wrapper">
                        <?php if (count($orders) > 0): ?>
                            <table>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                </tr>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#GG<?php echo $order['order_id']; ?></td>
                                        <td><?php echo date("d M Y", strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <span
                                                style="<?php echo ($order['order_status'] == 'pending') ? 'color:orange;' : 'color:green;'; ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>
                                        <td>Rs. <?php echo number_format($order['total_amount']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php else: ?>
                            <p>No orders found.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="section" id="securitySection">
                    <h3>Security</h3>
                    <button class="btn-outline" onclick="openPassword()">Change Password</button>
                    <form action="logout.php" method="post" style="margin-top:10px;">
                        <button type="submit" class="btn-danger">Logout</button>
                    </form>
                </div>

                <div class="section" id="changePassword" style="display:none;">
                    <h3>Change Password</h3>
                    <form action="change_password_action.php" method="POST">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                        <br><br>
                        <button type="submit" class="btn-primary">Update Password</button>
                        <button type="button" class="btn-outline" onclick="cancelPassword()">Cancel</button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <script src="user.js"></script>
    <script src="../cart/cart.js"></script>

    <?php if (isset($_SESSION['success'])): ?>
        <script>alert("<?php echo addslashes($_SESSION['success']); ?>");</script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>alert("<?php echo addslashes($_SESSION['error']); ?>");</script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

</body>

</html>