<?php
include 'db_connect.php';
session_start(); // Ensure session is started for tracking admin

if (isset($_GET['student_ID'])) {
    $student_ID = $_GET['student_ID'];

    $query = "SELECT last_name, first_name, middle_name, grade, section FROM students WHERE student_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_ID);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student) {
        // Restore student by setting status to 'active'
        $stmt = $conn->prepare("UPDATE students SET status='active' WHERE student_ID = ?");
    $stmt->bind_param("i", $student_ID);

        if ($stmt->execute()) {
            // Format full name as "Last Name, First Name Middle Name"
            $formattedName = "{$student['last_name']}, {$student['first_name']} {$student['middle_name']}";
            
            // Log transaction
            $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
            $action = "Restored student: $formattedName (Grade: {$student['grade']} - Section: {$student['section']})";

            $logStmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
            $logStmt->bind_param("ss", $adminUsername, $action);
            $logStmt->execute();
            $logStmt->close();

            header("Location: voters.php?success=restored");
            exit();
        } else {
            header("Location: voters.php?error=restore_failed");
            exit();
        }
    } else {
        header("Location: voters.php?error=student_not_found");
        exit();
    }
}

$conn->close();
?>
