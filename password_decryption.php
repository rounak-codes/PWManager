<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';

use phpseclib3\Crypt\AES;

// Function to decrypt the password
function decryptPassword($encryptedData, $encryptionKey) {
    // Extract IV and encrypted password from the encrypted data
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