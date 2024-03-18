<?php
// Include your database connection file here

// Fetch passwords from the database
// Example: SELECT id, username, password FROM passwords_table
$passwords = [
    ['id' => 1, 'username' => 'user1', 'password' => 'password1'],
    ['id' => 2, 'username' => 'user2', 'password' => 'password2'],
    // Add more passwords as needed
];

// Return passwords as JSON
header('Content-Type: application/json');
echo json_encode($passwords);
