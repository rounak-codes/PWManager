<?php

// Generate a new private (and public) key pair
$privateKey = openssl_pkey_new(array(
    'private_key_bits' => 2048, // Key size
    'private_key_type' => OPENSSL_KEYTYPE_RSA, // Key type
));

// Get the private key
openssl_pkey_export($privateKey, $privateKeyString);

// Get the public key
$publicKeyDetails = openssl_pkey_get_details($privateKey);
$publicKeyString = $publicKeyDetails['key'];

// Output the keys
echo "Private Key:\n";
echo $privateKeyString . "\n\n";

echo "Public Key:\n";
echo $publicKeyString . "\n";