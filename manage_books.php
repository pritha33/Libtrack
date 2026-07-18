<?php
// manage_books.php
include "db.php";

$action = $_GET['action'] ?? '';

if ($action === 'list') {
    $result = $conn->query("SELECT * FROM books ORDER BY id DESC");
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($books);
    exit;
}

if ($action === 'add') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $copies = (int)$_POST['copies'];
    
    $stmt = $conn->prepare("INSERT INTO books (title, author, category, isbn, copies, available_copies) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssii", $title, $author, $category, $isbn, $copies, $copies);
    $stmt->execute();
    echo "success";
    exit;
}

if ($action === 'delete') {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "deleted";
    exit;
}
?>