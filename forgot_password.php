<?php
// Include PHPMailer library
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

// Function to generate a random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Database connection
// Replace with your database credentials
$servername = "localhost";
$username = "rounak";
$password = "rounakbag24";
$dbname = "RounakDB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];

    // Generate a unique token
    $token = generateToken();

    // Update the database with the token
    $sql = "UPDATE users SET reset_token='$token' WHERE email='$email'";
    if ($conn->query($sql) === TRUE) {
        // Send email with reset link
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@example.com'; // Your email
        $mail->Password = 'your_password'; // Your email password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your_email@example.com', 'Your Name');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = 'Click the following link to reset your password: <a href="http://localhost/reset_pw.php?token=' . $token . '">Reset Password</a>';

        if ($mail->send()) {
            echo "Password reset link has been sent to your email.";
        } else {
            echo "Failed to send email. Please try again later.";
        }
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Close database connection
$conn->close();