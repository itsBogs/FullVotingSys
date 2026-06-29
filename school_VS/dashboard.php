<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in (if session is not set, redirect to login page)
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details from the database
$studentQuery = $conn->prepare("SELECT last_name, first_name, middle_name FROM students WHERE student_ID = ?");
$studentQuery->bind_param("i", $student_id);
$studentQuery->execute();
$studentResult = $studentQuery->get_result()->fetch_assoc();

// Check if the student exists
if ($studentResult) {
    // Sanitize input and prepare data for display
    $last_name = htmlspecialchars($studentResult['last_name']);
    $first_name = htmlspecialchars($studentResult['first_name']);
    $middle_name = htmlspecialchars($studentResult['middle_name']);
    $middle_initial = $middle_name ? strtoupper(substr($middle_name, 0, 1)) . '.' : ''; // Handle middle name initialization

    $student_name = "$last_name, $first_name $middle_initial";  // Full name dynamically constructed
} else {
    // Redirect if no student found (optional, just to ensure data integrity)
    echo "<script>alert('Student not found. Please log in again.'); window.location.href='login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSG Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Welcome to SSG Voting System</h1>
        <p>Hello, <?= htmlspecialchars($student_name) ?></p>

        <!-- Voting or other content here -->
        <p>Feel free to cast your vote or check your status below.</p>
    </div>
</body>
</html>
