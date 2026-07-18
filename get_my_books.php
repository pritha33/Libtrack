<?php
// get_my_books.php
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT ib.*, b.title, b.author, b.isbn 
          FROM issued_books ib 
          JOIN books b ON ib.book_id = b.id 
          WHERE ib.user_id = ? AND ib.status = 'issued'
          ORDER BY ib.issue_date DESC";

$stmt = $conn->prepare($query);<?php
// get_my_books.php - Get only approved and issued books for student
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT ib.*, b.title, b.author, b.isbn 
          FROM issued_books ib 
          JOIN books b ON ib.book_id = b.id 
          WHERE ib.user_id = ? AND ib.status = 'issued'
          ORDER BY ib.issue_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

echo json_encode($books);
?>
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
echo json_encode($books);
?>