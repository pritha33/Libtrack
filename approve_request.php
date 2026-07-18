<?php
// approve_request.php - Admin approves request and issues book
session_start();
header("Content-Type: application/json");
include "db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$request_id = $_POST['request_id'] ?? 0;

if (!$request_id) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

// Get request details
$req_stmt = $conn->prepare("SELECT * FROM book_requests WHERE id = ? AND status = 'pending'");
$req_stmt->bind_param("i", $request_id);
$req_stmt->execute();
$request = $req_stmt->get_result()->fetch_assoc();

if (!$request) {
    echo json_encode(["status" => "error", "message" => "Request not found or already processed"]);
    exit();
}

// Check book availability
$book_check = $conn->prepare("SELECT id, available_copies, title FROM books WHERE id = ?");
$book_check->bind_param("i", $request['book_id']);
$book_check->execute();
$book = $book_check->get_result()->fetch_assoc();

if (!$book) {
    echo json_encode(["status" => "error", "message" => "Book not found"]);
    exit();
}

if ($book['available_copies'] <= 0) {
    echo json_encode(["status" => "error", "message" => "Book not available"]);
    exit();
}

// Calculate due date (14 days from today)
$due_date = date('Y-m-d', strtotime('+14 days'));

// Issue the book
$issue_stmt = $conn->prepare("INSERT INTO issued_books (user_id, book_id, issue_date, due_date, status) VALUES (?, ?, CURDATE(), ?, 'issued')");
$issue_stmt->bind_param("iis", $request['user_id'], $request['book_id'], $due_date);

if ($issue_stmt->execute()) {
    // Update request status
    $update_req = $conn->prepare("UPDATE book_requests SET status = 'approved' WHERE id = ?");
    $update_req->bind_param("i", $request_id);
    $update_req->execute();
    
    // Update book available copies
    $update_book = $conn->prepare("UPDATE books SET available_copies = available_copies - 1 WHERE id = ?");
    $update_book->bind_param("i", $request['book_id']);
    $update_book->execute();
    
    echo json_encode(["status" => "success", "message" => "Book issued successfully to student"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to issue book"]);
}
?>