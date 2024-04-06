<?php
// Function to decrypt the password
function decryptPassword($encryptedData, $encryptionKey, $iv) {
    // Decrypt the password using OpenSSL
    $decryptedPassword = openssl_decrypt($encryptedData, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv);

    // Return the decrypted password
    return $decryptedPassword;
}