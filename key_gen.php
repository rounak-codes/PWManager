<?php

use phpseclib3\Crypt\RSA;

// Include Composer's autoloader
require 'vendor/autoload.php';

// Initialize RSA object
RSA::createKey(2048);

// Public key
$publicKey = $keyPair['publickey'];

// Private key
$privateKey = $keyPair['privatekey'];

// File paths
$publicKeyFile = 'public.pem';
$privateKeyFile = 'private.pem';

// Save public key to file
file_put_contents($publicKeyFile, $publicKey);

// Save private key to file
file_put_contents($privateKeyFile, $privateKey);

echo "RSA key pair generated and saved to files: $publicKeyFile, $privateKeyFile";