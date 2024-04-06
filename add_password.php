<?php
// Include your database connection file here

// Receive username and password from the request body
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

// Insert new password into the database
// Example: INSERT INTO passwords_table (username, password) VALUES (?, ?)
// Use prepared statements for security
// $stmt = $conn->prepare("INSERT INTO passwords_table (username, password) VALUES (?, ?)");
// $stmt->bind_param("ss", $username, $password);
// $stmt->execute();
// $stmt->close();

// Return success message
echo json_encode(['message' => 'Password added successfully']);
