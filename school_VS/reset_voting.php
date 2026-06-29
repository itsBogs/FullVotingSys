<?php
session_start();
include 'db_connect.php';

// Fetch the voting status
$status_query = $conn->query("SELECT status FROM voting_status WHERE id = 1");
$status_row = $status_query->fetch_assoc();
$current_status = $status_row['status'];

// Check if all students have voted
$voters_query = $conn->query("SELECT COUNT(*) AS total_voters FROM students WHERE status = 'active' AND voted = 0");
$voters_row = $voters_query->fetch_assoc();
$unvoted_voters = $voters_row['total_voters'];

// Prevent reset if not all voters have voted
if ($unvoted_voters > 0) {
    $_SESSION['error_message'] = "Cannot reset the system. Some students have not voted yet.";
    header("Location: super_admin_control.php");
    exit();
}

$conn->begin_transaction();

try {
    // Fetch current batch
    $batch_query = $conn->query("SELECT batch_number FROM batch WHERE id = 1");
    $batch_row = $batch_query->fetch_assoc();
    $new_batch_number = $batch_row['batch_number'] + 1;

    // Update batch number
    $conn->query("UPDATE batch SET batch_number = $new_batch_number WHERE id = 1");

    // Reset students: reset vote status
    $conn->query("UPDATE students SET voted = 0, status = 'active'");

    // Optional: Clear candidates and votes
    $conn->query("DELETE FROM candidates");
    $conn->query("DELETE FROM votes");

    // Set voting status to closed
    $conn->query("UPDATE voting_status SET status = 'closed' WHERE id = 1");

    $conn->commit();
    $_SESSION['success_message'] = "Voting system has been reset for batch $new_batch_number.";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error_message'] = "Reset failed: " . $e->getMessage();
}

header("Location: super_admin_control.php");
exit();
?>
