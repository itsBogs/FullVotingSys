<?php
session_start();
include 'db_connect.php';

// Check if the 'id' parameter is present
if (isset($_GET['id'])) {
    $admin_id = $_GET['id'];

    // Prepare the SQL query to update the status
    $stmt = $conn->prepare("UPDATE admins SET status = 'active' WHERE admin_ID = ?");
    $stmt->bind_param("i", $admin_id);

    // Execute the query
    if ($stmt->execute()) {
        // Log the action
        $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
        $action = "Unarchived admin with ID: $admin_id";
        $logStmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
        $logStmt->bind_param("ss", $adminUsername, $action);
        $logStmt->execute();
        $logStmt->close();

        // Redirect back to the dashboard with a success message
        header("Location: super_admin_dashboard.php?message=Admin unarchived successfully.");
        exit();
    } else {
        // Redirect back with an error message if something goes wrong
        header("Location: super_admin_dashboard.php?message=Failed to unarchive admin.");
        exit();
    }

    $stmt->close();
} else {
    // If the ID is not set, redirect to the dashboard
    header("Location: super_admin_dashboard.php");
    exit();
}
?>
