<?php
// reset_all.php - Complete reset of login system
$host = "localhost";
$user = "root";
$pass = "";
$db = "libtrack";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// First, clear all users
$conn->query("DELETE FROM users");

// Re-insert with plain text passwords
$sql = "INSERT INTO users (fullname, email, password, role) VALUES 
('Admin User', 'admin@gmail.com', 'admin123', 'admin'),
('John Student', 'student@gmail.com', 'student123', 'student')";

if ($conn->query($sql)) {
    echo "<h2 style='color: green;'>✓ Users reset successfully!</h2>";
} else {
    echo "<h2 style='color: red;'>✗ Error: " . $conn->error . "</h2>";
}

// Show current users
$result = $conn->query("SELECT id, fullname, email, password, role FROM users");
echo "<h3>Current Users:</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Name</th><th>Email</th><th>Password</th><th>Role</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['fullname'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td><strong>" . $row['password'] . "</strong></td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<br><br>";
echo "<a href='login.html' style='background: #1ABC9C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login →</a>";

$conn->close();
?>