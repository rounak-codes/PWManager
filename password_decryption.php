<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';
include_once 'db_connect.php';

use phpseclib3\Crypt\AES;

// Function to decrypt the password
function decryptPassword($encryptedData, $encryptionKey) {
    // Decode the base64 encoded encrypted data
    $encryptedData = base64_decode($encryptedData);
    
    // Extract IV and encrypted password from the decrypted data
    $iv = substr($encryptedData, 0, 16); // IV length is 16 bytes
    $encryptedPassword = substr($encryptedData, 16);
    
    // Initialize AES with the same key and IV
    $aes = new AES('cbc');
    $aes->setKey($encryptionKey);
    $aes->setIV($iv);
    
    // Decrypt the password
    $password = $aes->decrypt($encryptedPassword);
    
    return $password;
}