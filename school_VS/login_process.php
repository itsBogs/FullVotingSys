<?php
session_start();
include 'db_connect.php';

// Capture and sanitize form input
$lrn = trim($_POST['lrn']);
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$middle_name = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';

// Basic validation
if (empty($lrn) || empty($first_name) || empty($last_name)) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='login.php';</script>";
    exit();
}

// Prepare SQL to check if the student exists
$query = $conn->prepare("
    SELECT student_ID 
    FROM students 
    WHERE lrn = ? 
      AND first_name = ? 
      AND last_name = ? 
      AND IFNULL(middle_name, '') = ?
");
$query->bind_param("ssss", $lrn, $first_name, $last_name, $middle_name);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    // Student found, start session
    $student = $result->fetch_assoc();
    $_SESSION['student_id'] = $student['student_ID'];

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // Student not found
    echo "<script>alert('Invalid login details.'); window.location.href='login.php';</script>";
    exit();
}

$conn->close();
?>
