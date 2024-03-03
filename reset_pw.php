<?php
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
    $password = $_POST["password"];
    $token = $_GET["token"];

    // Check if token exists in the database
    $sql = "SELECT * FROM users WHERE reset_token='$token'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Token exists, update the password
        $row = $result->fetch_assoc();
        $email = $row["email"];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $update_sql = "UPDATE users SET password='$hashed_password', reset_token=NULL WHERE email='$email'";
        if ($conn->query($update_sql) === TRUE) {
            echo "Password updated successfully.";
        } else {
            echo "Error updating password: " . $conn->error;
        }
    } else {
        echo "Invalid token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form method="post">
        <label for="password">New Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>
