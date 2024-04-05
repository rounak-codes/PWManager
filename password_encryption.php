<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';

use phpseclib3\Crypt\AES;

// Function to generate a random initialization vector (IV)
function generateIV() {
    return random_bytes(16); // 128-bit IV for AES
}

// Function to generate a random encryption key
function generateEncryptionKey() {
    return random_bytes(32); // 256-bit key for AES-256
}

// Function to encrypt the password
function encryptPassword($password, $encryptionKey) {
    // Generate a 128-bit IV
    $iv = generateIV();

    // Create AES instance
    $aes = new AES('cbc');
    $aes->setKey($encryptionKey);
    $aes->setIV($iv);

    // Encrypt the password
    $encryptedPassword = $aes->encrypt($password);

    // Return the encrypted password and IV as binary data
    return array(
        'encryptedPassword' => $encryptedPassword, // Already binary
        'iv' => $iv
    );
}