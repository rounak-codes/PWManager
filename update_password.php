<?php
session_start();
require 'vendor/autoload.php';
include_once 'db_connect.php';
include 'password_encryption.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $siteName = $_POST['site_name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve the encryption key from the database
    $stmt = $conn->prepare("SELECT enc_key FROM enkeys LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $encryptionKey = $row['enc_key'];
    } else {
        die("Encryption key not found.");
    }

    $stmt->close();

    // Encrypt the new password
    $encryptionData = encryptPassword($password, $encryptionKey);

    // Update the password details in the database
    $stmt = $conn->prepare("UPDATE passwords SET site_name = ?, username = ?, pwds = ?, iv = ?, encryption_key = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $siteName, $username, $encryptionData['encryptedPassword'], $encryptionData['iv'], $encryptionKey, $id);

    $stmt->execute();
    $stmt->close();
    $conn->close();

    header("Location: manage_passwords.html");
    exit();
}
