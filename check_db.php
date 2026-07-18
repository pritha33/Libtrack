<?php
// check_db.php - Check what's in your database
include "db.php";

$result = $conn->query("SELECT id, email, password, role FROM users");
echo "<h2>Users in Database:</h2>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Email</th><th>Password</th><th>Role</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['password'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>