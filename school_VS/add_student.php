<?php
include 'db_connect.php';

// Query to get the current batch from the current_batch table
$batchResult = $conn->query("SELECT batch_number FROM current_batch LIMIT 1");
$batchRow = $batchResult->fetch_assoc();
$currentBatch = $batchRow['batch_number'];

session_start(); // Start session to access admin username

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize input
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $grade = trim($_POST['grade']);
    $section = trim($_POST['section']);
    $lrn = trim($_POST['lrn']);

    // Construct full name for uniqueness check
    $full_name = $last_name . ', ' . $first_name . ' ' . $middle_name;

    // Check if LRN already exists for the current batch
    $checkLrn = $conn->prepare("SELECT student_ID FROM students WHERE lrn = ? AND batch = ?");

    $checkLrn->bind_param("ss", $lrn, $currentBatch);
    $checkLrn->execute();
    $lrnResult = $checkLrn->get_result();

    if ($lrnResult->num_rows > 0) {
        echo "This student already exists in the current batch.";
        $checkLrn->close();
        $conn->close();
        exit();
    }
    $checkLrn->close();

    // Insert new student with batch_number
    $stmt = $conn->prepare("INSERT INTO students (last_name, first_name, middle_name, grade, section, lrn, status, batch) VALUES (?, ?, ?, ?, ?, ?, 'active', ?)");
    $stmt->bind_param("sssssss", $last_name, $first_name, $middle_name, $grade, $section, $lrn, $currentBatch);
    
    if ($stmt->execute()) {
        // Log admin action
        $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
        $action = "Added student: $full_name (Grade: $grade - Section: $section, LRN: $lrn)";

        $log = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
        $log->bind_param("ss", $adminUsername, $action);
        $log->execute();
        $log->close();

        header("Location: voters.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
