<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_ID = $_POST['student_ID'];
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $grade = trim($_POST['grade']);
    $section = trim($_POST['section']);

    // Construct full name for logging
    $new_full_name = $last_name . ', ' . $first_name . ' ' . $middle_name;

    // Fetch old data before updating
    $oldDataQuery = "SELECT last_name, first_name, middle_name, grade, section FROM students WHERE student_ID = '$student_ID'";
    $oldDataResult = $conn->query($oldDataQuery);

    if ($oldDataResult && $oldDataResult->num_rows > 0) {
        $oldData = $oldDataResult->fetch_assoc();
        $old_full_name = $oldData['last_name'] . ', ' . $oldData['first_name'] . ' ' . $oldData['middle_name'];

        // Update the student in the database (without touching LRN)
        $updateQuery = "UPDATE students 
                        SET last_name='$last_name', first_name='$first_name', middle_name='$middle_name', grade='$grade', section='$section' 
                        WHERE student_ID='$student_ID'";

        if ($conn->query($updateQuery) === TRUE) {
            // Log transaction
            $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
            $action = "Edited student: $old_full_name → $new_full_name";

            $stmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
            $stmt->bind_param("ss", $adminUsername, $action);
            $stmt->execute();
            $stmt->close();

            // Show alert and redirect
            echo "<script>
                    alert('Student updated successfully!');
                    window.location.href = 'voters.php';
                  </script>";
            exit();
        } else {
            echo "<script>alert('Error updating student.'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Student record not found.'); window.history.back();</script>";
        exit();
    }

    $conn->close();
}
?>
