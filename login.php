<?php

// Include necessary files
require_once "db_connect.php"; // Include database connection
require_once "password_encryption.php"; // Include password encryption functions

// Function to verify user's login credentials
function verifyLogin($username, $password)
{
    $servername = "localhost";
    $username = "rounak";
    $password = "rounakbag24";
    $database = "RounakDB";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to select user from 'users' table
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute query
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row["password"];

        // Decrypt the stored password using RSA private key
        $privateKeyPEM = file_get_contents("private.pem"); // Example: Load private key from file
        $decryptedPassword = decryptPassword($password);

        // Verify the password
        if ($decryptedPassword === $password) {
            echo "Login Successful , Redirecting now...";
            return true; // Login successful
        } else {
            echo "Incorrect password";
            return false; // Incorrect password
        }
    } else {
        echo "User not found!";
        return false; // User not found
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verify user's login credentials
    if (verifyLogin($username, $password)) {
        header("Location: manage_passwords.html");
        exit();
    } else {
        header("Location : index.html");
        exit();
    }
}