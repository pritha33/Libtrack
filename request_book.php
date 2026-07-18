<?php
// request_book.php - Student requests a book (needs admin approval)
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Please login first"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = intval($_POST['book_id'] ?? 0);

if (!$book_id) {
    echo json_encode(["status" => "error", "message" => "Invalid book"]);
    exit();
}

// Check if book exists
$book_check = $conn->prepare("SELECT id, available_copies, title FROM books WHERE id = ?");
$book_check->bind_param("i", $book_id);
$book_check->execute();
$book = $book_check->get_result()->fetch_assoc();

if (!$book) {
    echo json_encode(["status" => "error", "message" => "Book not found"]);
    exit();
}

// Check if user already has a pending request for this book
$req_check = $conn->prepare("SELECT id FROM book_requests WHERE user_id = ? AND book_id = ? AND status = 'pending'");
$req_check->bind_param("ii", $user_id, $book_id);
$req_check->execute();
if ($req_check->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "You already have a pending request for this book"]);
    exit();
}

// Check if user already has this book issued
$issue_check = $conn->prepare("SELECT id FROM issued_books WHERE user_id = ? AND book_id = ? AND status = 'issued'");
$issue_check->bind_param("ii", $user_id, $book_id);
$issue_check->execute();
if ($issue_check->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "You already have this book"]);
    exit();
}

// Create book request
$stmt = $conn->prepare("INSERT INTO book_requests (user_id, book_id, request_date, status) VALUES (?, ?, CURDATE(), 'pending')");
$stmt->bind_param("ii", $user_id, $book_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Book request sent to admin for approval"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to send request"]);
}
?>