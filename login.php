<?php
require 'vendor/autoload.php';
include_once 'db_connect.php';
include 'password_decryption.php';

session_start(); // Start the session

// Check if the encryption key exists in the database
$stmt = $conn->prepare("SELECT enc_key FROM enkeys LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Key exists, retrieve it from the database
    $row = $result->fetch_assoc();
    $encryptionKey = $row['enc_key'];
} else {
    // Handle the case where the encryption key doesn't exist
    echo "Encryption key not found. Please contact support.";
    exit();
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Sanitize input (optional but recommended)
    $username = htmlspecialchars($username);
    $password = htmlspecialchars($password);

    // Prepare SQL statement for retrieving encrypted password and IV
    $stmt = $conn->prepare("SELECT encrypted_password, iv FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch the result
        $row = $result->fetch_assoc();
        $encryptedPassword = $row['encrypted_password'];
        $iv = $row['iv'];

        // Decrypt the stored password using the encryption key retrieved from the database
        $decryptedPassword = decryptPassword($encryptedPassword, $encryptionKey, $iv);

        // Check if the entered password matches the decrypted password
        if ($password === $decryptedPassword) {
            // Passwords match, redirect user to dashboard.html or other page
            $_SESSION['username'] = $username;
            header("Location: dashboard.html");
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