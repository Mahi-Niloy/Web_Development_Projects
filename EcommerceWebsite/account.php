<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$user_result = $query->get_result();
$user = $user_result->fetch_assoc();

// Update user details if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the new password before updating (ensure it's secure)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update user details in the database
    $update_query = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
    $update_query->bind_param("ssi", $username, $hashed_password, $user_id);
    $update_query->execute();

    // Reload the page to show updated details
    header('Location: account.php');
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Account</title>
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
    display: flex;
    justify-content: center;
    align-items: center;
}

.form-container {
    width: 400px;
    padding: 40px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #f9f9f9;
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

        
        .form-container h2 {
            text-align: center;
            color:black;
        }
        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .form-container button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-container button:hover {
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
    

    <!-- Account Information Form -->
    <div class="form-container">
        <h2>Profile Information</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($user['username']) ?>" required>
            <input type="password" name="password" placeholder="New Password" required>
            <button type="submit">Update Info</button>
        </form>
    </div>

    
</body>
</html>
