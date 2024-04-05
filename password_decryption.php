<?php
// Include phpseclib3 autoloader
require 'vendor/autoload.php';
include_once 'db_connect.php';

use phpseclib3\Crypt\AES;

function decryptPassword($encryptedPassword, $iv) {
    // Create AES instance with the predefined encryption key
    global $encryptionKey;
    $aes = new AES('cbc');
    $aes->setKey($encryptionKey);
    $aes->setIV($iv);
    
    // Decrypt the password
    $decryptedPassword = $aes->decrypt($encryptedPassword);
    
    return $decryptedPassword;
}