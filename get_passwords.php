<?php
// Include your database connection file here
include_once 'db_connect.php';

// Fetch passwords from the database
$passwords = [];
$stmt = $conn->prepare("SELECT id, username, password FROM passwords");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $passwords[] = $row;
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Return passwords as JSON
header('Content-Type: application/json');
echo json_encode($passwords);