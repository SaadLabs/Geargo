<?php
require_once("../Backend/config/session_manager.php");

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Check for error messages
$errorMessage = "";
if (isset($_GET['error'])) {
    $errorMessage = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>GearGo | Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="register.css">
</head>

<body>
    <div class="container">

        <div class="left">
            <div class="logo">
                <img src="../assets/logo/logo_white_bgr_rem.png" alt="GearGo Logo">
            </div>

            <div class="content">
                <h1>GearGo</h1>
                <p>Create your account and start shopping smart</p>
                <div class="icons">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <i class="fa-solid fa-truck-fast"></i>
                    <i class="fa-solid fa-shield-halved"></i>
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>

        <div class="right">
            <form class="register-form" id="registerForm" method="post" action="reg_action.php">
                
                <h2>Create Account</h2>

                <?php if ($errorMessage): ?>
                    <p style="color: red; text-align: center; margin-bottom: 15px;">
                        <i class="fa-solid fa-circle-exclamation"></i> 
                        <?php echo $errorMessage; ?>
                    </p>
                <?php endif; ?>

                <input type="hidden" id="role" name="role" value="customer" required>

                <div class="input-group">
                    <input type="text" id="name" name="name" placeholder="Full Name" required>
                </div>

                <div class="input-group">
                    <input type="email" id="email" name="email" placeholder="Email Address" required>
                </div>

                <div class="input-group">
                    <input type="tel" id="mobile" name="phone" placeholder="Mobile Number" required>
                </div>

                <div class="input-group password">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fa-solid fa-eye" id="togglePassword"></i>
                </div>

                <div class="input-group">
                    <input type="password" id="confirmPassword" name="conf_pass" placeholder="Confirm Password" required>
                </div>

                <button type="submit">Create Account</button>

                <p class="login-text">
                    Already have an account?
                    <a href="../Login/user/login_user.php">Login</a>
                </p>

                <p class="error" id="errorMsg"></p>
            </form>
        </div>

    </div>

    <script src="register.js"></script>
</body>
</html>