<?php
require 'vendor/autoload.php';
include_once 'db_connect.php';
include 'password_encryption.php';

// Check if the encryption key exists in the database and retrieve it
$stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2"); // Assuming ID 2 is the key for managing passwords
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Key exists, retrieve it from the database
    $row = $result->fetch_assoc();
    $encryptionKey = $row['enc_key'];
} else {
    // Handle the case where the encryption key is not found (optional)
    die("Encryption key not found.");
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Encrypt password using the encryption key
    $encryptionData = encryptPassword($password, $encryptionKey);

    // Encode encrypted password for MySQL storage
    $encryptedPassword = base64_encode($encryptionData['encryptedPassword']);

    // Encode encryption key for MySQL storage (assuming it's binary)
    $encodedEncryptionKey = base64_encode($encryptionKey);

    // Prepare SQL statement for storing encrypted user data
    $stmt = $conn->prepare("INSERT INTO passwords (username, pwds, iv, encryption_key) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $encryptedPassword, $encryptionData['iv'], $encodedEncryptionKey);

    // Execute the statement
    $stmt->execute();

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect user to manage_passwords.html or any other page
    header("Location: manage_passwords.html");
    exit();
}