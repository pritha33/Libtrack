<?php
// setup_users.php - Run this file once to create users
include "db.php";

// Delete existing users first
$conn->query("DELETE FROM users");

// Create admin user (password: admin123)
$admin_password = password_hash("admin123", PASSWORD_DEFAULT);
$admin_stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
$admin_stmt->bind_param("ssss", $admin_name, $admin_email, $admin_password, $admin_role);
$admin_name = "Admin User";
$admin_email = "admin@gmail.com";
$admin_role = "admin";
$admin_stmt->execute();

// Create student user (password: student123)
$student_password = password_hash("student123", PASSWORD_DEFAULT);
$student_stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
$student_stmt->bind_param("ssss", $student_name, $student_email, $student_password, $student_role);
$student_name = "John Student";
$student_email = "student@gmail.com";
$student_role = "student";
$student_stmt->execute();

echo "Users created successfully!<br>";
echo "Admin Email: admin@gmail.com<br>";
echo "Admin Password: admin123<br>";
echo "Student Email: student@gmail.com<br>";
echo "Student Password: student123<br>";
?>