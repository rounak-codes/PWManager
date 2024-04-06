<?php
// Function to generate a random initialization vector (IV)
function generateIV() {
    return random_bytes(16); // 128-bit IV for AES
}

// Function to generate a random encryption key
function generateEncryptionKey() {
    return random_bytes(32); // 256-bit key for AES-256
}

// Function to encrypt the password using OpenSSL
function encryptPassword($password, $encryptionKey) {
    // Generate a 128-bit IV
    $iv = generateIV();

    // Choose the encryption method and mode
    $cipherMethod = 'aes-256-cbc';

    // Perform encryption using OpenSSL
    $encryptedPassword = openssl_encrypt($password, $cipherMethod, $encryptionKey, OPENSSL_RAW_DATA, $iv);

    // Return the encrypted password and IV as binary data
    return array(
        'encryptedPassword' => $encryptedPassword, // Already binary
        'iv' => $iv
    );
}