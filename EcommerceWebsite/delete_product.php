<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

// Check if the user is logged in and has appropriate role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header('Location: login.php');
    exit;
}

// Check if product ID is provided
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Sanitize the input to prevent SQL injection
    $product_id = $conn->real_escape_string($product_id);

    // Remove related cart items first
    $deleteCartQuery = "DELETE FROM cart WHERE product_id = '$product_id'";
    $conn->query($deleteCartQuery);

    // Prepare and execute the delete query for the product
    $query = "DELETE FROM products WHERE id = '$product_id' AND seller_id = '{$_SESSION['user_id']}'";
    
    if ($conn->query($query) === TRUE) {
        // Product deleted successfully, redirect to product listing page
        header('Location: view_products.php');
    } else {
        // Error occurred during deletion
        echo "Error deleting product: " . $conn->error;
    }
} else {
    // No product ID provided
    echo "No product ID provided.";
}

$conn->close();
?>
