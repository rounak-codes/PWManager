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
    $encrypted_password = encryptPassword($password);

    // Insert user data into the database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$encrypted_password')";
    if (mysqli_query($conn, $sql)) {
        // On success, redirect to managepasswords.html
        header("Location: manage_passwords.html");
        exit;
    } else {
        // On failure, redirect to index.html
        header("Location: index.html");
        exit;
    }
} else {
    // If not a POST request, redirect to index.html
    header("Location: index.html");
    exit;
}