<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch all products from the database
$query = "SELECT p.id, p.name, p.price, p.description, u.username AS seller_name 
          FROM products p
          JOIN users u ON p.seller_id = u.id";
$products = $conn->query($query);

?>
<!DOCTYPE html>
<html>
<head>
    <title>View Products</title>
    <style>
        /* General Styles */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    height: 100vh;
    background-image: url('assets/file.jpg'); /* Use the same image path as your homepage */
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    color: white;
}

/* Navigation Bar */
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

/* Product Container */
.product-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
    padding: 80px 20px 20px; /* Add top padding to adjust for fixed navbar */
}

.product-card {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 15px;
    width: 300px;
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.product-card h3 {
    margin: 0;
    font-size: 1.5em;
    color: #088F8F;
}

.product-card p {
    margin: 10px 0;
}

.add-to-cart-btn,
.edit-btn,
.delete-btn {
    background-color: #2ecc71;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin: 5px;
}

.add-to-cart-btn:hover,
.edit-btn:hover,
.delete-btn:hover {
    background-color: #27ae60;
}

.edit-btn {
    background-color: #3498db;
}

.edit-btn:hover {
    background-color: #2980b9;
}

.delete-btn {
    background-color: #e74c3c;
}

.delete-btn:hover {
    background-color: #c0392b;
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

    <!-- Product Container -->
    <div class="product-container">
        <?php while ($product = $products->fetch_assoc()): ?>
            <div class="product-card">
                <h3><?= $product['name'] ?></h3>
                <p>Price: $<?= $product['price'] ?></p>
                <p><?= $product['description'] ?></p>
                <p>Seller: <?= $product['seller_name'] ?></p>
                <?php if ($_SESSION['role'] === 'customer'): ?>
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                    </form>
                <?php elseif ($_SESSION['role'] === 'seller'): ?>
                    <form method="GET" action="edit_product.php" style="display: inline-block;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="edit-btn">Edit</button>
                    </form>
                    <form method="POST" action="delete_product.php" style="display: inline-block;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
