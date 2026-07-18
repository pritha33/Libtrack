<?php
// get_issued_books.php - Get all issued books for admin
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([]);
    exit();
}

$query = "SELECT ib.*, 
          u.fullname as student_name, 
          u.email,
          b.title as book_title, 
          b.author,
          b.isbn
          FROM issued_books ib
          JOIN users u ON ib.user_id = u.id
          JOIN books b ON ib.book_id = b.id
          ORDER BY ib.issue_date DESC";

$result = $conn->query($query);
$issued_books = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $issued_books[] = $row;
    }
}

echo json_encode($issued_books);
?>