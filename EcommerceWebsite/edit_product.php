<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit;
}

// Check if the product ID is provided in the URL
if (!isset($_GET['product_id'])) {
    echo "No product selected for editing.";
    exit;
}

$product_id = $_GET['product_id'];

// Fetch the product details to prefill the form
$query = "SELECT * FROM products WHERE id = ? AND seller_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $product_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "You do not have permission to edit this product.";
    exit;
}

$product = $result->fetch_assoc();

// Handle form submission for updating product
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $conn->real_escape_string($_POST['price']);
    $description = $conn->real_escape_string($_POST['description']);

    // Update the product in the database
    $update_query = "UPDATE products SET name = ?, price = ?, description = ? WHERE id = ? AND seller_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('ssdii', $name, $price, $description, $product_id, $_SESSION['user_id']);
    if ($update_stmt->execute()) {
        echo "Product updated successfully.";
        header('Location: view_products.php');
        exit;
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
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
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea, button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #088F8F;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #066666;
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
    <div class="container">
        <h1>Edit Product</h1>
        <form method="POST" action="">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label for="price">Price</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required>

            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>

            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>
