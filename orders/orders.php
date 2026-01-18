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
  exit(); // Stop here so it don't load the HTML
}

// Check Login Status
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;

// Define Paths
$loginPagePath = "../Login/user/login_user.php";
$profilePagePath = "../user profile/user.php";
$accountLink = $isLoggedIn ? $profilePagePath : $loginPagePath;

// Fetch Cart Data
$cartItems = [];
$cartTotal = 0;
if ($isLoggedIn) {
  $cartItems = getCartItems($conn, $user_id);
}

// FETCH ORDERS (New Logic)
$myOrders = [];
if ($isLoggedIn) {
  $myOrders = getUserOrders($conn, $user_id);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders | GearGo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
  <link rel="stylesheet" href="orders.css">
  <link rel="stylesheet" href="../cart/cart.css">
  <link rel="stylesheet" href="../home.css">
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
          $itemTotal = $item['price'] * $item['quantity'];
          $cartTotal += $itemTotal;
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
        <p class="empty-cart" style="display:block;">Please <a href="<?php echo $loginPagePath; ?>">login</a> to view your
          cart.</p>
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
  <header>
    <nav>
      <div class="menu-icon" id="menuIcon">☰</div>

      <div class="left-section">
        <div class="nav-links-container" id="navContainer">
          <ul class="nav-links">
            <li><a href="../index.php">Home</a></li>
            <li><a href="../category/category.php">Products</a></li>
            <li><a href="../orders/orders.php" class="active">My Orders</a></li>
            <li><a href="../contact/contact.php">Contact</a></li>
            <li><a href="../about/about.php">About</a></li>
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
        <form action="../search/search.php" method="GET" class="search-box desktop-search" style="position:relative;">
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
      <form action="search/search.php" method="GET" class="search-box" style="position:relative;">
        <input name="search" id="mobileSearchInput" placeholder="Search" autocomplete="off" />

        <button type="button" id="mobileClearBtn"><span class="material-symbols-outlined">close</span></button>
        <div class="vline"></div>
        <button type="submit"><span class="material-symbols-outlined search-icon">search</span></button>

        <div id="mobileSearchResultsList" class="search-suggestions-box"></div>
      </form>
    </div>
  </header>

  <main class="orders-page">
    <div class="container">
      <h1 class="page-title">My Orders</h1>

      <?php if (!$isLoggedIn): ?>
        <p>Please <a href="<?php echo $loginPagePath; ?>" style="color:blue;">login</a> to view your orders.</p>

      <?php elseif (empty($myOrders)): ?>
        <p>You have no orders yet.</p>

      <?php else: ?>
        <?php foreach ($myOrders as $order): ?>
          <?php
          // Format Date
          $dateObj = new DateTime($order['order_date']);
          $formattedDate = $dateObj->format('F j, Y');
          $status = ucfirst($order['order_status']);
          ?>

          <div class="order-card">
            <div class="order-header">
              <div class="meta-column">
                <span class="label">Order Placed</span>
                <span class="value"><?php echo $formattedDate; ?></span>
              </div>
              <div class="meta-column">
                <span class="label">Total</span>
                <span class="value">Rs. <?php echo number_format($order['total_amount']); ?></span>
              </div>
              <div class="meta-column">
                <span class="label">Ship To</span>
                <span class="value user-name">Me <i class="bi bi-chevron-down"></i></span>
              </div>
              <div class="order-number">
                <span class="label">Order # <?php echo $order['order_id']; ?></span>
              </div>
            </div>

            <div class="order-content">
              <div class="status-container">
                <h3 class="delivery-status"><?php echo $status; ?></h3>
                <p class="status-subtext">
                  <?php echo ($status == 'Pending') ? 'We are processing your order.' : 'Your package is on its way.'; ?>
                </p>
              </div>

              <?php foreach ($order['items'] as $item): ?>
                <?php
                $prodImg = !empty($item['image']) ? '../' . $item['image'] : '../headphone1.png';
                $prodLink = "../product page/product.php?id=" . $item['product_id'];
                ?>
                <div class="product-listing">
                  <div class="product-thumb">
                    <a href="<?php echo $prodLink; ?>">
                      <img src="<?php echo $prodImg; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    </a>
                  </div>
                  <div class="product-details">
                    <a href="<?php echo $prodLink; ?>" class="product-name">
                      <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                    <p class="return-window">Qty: <?php echo $item['quantity']; ?> | Price: Rs.
                      <?php echo number_format($item['price_at_purchase']); ?></p>

                    <div class="action-buttons">
                      <form action="../cart/add_to_cart.php" method="POST" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="buy_now" value="true">
                        <button type="submit" class="btn-secondary">Buy it again</button>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </main>
</body>

<script src="../home.js"></script>
<script src="../cart/cart.js"></script>
<script src="orders.js"></script>

</html>