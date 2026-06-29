<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and trim inputs
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $middle_name = isset($_POST['middle_name']) ? trim($_POST['middle_name']) : '';
    $lrn = trim($_POST['lrn']);

    // Ensure required fields are not empty
    if (empty($last_name) || empty($first_name) || empty($lrn)) {
        echo "<script>alert('Please fill in all required fields.'); window.location.href='index.php';</script>";
        exit();
    }

    // Prepare query - account for optional middle name (can be NULL in DB)
    $stmt = $conn->prepare("
        SELECT student_ID, last_name, first_name, middle_name, lrn
        FROM students
        WHERE last_name = ? AND first_name = ? AND IFNULL(middle_name, '') = ? AND lrn = ?
    ");
    $stmt->bind_param("ssss", $last_name, $first_name, $middle_name, $lrn);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check for valid match
    if ($result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $_SESSION['student_id'] = $student['student_ID'];
        $_SESSION['student_name'] = $student['last_name'] . ', ' . $student['first_name'] . ($student['middle_name'] ? ' ' . $student['middle_name'] : '');
        
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid login details. Please try again.'); window.location.href='index.php';</script>";
        exit();
    }
}
?>
