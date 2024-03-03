<?php

// Include necessary files
require_once "db_connect.php"; // Include database connection
require_once "password_encryption.php"; // Include password encryption functions

session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Retrieve password details
if (isset($_GET["password_id"])) {
    $username = $_SESSION["username"];
    $passwordId = $_GET["password_id"];
    $password = getPassword($username, $passwordId); // Get password details from database

    if ($password) {
        $decryptedPassword = decryptPassword($password["encrypted_password"]); // Decrypt password using RSA
        echo "Password: " . $decryptedPassword . "<br>";
        echo "<button onclick='copyPassword(\"$decryptedPassword\")'>Copy Password</button>";
    } else {
        echo "Password not found.";
    }
} else {
    echo "Password ID not provided.";
}

// Function to retrieve password details from the database
function getPassword($username, $passwordId) {
    global $conn; // Get access to the database connection object

    // Prepare and execute SQL query to retrieve password details
    $sql = "SELECT * FROM passwords WHERE username = ? AND id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $passwordId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a row is returned
    if ($result->num_rows == 1) {
        return $result->fetch_assoc(); // Return password details
    } else {
        return false; // Return false if password is not found
    }
}
