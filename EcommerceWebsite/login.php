<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'ecommerce_demo');
$loginWarning = ''; // Initialize the warning message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Get role from the form

    if ($_POST['action'] === 'login') {
        $query = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $query->bind_param("ss", $username, $role);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                echo "<script>window.location.href='index.php';</script>";
                exit;
            }
        }
        $loginWarning = 'Invalid credentials or role. Please try again.'; // Set the warning message
    } elseif ($_POST['action'] === 'register') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $query->bind_param("sss", $username, $hashedPassword, $role);
        if ($query->execute()) {
            echo "<script>alert('Registration successful. You can now log in.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login/Register</title>
    <style>
        /* Same styles as before */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: url('assets/file.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color: #fff;
        }
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            width: 400px;
            padding: 20px;
            text-align: center;
            margin: auto;
            top: 50%;
            transform: translateY(-50%);
        }
        .form-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .form-toggle button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            margin: 0 10px;
            background: linear-gradient(45deg, #ff6b6b, #fbc531);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .form-toggle button.active {
            background: linear-gradient(45deg, #6a89cc, #a29bfe);
        }
        .form-toggle button:hover {
            transform: scale(1.05);
        }
        .form-container {
            display: none;
        }
        .form-container.active {
            display: block;
        }
        form {
            margin-top: 20px;
        }
        input, select, button {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        button {
            background: linear-gradient(45deg, #e84118, #8c7ae6);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        button:hover {
            background: linear-gradient(45deg, #8c7ae6, #e84118);
        }
        .warning {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-toggle">
            <button id="loginToggle" class="active">Login</button>
            <button id="registerToggle">Register</button>
        </div>

        <div id="loginForm" class="form-container active">
            <h2>Login</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role">
                    <option value="customer">Customer</option>
                    <option value="seller">Seller</option>
                </select>
                <input type="hidden" name="action" value="login">
                <button type="submit">Login</button>
            </form>
            <p class="warning"><?php echo $loginWarning; ?></p> <!-- Display the warning message here -->
        </div>

        <div id="registerForm" class="form-container">
            <h2>Register</h2>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role">
                    <option value="customer">Customer</option>
                    <option value="seller">Seller</option>
                </select>
                <input type="hidden" name="action" value="register">
                <button type="submit">Register</button>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginToggle = document.getElementById('loginToggle');
            const registerToggle = document.getElementById('registerToggle');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            loginToggle.addEventListener('click', function () {
                loginToggle.classList.add('active');
                registerToggle.classList.remove('active');
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
            });

            registerToggle.addEventListener('click', function () {
                registerToggle.classList.add('active');
                loginToggle.classList.remove('active');
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
            });
        });
    </script>
</body>
</html>
