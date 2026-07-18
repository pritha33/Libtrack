<?php
// get_books.php
include "db.php";
$result = $conn->query("SELECT * FROM books ORDER BY id DESC");
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
echo json_encode($books);
?>