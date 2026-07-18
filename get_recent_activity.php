<?php
// get_recent_activity.php - Fetch real activity from database
session_start();
header("Content-Type: application/json");
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode([]);
    exit();
}

// Get recent activities from various tables
$activities = [];

// Get recent book additions
$books = $conn->query("SELECT title, added_at FROM books ORDER BY added_at DESC LIMIT 3");
while ($book = $books->fetch_assoc()) {
    $activities[] = [
        "icon" => "bi-book",
        "message" => "New book added: \"" . $book['title'] . "\"",
        "time" => timeAgo(strtotime($book['added_at']))
    ];
}

// Get recent user registrations
$users = $conn->query("SELECT fullname, email, created_at FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 3");
while ($user = $users->fetch_assoc()) {
    $activities[] = [
        "icon" => "bi-person-plus",
        "message" => "New student registered: " . $user['email'],
        "time" => timeAgo(strtotime($user['created_at']))
    ];
}

// Get recent book issues
$issues = $conn->query("
    SELECT ib.issue_date, u.fullname, b.title 
    FROM issued_books ib 
    JOIN users u ON ib.user_id = u.id 
    JOIN books b ON ib.book_id = b.id 
    ORDER BY ib.issue_date DESC LIMIT 3
");
while ($issue = $issues->fetch_assoc()) {
    $activities[] = [
        "icon" => "bi-journal-check",
        "message" => "Book issued: \"" . $issue['title'] . "\" to " . $issue['fullname'],
        "time" => timeAgo(strtotime($issue['issue_date']))
    ];
}

// Get recent book returns
$returns = $conn->query("
    SELECT ib.return_date, u.fullname, b.title 
    FROM issued_books ib 
    JOIN users u ON ib.user_id = u.id 
    JOIN books b ON ib.book_id = b.id 
    WHERE ib.return_date IS NOT NULL 
    ORDER BY ib.return_date DESC LIMIT 3
");
while ($return = $returns->fetch_assoc()) {
    $activities[] = [
        "icon" => "bi-arrow-return-left",
        "message" => "Book returned: \"" . $return['title'] . "\" by " . $return['fullname'],
        "time" => timeAgo(strtotime($return['return_date']))
    ];
}

// Sort by time (most recent first)
usort($activities, function($a, $b) {
    return strtotime($b['time']) - strtotime($a['time']);
});

// Take only first 10 activities
$activities = array_slice($activities, 0, 10);

echo json_encode($activities);

// Helper function to convert timestamp to "time ago" format
function timeAgo($timestamp) {
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . " minute" . ($mins > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . " day" . ($days > 1 ? "s" : "") . " ago";
    } else {
        return date("M j, Y", $timestamp);
    }
}
?>