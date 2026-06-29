<?php
include 'db_connect.php';
session_start(); // Start session to track admin username

if (isset($_GET['student_ID'])) {
    $student_ID = $_GET['student_ID'];

    // Fetch student details before archiving
    $studentQuery = "SELECT last_name, first_name, middle_name, grade, section FROM students WHERE student_ID = ?";
    $stmt = $conn->prepare($studentQuery);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $student_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student) {
        // Archive the student by setting status to 'archived'
        $sql = "UPDATE students SET status='archived' WHERE student_ID = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $student_ID);

        if ($stmt->execute()) {
            $stmt->close();

            // Log transaction
            $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
            $action = "Archived student: {$student['last_name']}, {$student['first_name']} {$student['middle_name']} (Grade: {$student['grade']} - Section: {$student['section']})";

            $logStmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
            if (!$logStmt) {
                die("Prepare failed: " . $conn->error);
            }
            $logStmt->bind_param("ss", $adminUsername, $action);
            if (!$logStmt->execute()) {
                die("Execution failed: " . $logStmt->error);
            }
            $logStmt->close();

            $conn->close();
            header("Location: voters.php?success=archived");
            exit();
        } else {
            die("Error updating student: " . $stmt->error);
        }
    } else {
        $conn->close();
        header("Location: voters.php?error=student_not_found");
        exit();
    }
}
?>
