<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize search results
$searchResults = [];

// Handle the search query
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_query'])) {
    $searchQuery = $conn->real_escape_string($_GET['search_query']);
    
    $query = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
    $searchTerm = '%' . $searchQuery . '%';
    $query->bind_param("s", $searchTerm);
    $query->execute();
    
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }

    $query->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products</title>
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
        .search-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 40vh; /* Full viewport height */
            text-align: center;
        }
        .search-container form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .search-container input {
            width: 300px;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
        }
        .search-container button {
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            background-color: #2ecc71;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .search-container button:hover {
            background-color: #27ae60;
        }
        .results-container {
            padding: 20px;
            text-align: center;
        }
        .product-card {
    background: linear-gradient(145deg, #4CAF50, #2c6e49); /* Gradient background */
    padding: 5px 8px; /* Further reduced left and right padding to 8px */
    border-radius: 15px; /* Rounded corners */
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden; /* To ensure rounded corners are respected */
    position: relative;
    max-width: 40%; /* Further reduced width */
    margin: 0 auto; /* Centers the card horizontally */
}

.product-card h3 {
    font-size: 24px; /* Bigger font size for emphasis */
    margin: 0 0 15px;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase; /* Uppercase letters for a bold, modern feel */
    letter-spacing: 1px;
}

.product-card p {
    font-size: 18px;
    color: #e1e1e1; /* Light grey color for description text */
    line-height: 1.5;
    margin-bottom: 15px;
}

.product-card:hover {
    transform: translateY(-10px); /* Slight lift effect on hover */
    box-shadow: 0px 15px 30px rgba(0, 0, 0, 0.3);
}

.product-card .price {
    font-size: 22px;
    color: #FFD700; /* Golden color for price */
    font-weight: bold;
    position: absolute;
    bottom: 20px;
    left: 20px;
}

.product-card .cta-button {
    background-color: #ff7f50; /* Coral color for call to action */
    color: white;
    border-radius: 25px;
    border: none;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease;
    position: absolute;
    bottom: 20px;
    right: 20px;
}

.product-card .cta-button:hover {
    background-color: #ff5733; /* Darker coral on hover */
}



        .edit-delete-buttons {
            margin-top: 10px;
        }
        .edit-delete-buttons a {
            color: #2ecc71;
            margin-right: 10px;
        }
        .edit-delete-buttons a:hover {
            color: #27ae60;
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

<!-- Centered Search Bar -->
<div class="search-container">
    <form method="GET" action="search.php">
        <input type="text" name="search_query" placeholder="Search for products..." 
            value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
        <button type="submit">Search</button>
    </form>
</div>

<!-- Results Section -->
<div class="results-container">
    <?php if (!empty($searchResults)): ?>
        <h2>Search Results:</h2>
        <?php foreach ($searchResults as $product): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                <p><?php echo htmlspecialchars($product['description']); ?></p>

                <!-- Edit and Delete options visible only for logged-in sellers -->
                <?php if (isset($_SESSION['seller_id'])): ?>
                    <div class="edit-delete-buttons">
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_query'])): ?>
        <h2>No results found for "<?php echo htmlspecialchars($_GET['search_query']); ?>"</h2>
    <?php endif; ?>
</div>
</body>
</html>
