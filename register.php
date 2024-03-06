<?php

// Include necessary files
require_once "db_connect.php"; // Include database connection
require_once "password_encryption.php"; // Include password encryption functions

// Function to insert user into database
function insertUser($username, $encryptedPassword)
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

    // Prepare SQL statement to insert user into 'passwords' table
    $sql = "INSERT INTO passwords (username,password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters and execute query
    $stmt->bind_param("ss", $username, $encryptedPassword);
    $result = $stmt->execute();

    // Check if the query was successful
    if ($result === TRUE) {
        $stmt->close();
        $conn->close();
        return true;
    } else {
        $stmt->close();
        $conn->close();
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Load RSA public key from a file or any other source
    $publicKeyPEM = file_get_contents("public_key.pem"); // Example: Load public key from file

    // Encrypt the password using RSA public key
    $encryptedPassword = encryptPassword($password);

    // Check if password encryption was successful
    if ($encryptedPassword === false) {
        echo "Failed to encrypt password.";
        exit(); // Exit script if encryption fails
    }

    // Insert user into database
    if (insertUser($username, $encryptedPassword)) {
        echo "User registered successfully.";
    } else {
        echo "Registration failed.";
    }
}