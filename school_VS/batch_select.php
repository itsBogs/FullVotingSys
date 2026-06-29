<?php
session_start();
include 'db_connect.php';

if (isset($_POST['batch'])) {
    $selected_batch = intval($_POST['batch']);

    // Attempt to update the existing row (assuming only one row with current_batchID = 1)
    $update_query = $conn->prepare("UPDATE current_batch SET batch_number = ? WHERE current_batchID = 1");
    $update_query->bind_param("i", $selected_batch);

    if ($update_query->execute()) {
        $_SESSION['success_message'] = "Batch $selected_batch selected successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update batch.";
    }

    $update_query->close();
} else {
    $_SESSION['error_message'] = "No batch selected.";
}

header("Location: super_admin_control.php");
exit;
