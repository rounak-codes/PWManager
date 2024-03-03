<?php

// Include database connection and RSA encryption functions
require_once "db_connect.php";
require_once "password_encryption.php";
require_once "key_gen.php";

session_start();

// Check if user is logged in
if (!isset($_SESSION["username"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Add new password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_password"])) {
    $username = $_SESSION["username"];
    $password = $_POST["password"];
    $encryptedPassword = encryptPassword($password); // Encrypt the password using RSA
    if (addPassword($username, $encryptedPassword)) {
        echo "Password added successfully.";
    } else {
        echo "Failed to add password.";
    }
}

// Edit password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_password"])) {
    $username = $_SESSION["username"];
    $passwordId = $_POST["password_id"];
    $newPassword = $_POST["new_password"];
    if (editPassword($username, $passwordId, $newPassword)) {
        echo "Password edited successfully.";
    } else {
        echo "Failed to edit password.";
    }
}

// Delete password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_password"])) {
    $username = $_SESSION["username"];
    $passwordId = $_POST["password_id"];
    if (deletePassword($username, $passwordId)) {
        echo "Password deleted successfully.";
    } else {
        echo "Failed to delete password.";
    }
}

// Retrieve and display passwords
$username = $_SESSION["username"];
$passwords = getPasswords($username); // Get passwords from database

foreach ($passwords as $password) {
    echo "Password ID: " . $password["id"] . "<br>";
    echo "Password: " . decryptPassword($password["encrypted_password"]) . "<br>"; // Decrypt password using RSA
    echo "<button onclick='editPassword(" . $password["id"] . ")'>Edit</button>";
    echo "<button onclick='deletePassword(" . $password["id"] . ")'>Delete</button><br><br>";
}

?>

<!-- HTML form to add new password -->
<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
    <label for="password">New Password:</label>
    <input type="password" id="password" name="password" required>
    <input type="submit" name="add_password" value="Add Password">
</form>
