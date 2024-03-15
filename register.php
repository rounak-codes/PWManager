<?php
include_once "db_connect.php";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include database connection file
    include_once "db_connect.php";
    
    // Function to validate email
    function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Validate email
    if (!isValidEmail($email)) {
        header("Location: index.html"); // Redirect to index.html if email is invalid
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: index.html"); // Redirect to index.html if passwords do not match
        exit;
    }

    // Include the encryption function
    include_once "password_encryption.php";

    // Encrypt the password
    $encryptionData = encryptPassword($password);
    $encrypted_password = $encryptionData['encryptedPassword'];
    // Insert user data into the database
    $sql = $conn->prepare("INSERT INTO users (username,email,pass) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $username, $email, $encrypted_password);
    
    // Execute the statement
    if ($sql->execute()) {
        // Redirect to success page
        header("Location: private_key.html");
        exit;
    } else {
        // Redirect to error page
        header("Location: index.html");
        exit;
    }
} else {
    // If not a POST request, redirect to index.html
    header("Location: index.html");
    exit;
}