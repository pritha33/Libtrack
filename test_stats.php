<?php
// test_stats.php - Test database connection and counts
session_start();
include "db.php";

echo "<h2>Database Statistics Test</h2>";

// Test connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

// Count books
$result = $conn->query("SELECT COUNT(*) as count FROM books");
if ($result) {
    $books = $result->fetch_assoc()['count'];
    echo "<p>📚 Total Books: <strong>$books</strong></p>";
} else {
    echo "<p style='color: red;'>Error counting books: " . $conn->error . "</p>";
}

// Count users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'");
if ($result) {
    $users = $result->fetch_assoc()['count'];
    echo "<p>👥 Total Students: <strong>$users</strong></p>";
} else {
    echo "<p style='color: red;'>Error counting users: " . $conn->error . "</p>";
}

// Count issued books
$result = $conn->query("SELECT COUNT(*) as count FROM issued_books WHERE status = 'issued'");
if ($result) {
    $issued = $result->fetch_assoc()['count'];
    echo "<p>📖 Issued Books: <strong>$issued</strong></p>";
} else {
    echo "<p style='color: red;'>Error counting issued books: " . $conn->error . "</p>";
}

// Show all tables
echo "<h3>All Tables in Database:</h3>";
$result = $conn->query("SHOW TABLES");
echo "<ul>";
while ($row = $result->fetch_array()) {
    echo "<li>" . $row[0] . "</li>";
}
echo "</ul>";

// Show sample data
echo "<h3>Sample Users:</h3>";
$result = $conn->query("SELECT id, fullname, email, role FROM users LIMIT 5");
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['fullname'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Sample Books:</h3>";
$result = $conn->query("SELECT id, title, author, copies, available_copies FROM books LIMIT 5");
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Copies</th><th>Available</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['title'] . "</td>";
    echo "<td>" . $row['author'] . "</td>";
    echo "<td>" . $row['copies'] . "</td>";
    echo "<td>" . $row['available_copies'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>