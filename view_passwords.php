<?php
session_start();
include_once 'db_connect.php';
include_once 'password_decryption.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Unauthorized access.");
}

$username = $_SESSION['username'];

// Fetch encryption key
$stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $encryptionKey = $row['enc_key'];
} else {
    die("Encryption key not found.");
}

$stmt->close();

// Fetch passwords for the logged-in user
$stmt = $conn->prepare("SELECT site_name, username, pwds, iv FROM passwords WHERE user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$passwords = [];
while ($row = $result->fetch_assoc()) {
    // Decrypt the password
    $decryptedPassword = decryptPassword($row['pwds'], $encryptionKey, $row['iv']);
    $passwords[] = [
        'site_name' => $row['site_name'],
        'username' => $row['username'],
        'password' => $decryptedPassword
    ];
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Passwords</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to style.css -->
</head>
<body>
    <div class="container">
        <h1>View Passwords</h1>
        <table class="passwords-table">
            <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Username</th>
                    <th>Password</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($passwords)): ?>
                    <tr>
                        <td colspan="3">No passwords found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($passwords as $password): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($password['site_name']); ?></td>
                            <td><?php echo htmlspecialchars($password['username']); ?></td>
                            <td><?php echo htmlspecialchars($password['password']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="button-group2">
            <button onclick="window.location.href='dashboard.html'">Go to Dashboard</button>
            <button id="logout" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
</body>
</html>
