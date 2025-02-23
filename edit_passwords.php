<?php
session_start();
include_once 'db_connect.php';
include_once 'password_encryption.php';
include_once 'password_decryption.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    
    // Fetch encryption key
    $stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2 LIMIT 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $encryptionKey = ($result->num_rows === 1) ? $result->fetch_assoc()['enc_key'] : die("Encryption key not found.");
    $stmt->close();

    if ($action === 'edit') {
        $newPassword = $_POST['password'];
        $encryptedData = encryptPassword($newPassword, $encryptionKey);
        
        $stmt = $conn->prepare("UPDATE passwords SET pwds = ?, iv = ? WHERE id = ? AND user = ?");
        $stmt->bind_param("ssss", $encryptedData['encryptedPassword'], $encryptedData['iv'], $id, $username);
        
        if ($stmt->execute()) {
            header("Location: edit_passwords.php?status=updated");
        } else {
            header("Location: edit_passwords.php?status=error");
        }
        $stmt->close();
    } 
    elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM passwords WHERE id = ? AND user = ?");
        $stmt->bind_param("ss", $id, $username);
        
        if ($stmt->execute()) {
            header("Location: edit_passwords.php?status=deleted");
        } else {
            header("Location: edit_passwords.php?status=error");
        }
        $stmt->close();
    }
    exit();
}

// Fetch passwords
$stmt = $conn->prepare("SELECT enc_key FROM enkeys WHERE id = 2 LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();
$encryptionKey = ($result->num_rows === 1) ? $result->fetch_assoc()['enc_key'] : die("Encryption key not found.");
$stmt->close();

$stmt = $conn->prepare("SELECT id, site_name, username, pwds, iv FROM passwords WHERE user = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$passwords = [];

while ($row = $result->fetch_assoc()) {
    $passwords[] = [
        'id' => $row['id'],
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
    <title>Edit Passwords</title>
    <link rel="stylesheet" href="style.css">
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
                    <tr><td colspan="4">No passwords found.</td></tr>
                <?php else: ?>
                    <?php foreach ($passwords as $password): ?>
                        <tr>
                            <form action="edit_passwords.php" method="post">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($password['id']); ?>">
                                <td><?php echo htmlspecialchars($password['site_name']); ?></td>
                                <td><?php echo htmlspecialchars($password['username']); ?></td>
                                <td>
                                    <div class="password-field">
                                        <input type="password" name="password" value="<?php echo htmlspecialchars($password['password']); ?>" required>
                                        <button type="button" class="show-password">Show</button>
                                    </div>
                                </td>
                                <td>
                                    <button type="submit" name="action" value="edit" class="edit-btn">Save</button>
                                    <button type="submit" name="action" value="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this password?')">Delete</button>
                                </td>
                            </form>
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
        // Handle status messages
        <?php if (isset($_GET['status'])): ?>
            const status = "<?php echo htmlspecialchars($_GET['status']); ?>";
            if (status === 'updated') {
                alert('Password has been updated successfully.');
            } else if (status === 'deleted') {
                alert('Password has been deleted successfully.');
            } else if (status === 'error') {
                alert('An error occurred. Please try again.');
            }
        <?php endif; ?>

        // Show/hide password functionality
        document.querySelectorAll('.show-password').forEach(button => {
            button.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.textContent = 'Hide';
                } else {
                    passwordInput.type = 'password';
                    this.textContent = 'Show';
                }
            });
        });
    });
    </script>
</body>
</html>