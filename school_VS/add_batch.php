<?php
session_start();
include 'db_connect.php';

if (isset($_POST['new_batch'])) {
    $new_batch = intval($_POST['new_batch']);

    // Check if the batch already exists in batch_history
    $check_query = $conn->prepare("SELECT COUNT(*) AS count FROM batch_history WHERE batch = ?");
    $check_query->bind_param("i", $new_batch);
    $check_query->execute();
    $check_result = $check_query->get_result();
    $check_row = $check_result->fetch_assoc();

    if ($check_row['count'] > 0) {
        $_SESSION['error_message'] = "Batch $new_batch already exists.";
    } else {
        // Insert the new batch into batch_history
        $insert_query = $conn->prepare("INSERT INTO batch_history (batch, election_date) VALUES (?, NOW())");
        $insert_query->bind_param("i", $new_batch);

        if ($insert_query->execute()) {
            $_SESSION['success_message'] = "Batch $new_batch added successfully.";
        } else {
            $_SESSION['error_message'] = "Failed to add new batch.";
        }

        $insert_query->close();
    }

    $check_query->close();
}

header("Location: super_admin_control.php");
exit;
