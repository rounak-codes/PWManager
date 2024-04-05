<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';
include_once 'db_connect.php';

// Include the password decryption function
include 'password_decryption.php';

// Assuming login is successful
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement for retrieving encrypted password, IV, and encryption key
    $stmt = $conn->prepare("SELECT password, iv, encryption_key FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();

    // Bind the result variables
    $stmt->bind_result($encryptedPassword, $iv, $encryptionKey);
    // Fetch the result
    $stmt->fetch();

    // Close statement
    $stmt->close();

    // If user exists
    if ($encryptedPassword !== null) {
        error_log("Encryption Key: " . bin2hex($encryptionKey));
        error_log("IV: " . bin2hex($iv));
        // Decrypt the stored password using the encryption key retrieved from the database
        $decryptedPassword = decryptPassword($encryptedPassword, $encryptionKey, $iv);
        // Check if the entered password matches the decrypted password
        if ($password === $decryptedPassword) {
            // Passwords match, redirect user to manage_passwords.html or do whatever you need
            header("Location: manage_passwords.html");
            exit();
        } else {
            // Incorrect password, redirect back to login page with an error message
            header("Location: login.html?error=Incorrect credentials");
            exit();
        }
    } else {
        // User not found, redirect back to login page with an error message
        header("Location: login.html?error=User not found");
        exit();
    }
}

