<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';
include_once 'db_connect.php';

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;

// Function to generate key pair and encrypt password
function encryptPassword($password) {
    // Generate RSA key pair
    $private = RSA::createKey(2048);
    $public = $private->getPublicKey();

    $key=PublicKeyLoader::load($public)->withHash('sha256');

    $encryptedPassword=$key->encrypt($password);
    $filename = 'master_key.txt';
    file_put_contents($filename, $private);
    return array(
        'encryptedPassword' => base64_encode($encryptedPassword),
        'privateKey' => $private->toString('PKCS1'),'publicKey' => $public);
    
}
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming registration is successful
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Assuming password is submitted from the form

    // Encrypt the password and get keys
    $encryptionData = encryptPassword($password);
    $encryptedPassword = $encryptionData['encryptedPassword'];
    $privateKey = $encryptionData['privateKey'];
    $publicKey = $encryptionData['publicKey'];

    // Prepare SQL statement
    
    $stmt = $conn->prepare("INSERT INTO user_keys (username, email, public_key, private_key) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $publicKey, $privateKey);
    $encryptionData = encryptPassword($password);
    $encrypted_password = $encryptionData['encryptedPassword'];
    // Insert user data into the database
    $sql = $conn->prepare("INSERT INTO users (username,email,pass) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $username, $email, $encrypted_password);
    // Execute the statement
    $stmt->execute();
    $sql->execute();
    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Redirect user to success page or do whatever you need
    header("Location: private_key.html");
    exit();
}