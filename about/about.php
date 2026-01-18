<?php
require_once '../Backend/config/session_manager.php';
require_once '../Backend/config/functions.php';

$conn = dbConnect();

//AJAX
if (isset($_GET['ajax_query'])) {
    $query = $_GET['ajax_query'];
    $suggestions = getSearchSuggestions($conn, $query);

    // Set header to JSON so JS understands it
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit();
}

// Check Login Status
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;

// Define Paths
$loginPagePath = "../Login/user/login_user.php";
$profilePagePath = "../user profile/user.php";

// Determine where links should go
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// Fetch Cart Data (If logged in)
$cartItems = [];
$cartTotal = 0;
if ($isLoggedIn) {
    $cartItems = getCartItems($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - GearGo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="../cart/cart.css">
    <link rel="stylesheet" href="about.css">
</head>

<body>
    <!-- ================= CART START ================= -->
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
                    $itemTotal = $item['price'] * $item['quantity'];
                    $cartTotal += $itemTotal;
                    // Placeholder image logic
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
                <strong id="cartTotal">Rs. <?php echo number_format($cartTotal); ?></strong>
            </div>

            <?php if ($isLoggedIn && count($cartItems) > 0): ?>
                <form action="../checkout/checkout.php" method="POST">
                    <button type="submit" class="checkout-btn">Checkout</button>
                </form>
            <?php else: ?>
                <button class="checkout-btn" onclick="window.location.href='<?php echo $loginPagePath; ?>'">
                    <?php echo $isLoggedIn ? 'Checkout' : 'Login to Checkout'; ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
    <!-- ================= CART END ================= -->


    <header>
        <nav>
            <div class="menu-icon" id="menuIcon">☰</div>

            <div class="left-section">
                <div class="nav-links-container" id="navContainer">
                    <ul class="nav-links">
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../category/category.php">Products</a></li>
                        <li><a href="<?php echo $isLoggedIn ? '../orders/orders.php' : $loginPagePath; ?>">My Orders</a></li>
                        <li><a href="../contact/contact.php">Contact</a></li>
                        <li><a href="#" class="active">About</a></li>
                    </ul>

                    <div class="mobile-menu-icons">
                        <a href="../user profile/user.php" class="mobile-icon-link">
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


    <section class="about-hero">
        <h1>About <span>GearGo</span></h1>
        <p>Your trusted destination for premium gear and smart shopping.</p>
    </section>

    <section class="about-content">
        <div class="about-text">
            <h2>Who We Are</h2>
            <p>
                GearGo is an emerging e-commerce platform focused on providing
                high-quality gear and accessories with a smooth and secure
                shopping experience. Our goal is to make modern products
                accessible, reliable, and affordable for everyone.
            </p>

            <p>
                We believe in innovation, simplicity, and customer satisfaction.
                Every feature of GearGo is designed with users in mind, ensuring
                fast navigation, secure transactions, and trusted products.
            </p>
        </div>

        <div class="about-image">
            <img src="../assets/images/about-page/geargo-about-image.png" alt="GearGo About Image">
        </div>
    </section>

    <section class="developers">
        <h2>Meet the Developers</h2>

        <div class="dev-cards">
            <div class="dev-card">
                <img src="../assets/developers/cropped_circle_image.png" alt="Developer 1">
                <h3>Muhammad Saad Khan</h3>
                <p>Front End Developer</p>
            </div>

            <div class="dev-card">
                <img src="../assets/developers/khizar.png" alt="Developer 2">
                <h3>Khizar Nadeem</h3>
                <p>Backend Developer</p>
            </div>
        </div>
    </section>

    <script src="about.js"></script>
    <script src="../cart/cart.js"></script>
</body>

</html>
</body>

</html>