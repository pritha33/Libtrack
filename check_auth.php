<?php
// check_auth.php
session_start();
header("Content-Type: application/json");

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "ok",
        "name" => $_SESSION['fullname'],
        "role" => $_SESSION['role']
    ]);
} else {
    echo json_encode(["status" => "no"]);
}
?>