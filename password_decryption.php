<?php

use phpseclib3\Crypt\RSA;
require_once 'vendor/autoload.php';

// Function to decrypt password using RSA private key
function decryptPassword($password)
{
    // Load public key
    $privatekey = file_get_contents("private.pem");
    if ($privatekey === false) {
        echo "Error in fetching key";
        return false;
    }
    $password = RSA::loadPrivateKeyFormat(file_get_contents("private.pem"),$password=false);
    $decryptedPassword = $password ->decrypt($password);
    return $decryptedPassword;
}