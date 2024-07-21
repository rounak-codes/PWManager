<?php
session_start();
include_once 'db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo "User not logged in.";
    exit();
}

$username = $_SESSION['username'];

// Prepare SQL statement for retrieving passwords
$stmt = $conn->prepare("SELECT site_name, username, pwds FROM passwords WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$passwords = [];

while ($row = $result->fetch_assoc()) {
    $passwords[] = $row;
}

$stmt->close();
$conn->close();

// Encode data to JSON
header('Content-Type: application/json');
echo json_encode($passwords);
