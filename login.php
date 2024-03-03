<?php
require_once "db_connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verify user credentials (you'll need a function for this)
    if (verifyUser($conn, $username, $password)) {
        // Start session and store username
        $_SESSION["username"] = $username;
        echo "Login successful. Redirecting...";
        header("Location: dashboard.php"); // Redirect to dashboard or any other page
        exit();
    } else {
        echo "Invalid username or password.";
    }
}

// Function to verify user credentials
// Include the PHP file where your database connection is established
// For example: require 'db_connection.php';

// Function to verify user credentials
function verifyUser($conn, $username, $password) {
    // Prepare and execute a query to retrieve the user's hashed password based on the provided username
    $sql = "SELECT password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row was returned
    if ($result->num_rows == 1) {
        // Fetch the hashed password from the result set
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the provided password against the hashed password
        if (password_verify($password, $hashed_password)) {
            // Passwords match, return true (credentials are valid)
            return true;
        } else {
            // Passwords do not match, return false (credentials are invalid)
            return false;
        }
    } else {
        // No user found with the provided username, return false (credentials are invalid)
        return false;
    }
}

// Example usage:
// Establish your database connection
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";
$conn = new mysqli($servername, $username, $password, $dbname);

// Example usage of the verifyUser function
$username = "example_user";
$password = "example_password";
if (verifyUser($conn, $username, $password)) {
    echo "User authenticated successfully.";
} else {
    echo "Invalid username or password.";
}

// Close the database connection when done
$conn->close();