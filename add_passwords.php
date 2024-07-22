<?php
session_start();
include_once 'db_connect.php';
include_once 'password_encryption.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

$username = $_SESSION['username'];

// Fetch encryption key
$stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $encryptionKey = $row['enc_key'];
} else {
    die("Encryption key not found.");
}

$stmt->close();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action'])) {
        $siteName = $_POST['site_name'];
        $pwd = $_POST['password'];
        $formUsername = $_POST['username']; // Get username from form

        switch ($_POST['action']) {
            case 'add':
                // Handle add password
                $encryptionData = encryptPassword($pwd, $encryptionKey);
                $stmt = $conn->prepare("INSERT INTO passwords (site_name, username, pwds, iv, encryption_key, user) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $siteName, $formUsername, $encryptionData['encryptedPassword'], $encryptionData['iv'], $encryptionKey, $username);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('Password has been added successfully.');</script>";
                break;

            case 'edit':
                // Handle edit password
                $id = $_POST['id'];
                $encryptionData = encryptPassword($pwd, $encryptionKey);
                $stmt = $conn->prepare("UPDATE passwords SET site_name = ?, pwds = ?, iv = ?, encryption_key = ? WHERE id = ? AND username = ?");
                $stmt->bind_param("sssssi", $siteName, $encryptionData['encryptedPassword'], $encryptionData['iv'], $encryptionKey, $id, $formUsername);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('Password has been updated successfully.');</script>";
                break;

            case 'delete':
                // Handle delete password
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM passwords WHERE id = ? AND username = ?");
                $stmt->bind_param("is", $id, $formUsername);
                $stmt->execute();
                $stmt->close();
                echo "<script>alert('Password has been deleted successfully.');</script>";
                break;
        }
    }

    $conn->close();
    echo "<script>window.location.href = 'dashboard.html';</script>";
    exit();
}
