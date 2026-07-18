-- Simple database without any complex stuff
DROP DATABASE IF EXISTS libtrack;
CREATE DATABASE libtrack;
USE libtrack;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin','student') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Books table
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(150) NOT NULL,
    category VARCHAR(100),
    isbn VARCHAR(50) UNIQUE,
    copies INT DEFAULT 1,
    available_copies INT DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Issued books table
CREATE TABLE issued_books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    issue_date DATE,
    due_date DATE,
    return_date DATE NULL,
    status ENUM('issued','returned') DEFAULT 'issued'
);

-- Book requests table
CREATE TABLE book_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending','approved','rejected') DEFAULT 'pending'
);

-- Fines table
CREATE TABLE fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    issued_book_id INT NOT NULL,
    amount DECIMAL(10,2) DEFAULT 0,
    status ENUM('unpaid','paid') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin
INSERT INTO users (fullname, email, password, role) VALUES 
('Admin', 'admin@gmail.com', 'admin123', 'admin');

-- Insert sample books
INSERT INTO books (title, author, category, isbn, copies, available_copies) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', 'Fiction', '9780743273565', 3, 3),
('1984', 'George Orwell', 'Dystopian', '9780452284234', 4, 4),
('The Hobbit', 'J.R.R. Tolkien', 'Fantasy', '9780547928227', 3, 3);

-- Show everything
SELECT * FROM users;
SELECT * FROM books;
SELECT * FROM issued_books;
SELECT * FROM book_requests;
SELECT * FROM fines;