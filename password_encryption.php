<?php

use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;
require_once 'vendor/autoload.php';

// Function to encrypt password using RSA public key
function encryptPassword($password)
{
    // Load public key
    $publicKey = file_get_contents("public.pem");
    if ($publicKey === false) {
        echo "Error in fetching key";
        return false;
    }
    $password = RSA::loadFormat('PKCS1',file_get_contents("public.pem"),);
    $encryptedPassword = $password ->encrypt($password);
    return $encryptedPassword;
}