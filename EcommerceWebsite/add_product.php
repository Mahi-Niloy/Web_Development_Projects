<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $seller_id = $_SESSION['user_id'];

    $query = $conn->prepare("INSERT INTO products (seller_id, name, price, description) VALUES (?, ?, ?, ?)");
    $query->bind_param("isds", $seller_id, $name, $price, $description);
    $query->execute();

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
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

        /* Navbar styles */
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
        /* Form container styles */
        .form-container {
            text-align: center;
            margin-top: 120px;
            background-color: #679986; /* Semi-transparent background */
            padding: 40px 60px;
            border-radius: 10px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-container h2 {
            font-size: 28px;
            color: #fff;
            margin-bottom: 20px;
        }

        .form-container input, .form-container textarea {
            padding: 12px 15px;
            margin: 10px 0;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ccc;
            background-color: rgba(255, 255, 255, 0.8); /* Slight transparent background */
            color: #333;
            font-size: 16px;
        }

        .form-container input:focus, .form-container textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .form-container button {
            padding: 12px 20px;
            width: 100%;
            border-radius: 8px;
            margin-top: 15px;
            background-color: #27AE60;
            color: white;
            cursor: pointer;
            font-size: 16px;
            margin-left:15px;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <!-- Navigation bar -->
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

    <!-- Add Product Form -->
    <div class="form-container">
        <h2>Add a New Product</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Price" required step="0.01">
            <textarea name="description" placeholder="Description" required></textarea>
            <button type="submit">Add Product</button>
        </form>
    </div>
</body>
</html>
