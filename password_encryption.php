<?php

use phpseclib3\Crypt\RSA;
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
    $password = RSA::loadPublicKeyFormat(file_get_contents("public.pem"),$password=false);
    $encryptedPassword = $password ->encrypt($password);
    return $encryptedPassword;
}