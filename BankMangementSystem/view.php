<?php 
include 'connect.php';
session_start(); 

if (!isset($_SESSION['role'])) {
    // If no role is set, redirect to login or authentication page
    header("Location: auth.php");
    exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy(); // End the session
    header("Location: auth.php"); // Redirect to login page
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Accounts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        header {
            background-color: #2A9D8F;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            text-decoration: none;
            color: white;
            font-size: 1rem;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #264653;
            transition: background-color 0.3s, transform 0.3s;
        }

        nav a:hover {
            background-color: #e76f51;
            transform: translateY(-3px);
        }

        /* Table Styles */
        table {
            width: 100%;
            max-width: 900px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 10px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        td {
            color: black;
        }

        th {
            background-color: #2A9D8F;
            color: white;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td a {
            text-decoration: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        td a.edit {
            background-color: #4CAF50; /* Green */
        }

        td a.edit:hover {
            background-color: #45A049;
        }

        td a.delete {
            background-color: #F44336; /* Red */
        }

        td a.delete:hover {
            background-color: #E53935;
        }

        /* Footer Styles */
        footer {
            background-color: #264653;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 0.9rem;
        }

        h2 {
            color: #2A9C8E;
        }

        /* Content Styles */
        .content {
            padding: 20px;
            text-align: center;
            color: white;
        }
    </style>
    <script>
        function confirmDelete(event) {
            if (!confirm("Are you sure you want to delete this account?")) {
                event.preventDefault(); // Prevent the default action if the user cancels.
            }
        }
    </script>
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">Bank Management System</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="search.php">Search</a>
            <a href="?logout=true">Logout</a>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="content">
        <h2>All Accounts</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Balance</th> <!-- Add Balance Column -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch data from database
                $result = $conn->query("SELECT * FROM accounts");

                // Assume the user role is stored in the session
                session_start();
                $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'customer'; // Default role is 'customer'

                // Loop through each row and display it in the table
                while ($row = $result->fetch_assoc()) {
                    // Fetch the balance from the database
                    $balance = number_format($row['balance'], 2); // Format balance to two decimal places
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['phone']}</td>
                            <td>{$row['address']}</td>
                            <td>\${$balance}</td> <!-- Display the balance -->
                            <td>";

                    // Display actions based on user role
                    if ($userRole === 'admin') {
                        // Admin has access to both Edit and Delete
                        echo "<a href='edit.php?id={$row['id']}' class='edit'>Edit</a>
                              <a href='delete.php?id={$row['id']}' class='delete' onclick='confirmDelete(event)'>Delete</a>";
                    } elseif ($userRole === 'customer') {
                        // Customer only has access to Edit
                        echo "<a href='edit.php?id={$row['id']}' class='edit'>Edit</a>";
                    }

                    echo "</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Footer Section -->
    <footer>
        &copy; 2024 Bank Management System. All Rights Reserved.
    </footer>

</body>



</html>
