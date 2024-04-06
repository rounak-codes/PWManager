<?php
// Include your database connection file here
include_once 'db_connect.php';

// Receive username and password from the request body
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];
$password = $data['password'];

// Retrieve the second encryption key from the database
$stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Key exists, retrieve it from the database
    $row = $result->fetch_assoc();
    $encryptionKey = $row['enc_key'];
} else {
    // Second encryption key not found, handle error (e.g., redirect or return error response)
    echo json_encode(['error' => 'Second encryption key not found']);
    exit();
}

// Encrypt the password using the second encryption key
$encryptedPassword = openssl_encrypt($password, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA);

// Insert new password into the database
$stmt = $conn->prepare("INSERT INTO passwords_table (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $encryptedPassword);
$stmt->execute();
$stmt->close();

// Return success message
echo json_encode(['message' => 'Password added successfully']);