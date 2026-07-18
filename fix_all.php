<?php
// fix_all.php - Complete fix script
$host = "localhost";
$user = "root";
$pass = "";
$db = "libtrack";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>🔧 LibTrack Fix Tool</h2>";

// 1. Add reset columns if missing
echo "<h3>1. Adding reset columns...</h3>";
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(255) NULL");
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_expires DATETIME NULL");
$conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS last_password_reset DATETIME NULL");
echo "<p style='color: green;'>✓ Reset columns added</p>";

// 2. Update passwords to plain text
echo "<h3>2. Updating passwords...</h3>";
$conn->query("UPDATE users SET password = 'admin123' WHERE email = 'admin@gmail.com'");
$conn->query("UPDATE users SET password = 'student123' WHERE email = 'student@gmail.com'");

// Add pritha if exists, or update
$conn->query("INSERT INTO users (fullname, email, password, role) VALUES ('Pritha User', 'pritha@gmail.com', 'pritha123', 'student') ON DUPLICATE KEY UPDATE password = 'pritha123'");

echo "<p style='color: green;'>✓ Passwords updated to plain text</p>";

// 3. Show all users
echo "<h3>3. Current Users:</h3>";
$result = $conn->query("SELECT id, fullname, email, password, role FROM users");
echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
echo "<tr style='background: #2C3E50; color: white;'><th>ID</th><th>Name</th><th>Email</th><th>Password</th><th>Role</th></tr>";
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

echo "<br><hr>";
echo "<h3>✅ Login Credentials:</h3>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@gmail.com / admin123</li>";
echo "<li><strong>Student:</strong> student@gmail.com / student123</li>";
echo "<li><strong>Pritha:</strong> pritha@gmail.com / pritha123</li>";
echo "</ul>";

echo "<br><a href='login.html' style='background: #1ABC9C; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login →</a>";

$conn->close();
?>