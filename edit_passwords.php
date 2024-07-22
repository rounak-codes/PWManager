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
$stmt = $conn->prepare("SELECT id, site_name, username, pwds, iv FROM passwords WHERE user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$passwords = [];
while ($row = $result->fetch_assoc()) {
    // Decrypt the password
    $decryptedPassword = decryptPassword($row['pwds'], $encryptionKey, $row['iv']);
    $passwords[] = [
        'id' => $row['id'],
        'site_name' => $row['site_name'],
        'username' => $row['username'],  // Added username
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
    <title>Edit Passwords</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to style.css -->
</head>
<body>
    <div class="container">
        <h1>Edit Passwords</h1>
        <table class="passwords-table">
            <thead>
                <tr>
                    <th>Site Name</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($passwords)): ?>
                    <tr>
                        <td colspan="4">No passwords found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($passwords as $password): ?>
                        <tr>
                            <form action="edit_passwords.php" method="post">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($password['id']); ?>">
                                <td><?php echo htmlspecialchars($password['site_name']); ?></td>
                                <td><?php echo htmlspecialchars($password['username']); ?></td>
                                <td>
                                    <input type="password" name="password" value="<?php echo htmlspecialchars($password['password']); ?>" required>
                                </td>
                                <td>
                                    <button type="submit" name="action" value="edit">Edit</button>
                                    <button type="submit" name="action" value="delete">Delete</button>
                                </td>
                            </form>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_GET['status'])): ?>
                let status = "<?php echo htmlspecialchars($_GET['status']); ?>";
                if (status === 'updated') {
                    alert('Password has been updated.');
                } else if (status === 'deleted') {
                    alert('Password has been deleted.');
                }
            <?php endif; ?>
        });
    </script>
</body>
</html>
