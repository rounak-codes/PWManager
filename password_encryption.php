<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';
include_once 'db_connect.php';

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
    $iv = generateIV();
    $encryptionKey = generateEncryptionKey();
    $aes = new AES('cbc');
    $aes->setKey($encryptionKey);
    $aes->setIV($iv);
    $encryptedPassword = $aes->encrypt($password);
    $encryptedData = base64_encode($iv . $encryptedPassword);
    return array(
        'encryptedPassword' => $encryptedData,
        'iv' => $iv
    );
}