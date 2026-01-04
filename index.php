<?php
// 1. Start Session and Include Functions
require_once 'Backend/config/session_manager.php';
require_once 'Backend/config/functions.php';

$conn = dbConnect();

// === NEW: AJAX HANDLER ===
// If 'ajax_query' is in the URL, return JSON data and STOP loading the rest of the page.
if (isset($_GET['ajax_query'])) {
    $query = $_GET['ajax_query'];
    $suggestions = getSearchSuggestions($conn, $query);

    // Set header to JSON so JS understands it
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit(); // Important: Stop here so we don't load the HTML!
}
// =========================

// 2. Check Login Status
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;

// 3. Define Paths
$loginPagePath = "Login/user/login_user.php";
$profilePagePath = "user profile/user.php";

// Determine where links should go
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// 4. Fetch Cart Data (If logged in)
$cartItems = [];
$cartTotal = 0;
if ($isLoggedIn) {
    $cartItems = getCartItems($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gear Go</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="cart/cart.css">
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
                    $imgSrc = !empty($item['image']) ? $item['image'] : "headphone1.png";
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">

                        <div class="cart-item-details">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p>Rs. <?php echo number_format($item['price']); ?></p>

                            <form action="cart/update_quantity.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="cart_item_id" value="<?php echo $item['cart_item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1"
                                    onchange="this.form.submit()" style="width: 50px; padding: 5px;">
                            </form>
                        </div>

                        <form action="cart/remove_cart_item.php" method="POST" style="display:inline;">
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
                <form action="checkout/checkout.php" method="POST">
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
                        <li><a href="#">Home</a></li>
                        <li><a href="category/category.php">Products</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="<?php echo $isLoggedIn ? 'orders/orders.php' : $loginPagePath; ?>">My Orders</a>
                        </li>
                    </ul>

                    <div class="mobile-menu-icons">
                        <a href="<?php echo $accountLink; ?>" class="mobile-icon-link">
                            <img height="25px" src="assets/svg/user.svg" alt="User">
                            <span><?php echo $isLoggedIn ? 'My Account' : 'Login / Register'; ?></span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="center-section">
                <div class="logo">
                    <img src="assets/logo/logo_blue_bgr.png" alt="Logo" />
                </div>
            </div>

            <div class="right-section">
                <form action="search/search.php" method="GET" class="search-box desktop-search"
                    style="position:relative;">
                    <input name="search" id="searchInput" placeholder="Search" autocomplete="off">

                    <button type="button" id="clearBtn"><span class="material-symbols-outlined">close</span></button>
                    <div class="vline"></div>
                    <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>

                    <div id="searchResultsList" class="search-suggestions-box"></div>
                </form>

                <span class="material-symbols-outlined mobile-search-icon">search</span>

                <a class="nav-svg no-show-svg" href="<?php echo $accountLink; ?>">
                    <img src="assets/svg/user.svg" alt="User Profile">
                </a>

                <a class="nav-svg" href="javascript:void(0)"
                    onclick="<?php echo $isLoggedIn ? 'openCart()' : "window.location.href='$loginPagePath'"; ?>">
                    <img src="assets/svg/cart.svg" alt="Cart">
                </a>
            </div>
        </nav>

        <div class="mobile-search-bar">
            <form action="search/search.php" method="GET" class="search-box" style="position:relative;">
                <input name="search" id="mobileSearchInput" placeholder="Search" autocomplete="off" />

                <button type="button" id="mobileClearBtn"><span class="material-symbols-outlined">close</span></button>
                <div class="vline"></div>
                <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>

                <div id="mobileSearchResultsList" class="search-suggestions-box"></div>
            </form>
        </div>
    </header>

    <section class="hero">

        <div class="hero-container active">
            <div class="hero-text-container">
                <div class="hero-heading">
                    <h1>Premium Headphones</h1>
                </div>
                <div class="hero-text">
                    Express crystal-clear audio with our latest wireless technology
                </div>
                <div class="hero-button">
                    <button class="slide-button" onclick="window.location.href='category/category.php?category=1'">
                        Explore Collection
                    </button>
                </div>
            </div>
            <div class="hero-image-container">
                <div class="hero-image-box">
                    <img src="assets/images/hero-section/headphone.png" alt="">
                </div>
            </div>
        </div>

        <div class="hero-container">
            <div class="hero-text-container">
                <div class="hero-heading">
                    <h1>Smart Watches</h1>
                </div>
                <div class="hero-text">
                    Stay connected with cutting-edge wearable technology
                </div>
                <div class="hero-button">
                    <button class="slide-button" onclick="window.location.href='category/category.php?category=2'">
                        Shop Watches
                    </button>
                </div>
            </div>
            <div class="hero-image-container">
                <div class="hero-image-box">
                    <img src="assets/images/hero-section/smart-watch.png" alt="">
                </div>
            </div>
        </div>

<div class="hero-container">
            <div class="hero-text-container">
                <div class="hero-heading">
                    <h1>High-Performance Laptops</h1>
                </div>
                <div class="hero-text">
                    Unleash your potential with blazing-fast processors and stunning displays designed for both productivity and play.
                </div>
                <div class="hero-button">
                    <button class="slide-button" onclick="window.location.href='category/category.php?category=3'">
                        Discover More
                    </button>
                </div>
            </div>
            <div class="hero-image-container">
                <div class="hero-image-box">
                    <img src="assets/images/hero-section/laptop.png" alt="Laptops">
                </div>
            </div>
        </div>

        <div class="hero-container">
            <div class="hero-text-container">
                <div class="hero-heading">
                    <h1>Mobiles & Tablets</h1>
                </div>
                <div class="hero-text">
                    Stay connected anywhere with our latest range of flagship smartphones and versatile tablets.
                </div>
                <div class="hero-button">
                    <button class="slide-button" onclick="window.location.href='category/category.php?category=5'">
                        Shop Mobile
                    </button>
                </div>
            </div>
            <div class="hero-image-container">
                <div class="hero-image-box">
                    <img src="assets/images/hero-section/mobile.png" alt="Mobiles and Tablets">
                </div>
            </div>
        </div>

        <div class="dots-container">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </section>

    <section class="hot-products">
        <h2>🔥 Hot Products</h2>
        <div class="hot-products-grid">
            <?php
            if (function_exists('Random_products')) {
                $products = Random_products(10, $conn);
            } else {
                $result = $conn->query("SELECT * FROM Product ORDER BY RAND() LIMIT 10");
                $products = $result->fetch_all(MYSQLI_ASSOC);
            }

            foreach ($products as $product) {
                $img = !empty($product['image']) ? $product['image'] : 'headphone1.png';
                ?>

                <div class="product-card-premium">
                    <a href="product page/product.php?id=<?php echo $product['product_id']; ?>"
                        style="text-decoration:none; color:inherit;">
                        <div class="product-image-container-premium">
                            <img src="<?php echo $img; ?>" height="200px"
                                alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image-premium">
                        </div>
                        <div class="product-info-section">
                            <h3 class="product-title-premium"><?php echo htmlspecialchars($product['title']); ?></h3>
                            <div class="price-comparison-section">
                                <span
                                    class="current-price-premium"><small>Rs.</small><?php echo number_format($product['price']); ?></span>
                                <span
                                    class="original-price-premium"><small>Rs.</small><?php echo number_format($product['price'] + ($product['price'] * 0.1)); ?></span>
                            </div>
                        </div>
                    </a>

                    <form action="cart/add_to_cart.php" method="POST" style="padding: 0 15px 15px 15px;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <input type="hidden" name="quantity" value="1">

                        <?php if ($isLoggedIn): ?>
                            <button type="submit" class="add-to-cart-btn-premium">ADD TO CART</button>
                        <?php else: ?>
                            <button type="button" class="add-to-cart-btn-premium"
                                onclick="window.location.href='<?php echo $loginPagePath; ?>'">
                                ADD TO CART
                            </button>
                        <?php endif; ?>
                    </form>
                </div>

            <?php } ?>
        </div>
    </section>
    <section class="promo-banner-section">
        <div class="promo-banner">
            <picture>
                <source srcset="assets/images/home-section/banner/geargo-banner-mobile.png" media="(max-width: 768px)">
                <source srcset="assets/images/home-section/banner/geargo-banner-pc.webp" media="(min-width: 769px)">
                <img src="assets/images/home-section/banner/geargo-banner-pc.webp" alt="GearGo Wireless Earbuds">
            </picture>
            <div class="banner-content">
                <span class="banner-tag">NEW ARRIVAL</span>
                <h2>Power Your Sound, Anywhere</h2>
                <p>Premium tech accessories built for everyday performance.</p>
                <a href="#" class="banner-btn">Shop Now</a>
            </div>
        </div>
    </section>

    <section class="why-geargo">
        <h2>Why buy from <span>GearGo</span></h2>
        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon"><i class="bi-arrow-counterclockwise"></i></div>
                <h3>Easy returns</h3>
                <p>Try our products with confidence. Simple and hassle-free returns.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="bi-currency-dollar"></i></div>
                <h3>Best price promise</h3>
                <p>Get competitive pricing on premium tech accessories.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="bi bi-truck"></i></div>
                <h3>Fast shipping & returns</h3>
                <p>Quick delivery on all in-stock items with easy returns.</p>
            </div>
            <div class="why-card">
                <div class="why-icon"><i class="bi-person"></i></div>
                <h3>GearGo perks</h3>
                <p>Exclusive deals, early access, and special offers for members.</p>
            </div>
        </div>
    </section>

    <script src="home.js"></script>
    <script src="cart/cart.js"></script>

</body>

</html>