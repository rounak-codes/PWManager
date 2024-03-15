<?php
$file = 'master_key.txt';

if (file_exists($file)) {
    if (unlink($file)) {
        http_response_code(200); // Success
    } else {
        http_response_code(500); // Server error
    }
} else {
    http_response_code(404); // File not found
}