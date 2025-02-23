<?php
session_start();
include_once 'db_connect.php';
include_once 'password_decryption.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch encryption key
$stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$encryptionKey = ($result->num_rows === 1) ? $result->fetch_assoc()['enc_key'] : die("Encryption key not found.");
$stmt->close();

// Fetch passwords
$stmt = $conn->prepare("SELECT site_name, username, pwds, iv FROM passwords WHERE user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$passwords = [];

while ($row = $result->fetch_assoc()) {
    $passwords[] = [
        'site_name' => $row['site_name'],
        'username' => $row['username'],
        'password' => decryptPassword($row['pwds'], $encryptionKey, $row['iv'])
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
    <link rel="stylesheet" href="style.css">
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
                    <tr><td colspan="3">No passwords found.</td></tr>
                <?php else: ?>
                    <?php foreach ($passwords as $password): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($password['site_name']); ?></td>
                            <td><?php echo htmlspecialchars($password['username']); ?></td>
                            <td>
                                <div class="password-field">
                                    <span class="password-text">••••••••</span>
                                    <button type="button" class="show-password" data-password="<?php echo htmlspecialchars($password['password']); ?>">Show</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="button-group2">
            <button onclick="window.location.href='dashboard.html'">Dashboard</button>
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.show-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordSpan = this.previousElementSibling;
                const password = this.getAttribute('data-password');
                
                if (passwordSpan.textContent === '••••••••') {
                    passwordSpan.textContent = password;
                    this.textContent = 'Hide';
                } else {
                    passwordSpan.textContent = '••••••••';
                    this.textContent = 'Show';
                }
            });
        });
    });
    </script>
</body>
</html>