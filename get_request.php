<?php
// get_requests.php - Get all pending book requests
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([]);
    exit();
}

// Simple query without JOIN first to test
$query = "SELECT * FROM book_requests ORDER BY request_date DESC";
$result = $conn->query($query);

$requests = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Get user name separately
        $user_query = "SELECT fullname, email FROM users WHERE id = " . $row['user_id'];
        $user_result = $conn->query($user_query);
        $user = $user_result->fetch_assoc();
        
        // Get book title separately
        $book_query = "SELECT title, author FROM books WHERE id = " . $row['book_id'];
        $book_result = $conn->query($book_query);
        $book = $book_result->fetch_assoc();
        
        $requests[] = [
            'id' => $row['id'],
            'user_name' => $user['fullname'] ?? 'Unknown',
            'email' => $user['email'] ?? '',
            'book_title' => $book['title'] ?? 'Unknown',
            'author' => $book['author'] ?? '',
            'request_date' => $row['request_date'],
            'status' => $row['status']
        ];
    }
}

echo json_encode($requests);
?>