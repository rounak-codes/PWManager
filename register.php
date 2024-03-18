<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';
include_once 'db_connect.php';

// Include the password encryption function
include 'password_encryption.php';

// Assuming registration is successful
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Assuming password is submitted from the form

    // Encrypt the password using the function from password_encryption.php
    $encryptionData = encryptPassword($password,$encryptionKey);
    // Prepare SQL statement for storing encrypted password and IV
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, iv) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $encryptionData['encryptedPassword'], $encryptionData['iv']);

    // Execute the statement
    $stmt->execute();

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect user to success page or do whatever you need
    header("Location: manage_passwords.html");
    exit();
}