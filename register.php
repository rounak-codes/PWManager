<?php

// Include necessary files
require_once "db_connect.php"; // Include database connection
require_once "password_encryption.php"; // Include password encryption functions

function validateEmail($email) {
    // First, validate the email format using PHP's built-in filter_var function
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }

    // Split the email address into user and domain parts
    list($user, $domain) = explode('@', $email);

    // Check if the domain has MX (Mail Exchange) records
    if (!checkdnsrr($domain, 'MX')) {
        return "Invalid domain or domain does not accept email";
    }

    // Check if the domain is configured to accept emails
    // You may want to implement a more sophisticated check here, such as sending a test email
    // For simplicity, we'll just assume the domain accepts emails
    return "Email is valid";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    // Validate email
    $emailValidationResult = validateEmail($email);

    if ($emailValidationResult !== "Email is valid") {
        // Email is not valid, handle error
        echo $emailValidationResult;
        exit; // Exit PHP script to stop further execution
    }

    // Proceed with the rest of your registration logic
    // For example, you can insert user data into a database

    // Example:
    // $dbConnection = new mysqli("localhost", "username", "password", "database");
    // $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    // $result = $dbConnection->query($query);

    // if ($result) {
    //     echo "Registration successful";
    // } else {
    //     echo "Registration failed";
    // }

    // Close database connection if necessary
    // $dbConnection->close();
}

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

    // Prepare SQL statement to insert user into 'users' table
    $sql = "INSERT INTO users (username,password) VALUES (?, ?)";
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