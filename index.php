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

          <div class="cart-item">
          <img src="headphone1.png" alt="Test Product">

          <div class="cart-item-details">
            <h4>GearGo Wireless Headphones</h4>
            <p>Rs. 5,999</p>
            <input type="number" value="1" min="1">
          </div>

          <button class="remove-btn">&times;</button>
    </div>


      <!-- Items will come here dynamically -->
      <p class="empty-cart">Your cart is empty</p>
    </div>

    <div class="cart-footer">
      <div class="cart-total">
        <span>Total:</span>
        <strong id="cartTotal">Rs. 0</strong>
      </div>
      <button class="checkout-btn">Checkout</button>
    </div>
  </div>
  <!-- ================= CART END ================= -->



  <!-- header section -->
  <header>
    <nav>
      <div class="menu-icon" id="menuIcon">☰</div>

      <div class="left-section">
        <div class="nav-links-container" id="navContainer">
          <ul class="nav-links">
            <li><a href="#">Home</a></li>
            <li><a href="category/category.html">Products</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
            <li><a href="orders/orders.html">My Orders</a></li>
          </ul>
          <div class="mobile-menu-icons">
            <a href="#" class="mobile-icon-link">
              <img height="25px" src="assets/svg/user.svg" alt="User">
              <span>My Account</span>
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
        <!-- Desktop search bar -->
        <div class="search-box desktop-search">
          <input id="searchInput" placeholder="Search">
          <button id="clearBtn"><span class="material-symbols-outlined">close</span></button>
          <div class="vline"></div>
          <button><span class="material-symbols-outlined search-icon">search</span></button>
        </div>

        <!-- Mobile search icon -->
        <span class="material-symbols-outlined mobile-search-icon">search</span>

        <a class="nav-svg no-show-svg" href="Register/register.php"><img src="assets/svg/user.svg" alt=""></a>
        <a class="nav-svg" href="javascript:void(0)" onclick="openCart()">
          <img src="assets/svg/cart.svg" alt="Cart">
          </a>
      </div>
    </nav>

    <!-- Mobile search bar -->
    <div class="mobile-search-bar">
      <div class="search-box">
        <input id="mobileSearchInput" placeholder="Search" />
        <button id="mobileClearBtn"><span class="material-symbols-outlined">close</span></button>
        <div class="vline"></div>
        <button><span class="material-symbols-outlined search-icon">search</span></button>
      </div>
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
          <button class="slide-button">Explore Collection</button>
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
          <button class="slide-button">Shop Watches</button>
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
          <h1>Gaming Gear</h1>
        </div>
        <div class="hero-text">
          Elevate your gaming experience with professional-grade equipment
        </div>
        <div class="hero-button">
          <button class="slide-button">View Gaming</button>
        </div>
      </div>
      <div class="hero-image-container">
        <div class="hero-image-box">
          <img src="assets/images/hero-section/controller.png" alt="">
        </div>
      </div>
    </div>

    <div class="hero-container">
      <div class="hero-text-container">
        <div class="hero-heading">
          <h1>Laptops & Tablets</h1>
        </div>
        <div class="hero-text">
          Power and portability for work and play
        </div>
        <div class="hero-button">
          <button class="slide-button">Discover More</button>
        </div>
      </div>
      <div class="hero-image-container">
        <div class="hero-image-box">
          <img src="assets/images/hero-section/laptop.png" alt="">
        </div>
      </div>
    </div>
    <!-- <button class="slide-change-btn prev">&#10094;</button> -->
    <!-- <button class="slide-change-btn next">&#10095;</button> -->

    <div class="dots-container">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>


  </section>

  <!-- hot Products section -->
  <section class="hot-products">
    <h2>🔥 Hot Products</h2>
    <div class="hot-products-grid">
      <?php 
        require_once 'backend/Config/functions.php';
        $conn = dbConnect();
        $products = Random_products(10, $conn);
        $product = $products[0];

        foreach ($products as $product){
      ?>
      <a href="product page/product.php?id=<?php echo $product['product_id'];?>">
        <div class="product-card-premium">
          <!-- <div class="wishlist-icon">
          <img src="svg/Heart.svg" height="25px" alt=""></a>
        </div> -->
          <div class="product-image-container-premium">
            <img src="<?php echo $product['image'] ?>" height="200px" alt="<?php echo $product['title'] ?>"
              class="product-image-premium">

          </div>
          <div class="product-info-section">
            <h3 class="product-title-premium"><?php echo $product['title'] ?></h3>
            <div class="price-comparison-section">
              <span class="current-price-premium"><small>Rs.</small><?php echo $product['price'] ?></span>
              <span class="original-price-premium"><small>Rs.</small><?php echo $product['price'] + ($product['price']*0.1) ?></span>
            </div>
            <button class="add-to-cart-btn-premium">ADD TO CART</button>
          </div>
        </div>
      </a>
      <?php
        }
        $conn->close();
        ?>
    </div>
  </section>

  <!-- Banner  section -->
  <section class="promo-banner-section">
    <div class="promo-banner">
      <picture>
        <!-- Mobile image -->
        <source srcset="assets/images/home-section/banner/geargo-banner-mobile.png" media="(max-width: 768px)">

        <!-- Desktop image -->
        <source srcset="assets/images/home-section/banner/geargo-banner-pc.webp" media="(min-width: 769px)">

        <!-- Fallback -->
        <img src="assets/images/home-section/banner/geargo-banner-pc.webp" alt="GearGo Wireless Earbuds">
      </picture>
      <!-- TEXT OVERLAY -->
      <div class="banner-content">
        <span class="banner-tag">NEW ARRIVAL</span>
        <h2>Power Your Sound, Anywhere</h2>
        <p>Premium tech accessories built for everyday performance.</p>
        <a href="#" class="banner-btn">Shop Now</a>
      </div>
    </div>
  </section>

  <!-- Why Buy Section -->
  <section class="why-geargo">
    <h2>Why buy from <span>GearGo</span></h2>

    <div class="why-grid">
      <div class="why-card">
        <div class="why-icon">
          <i class="bi-arrow-counterclockwise"></i>
        </div>
        <h3>Easy returns</h3>
        <p>Try our products with confidence. Simple and hassle-free returns.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">
          <i class="bi-currency-dollar"></i>
        </div>
        <h3>Best price promise</h3>
        <p>Get competitive pricing on premium tech accessories.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">
          <i class="bi bi-truck"></i>
        </div>
        <h3>Fast shipping & returns</h3>
        <p>Quick delivery on all in-stock items with easy returns.</p>
      </div>

      <div class="why-card">
        <div class="why-icon">
          <i class="bi-person"></i>
        </div>
        <h3>GearGo perks</h3>
        <p>Exclusive deals, early access, and special offers for members.</p>
      </div>
    </div>
  </section>

  <script src="home.js"></script>
  <script src="cart/cart.js"></script>


</body>

</html>