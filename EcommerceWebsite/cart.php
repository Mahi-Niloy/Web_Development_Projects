<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

// Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;

    // Check if the product is already in the cart
    $check_query = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check_query->bind_param("ii", $user_id, $product_id);
    $check_query->execute();
    $result = $check_query->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if the product already exists in the cart
        $cart_item = $result->fetch_assoc();
        $new_quantity = $cart_item['quantity'] + $quantity; // Increase the quantity
        $update_query = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_query->bind_param("ii", $new_quantity, $cart_item['id']);
        $update_query->execute();
    } else {
        // Add new product to the cart
        $query = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $query->bind_param("iii", $user_id, $product_id, $quantity);
        $query->execute();
    }
}

// Handle quantity modification (increment or decrement)
if (isset($_GET['action']) && isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];
    $action = $_GET['action'];

    // Get the current quantity
    $cart_query = $conn->prepare("SELECT quantity FROM cart WHERE id = ?");
    $cart_query->bind_param("i", $item_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result();
    $cart_item = $cart_result->fetch_assoc();
    $current_quantity = $cart_item['quantity'];

    // Update quantity based on action
    if ($action === 'increase') {
        $new_quantity = $current_quantity + 1;
    } elseif ($action === 'decrease' && $current_quantity > 1) {
        $new_quantity = $current_quantity - 1;
    } else {
        $new_quantity = $current_quantity;
    }

    // Update the cart
    $update_query = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $update_query->bind_param("ii", $new_quantity, $item_id);
    $update_query->execute();

    // Redirect back to the cart page
    header('Location: cart.php');
    exit;
}

// Handle product removal
if (isset($_GET['remove']) && isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];

    // Delete the product from the cart
    $remove_query = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $remove_query->bind_param("i", $item_id);
    $remove_query->execute();

    // Redirect back to the cart page
    header('Location: cart.php');
    exit;
}

// Handle Buy button action
if (isset($_POST['buy'])) {
    $user_id = $_SESSION['user_id'];

    // Check if the cart is empty
    $cart_count_query = $conn->prepare("SELECT COUNT(*) as total_items FROM cart WHERE user_id = ?");
    $cart_count_query->bind_param("i", $user_id);
    $cart_count_query->execute();
    $cart_count_result = $cart_count_query->get_result();
    $cart_count = $cart_count_result->fetch_assoc()['total_items'];

    if ($cart_count > 0) {
        // Clear the cart for the customer
        $clear_cart_query = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $clear_cart_query->bind_param("i", $user_id);
        $clear_cart_query->execute();

        // Show success message
        echo "<script>
            alert('Congratulations on your purchase!');
            window.location.href = 'cart.php';
        </script>";
        exit;
    } else {
        // Show a message to view products if the cart is empty
        echo "<script>
            alert('Your cart is empty! Browse products to add items to your cart.');
            window.location.href = 'index.php';
        </script>";
        exit;
    }
}

// Fetch cart items
$cart_items = $conn->query("SELECT c.id, p.name, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = " . $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('assets/file.jpg');
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
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
            padding-left:15px;
        }

        .form-container {
            width: 400px;
            padding: 40px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #0c7094;
            color: black;
        }

        .form-container h2 {
            text-align: center;
        }

        .cart-item {
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fff;
            color: black;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cart-item span {
            font-weight: bold;
        }

        .cart-item .actions {
            display: flex;
            align-items: center;
        }

        .cart-item button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cart-item button:hover {
            background-color: #45a049;
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

        .buy-section {
    margin-top: 20px;
    text-align: center;
}

.buy-btn {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
}

.buy-btn:hover {
    background-color: #45a049;
}
    </style>
</head>
<body>

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

    <div class="form-container">
        <h2>Your Cart</h2>
        <ul>
            <?php while ($item = $cart_items->fetch_assoc()): ?>
                <li class="cart-item">
                    <span><?= $item['name'] ?> - $<?= $item['price'] ?></span>
                    <div class="actions">
                        <a href="cart.php?action=increase&item_id=<?= $item['id'] ?>"><button>+</button></a>
                        <span><?= $item['quantity'] ?></span>
                        <a href="cart.php?action=decrease&item_id=<?= $item['id'] ?>"><button>-</button></a>
                        <a href="cart.php?remove=true&item_id=<?= $item['id'] ?>"><button>Remove</button></a>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="buy-section">
            <?php if ($_SESSION['role'] === 'customer'): ?>
                <form method="POST" action="cart.php">
                    <button type="submit" name="buy" class="buy-btn">Buy</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</body>

</body>
</html>
