<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "booking_db"; // Ensure this database and table are set up

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $conn->real_escape_string($_POST["first_name"]);
    $last_name = $conn->real_escape_string($_POST["last_name"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $date = $conn->real_escape_string($_POST["date"]);
    $time = $conn->real_escape_string($_POST["time"]);
    $location = $conn->real_escape_string($_POST["location"]);
    $message = $conn->real_escape_string($_POST["message"]);

    if (!empty($first_name) && !empty($last_name) && !empty($email) && !empty($date) && !empty($time) && !empty($location)) {
        $sql = "INSERT INTO bookings (first_name, last_name, email, date, time, location, message) 
                VALUES ('$first_name', '$last_name', '$email', '$date', '$time', '$location', '$message')";

        if ($conn->query($sql) === TRUE) {
            $successMessage = "Booking successfully created!";
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    } else {
        $errorMessage = "All fields except message are required!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Singer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .booking-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .booking-form h2 {
            margin-top: 0;
        }
        .booking-form .row {
            display: flex;
            justify-content: space-between;
        }
        .booking-form .row input {
            width: 48%;
        }
        .booking-form input, .booking-form textarea, .booking-form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .booking-form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .booking-form button:hover {
            background-color: #45a049;
        }
        .success, .error {
            text-align: center;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .success { background-color: #4CAF50; }
        .error { background-color: #f44336; }
    </style>
</head>
<body>

<div class="booking-form">
    <h2>Book Joe Dreamz</h2>
    <?php if (!empty($successMessage)) echo "<div class='success'>$successMessage</div>"; ?>
    <?php if (!empty($errorMessage)) echo "<div class='error'>$errorMessage</div>"; ?>
    <form action="booking.php" method="post">
        <div class="row">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
        </div>
        <input type="email" name="email" placeholder="Your Email" required>
        <div class="row">
            <input type="date" name="date" required>
            <input type="time" name="time" required>
        </div>
        <input type="text" name="location" placeholder="Location" required>
        <textarea name="message" placeholder="Additional Message"></textarea>
        <button type="submit">Submit Booking</button>
    </form>
</div>

</body>
</html>
