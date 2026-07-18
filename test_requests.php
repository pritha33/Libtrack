<?php
// test_requests.php - Test what's in your database
include "db.php";

echo "<h2>Book Requests Test</h2>";

// Check book_requests table
$result = $conn->query("SELECT * FROM book_requests");
echo "<h3>book_requests table:</h3>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>user_id</th><th>book_id</th><th>request_date</th><th>status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['book_id'] . "</td>";
        echo "<td>" . $row['request_date'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No data in book_requests table!</p>";
}

// Check users
echo "<h3>Users:</h3>";
$result = $conn->query("SELECT id, fullname, email, role FROM users");
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

// Check books
echo "<h3>Books:</h3>";
$result = $conn->query("SELECT id, title, author FROM books");
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Title</th><th>Author</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['title'] . "</td>";
    echo "<td>" . $row['author'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>