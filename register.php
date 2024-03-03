<?php

// Include necessary files
require_once "db_connect.php"; // Include database connection
require_once "password_encryption.php"; // Include password encryption functions

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Load RSA public key from a file or any other source
    $publicKeyPEM = file_get_contents("public_key.pem"); // Example: Load public key from file

    // Encrypt the password using RSA public key
    $encryptedPassword = encryptPassword($password, $publicKeyPEM);

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
