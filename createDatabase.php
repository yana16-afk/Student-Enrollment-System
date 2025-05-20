<?php
require 'db.php';

function setupDatabase() {
    $pdo = getConnection();

    // Drop tables if they exist (optional)
    $pdo->exec("DROP TABLE IF EXISTS student_courses");
    $pdo->exec("DROP TABLE IF EXISTS students");
    $pdo->exec("DROP TABLE IF EXISTS courses");

    // Create students table
    $pdo->exec("
        CREATE TABLE students (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL
        )
    ");

    // Create courses table
    $pdo->exec("
        CREATE TABLE courses (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            course_name VARCHAR(255) NOT NULL
        )
    ");

    // Create student_courses table (enrollment)
    $pdo->exec("
        CREATE TABLE student_courses (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            course_id INT NOT NULL,
            FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE
        )
    ");

    echo "Database and tables created successfully.";
}

setupDatabase();
