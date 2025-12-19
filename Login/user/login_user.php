<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>GearGo | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="login_user.css">
</head>

<body>

    <div class="container">

        <!-- LEFT BLUE SECTION -->
        <div class="left">
            <div class="logo">
                <img src="../../assets/logo/logo_white_bgr_rem.png" alt="GearGo Logo">
            </div>

            <div class="content">
                <h1>GearGo</h1>
                <p>Login your account and start shopping smart</p>

                <div class="icons">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <i class="fa-solid fa-truck-fast"></i>
                    <i class="fa-solid fa-shield-halved"></i>
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
        </div>



        <!-- RIGHT WHITE SECTION -->
        <div class="right">
            <form class="register-form" id="loginForm" method="post" action="log_action.php">

                <h2>Login</h2>

                <div class="input-group">
                    <input type="email" id="email" placeholder="Email Address" name = "email" required>
                </div>

                <div class="input-group password">
                    <input type="password" id="password" name = "password" placeholder="Password" required>
                    <i class="fa-solid fa-eye" id="togglePassword"></i>
                </div>

                <button type="submit">Login</button>

                <p class="login-text">
                    Don't have an account?
                    <a href="../../Register/register.php">Register</a>
                </p>

                <p class="admin-login">
                    <a href="../admin/login_admin.php">Login as Admin</a>
                </p>

                <p class="error" id="errorMsg"></p>
            </form>
        </div>

    </div>

    <script src="login_user.js"></script>
</body>

</html>