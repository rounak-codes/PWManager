<?php
include_once 'db_connect.php';

// Fetch passwords from the database
$sql = "SELECT username, pwds FROM passwords";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<span>{$row['username']}</span>";
        echo "<span>{$row['pwds']}</span>";
        echo "<button>Edit</button>";
        echo "<button>Delete</button>";
        echo "</div>";
    }
} else {
    echo "No passwords found.";
}

$conn->close();