<?php
session_start();
include 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check for empty inputs
    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill in both username and password.'); window.location.href='index.php';</script>";
        exit();
    }

    // Prepare and execute the query
    $query = $conn->prepare("SELECT student_ID, last_name, first_name, middle_name, password FROM students WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        // If using hashed passwords, replace this with password_verify
        if ($password === $student['password']) {
            $_SESSION['student_id'] = $student['student_ID'];  // <- updated here

            // Format: Last Name, First Name Middle Name
            $middle = trim($student['middle_name']);
            $_SESSION['student_name'] = $student['last_name'] . ', ' . $student['first_name'] . ($middle ? ' ' . $middle : '');

            header("Location: voters_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Username not found.'); window.location.href='index.php';</script>";
        exit();
    }
} else {
    // Redirect if accessed directly
    header("Location: index.php");
    exit();
}
?>
