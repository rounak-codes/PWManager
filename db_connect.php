<?php

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

echo "Connected successfully";