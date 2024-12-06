<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    height: 100vh; /* Ensures the height spans the viewport */
    background-image: url('assets/file.jpg');
    background-repeat: no-repeat;
    background-size: cover; /* Stretches the image to cover the entire screen */
    background-position: center;
    background-attachment: fixed; /* Ensures the background image is fixed while scrolling */
    color: white;
}



        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #088F8F;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 16px;
            font-weight: bold;
        }

        .navbar a:hover {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
        }

        .logout-btn {
            background-color: #e74c3c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 25px;
        }

        .logout-btn:hover {
            background-color: #c0392b;
        }

        .centered-content {
    display: flex;
    flex-direction: column; /* Ensure vertical stacking of elements */
    justify-content: center; /* Vertically center content */
    align-items: center; /* Horizontally center content */
    height: 100vh; /* Full viewport height */
    text-align: center;
}

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .button-container a button {
            background-color: #2ecc71;
            padding: 15px 25px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            color: white;
            cursor: pointer;
        }

        .button-container a button:hover {
            background-color: #27ae60;
        }

        
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">Ecom</div>
        <div>
            <a href="index.php">Home</a>
            <a href="cart.php">Cart</a>
            <a href="account.php">Account</a>
            <a href="search.php">Search Products</a>
        </div>
        <form method="POST" action="logout.php" style="margin: 0;">
            <button class="logout-btn" type="submit">Logout</button>
        </form>
    </div>

    <!-- Centered Welcome Text -->
    <div class="centered-content">
        <h1 class="welcome-text">Welcome to Your Dashboard!</h1>
        <h3 class="subtitle">Manage your products and view your cart</h3>
        
        <!-- Buttons Based on Role -->
        <div class="button-container">
            <a href="view_products.php"><button>View Products</button></a>
            <?php if ($role === 'seller'): ?>
                <a href="add_product.php"><button>Add Products</button></a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
