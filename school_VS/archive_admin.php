<?php
session_start();
include 'db_connect.php';

// Check if 'id' is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Ensure the ID is an integer to prevent SQL injection
    if (filter_var($id, FILTER_VALIDATE_INT)) {
        
        // Prepare and execute the query to update admin status to 'archived'
        $stmt = $conn->prepare("UPDATE admins SET status = 'archived' WHERE admin_ID = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Log the action (optional but good practice to track changes)
            $adminUsername = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Unknown';
            $action = "Archived admin with ID: $id";
            $historyStmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
            $historyStmt->bind_param("ss", $adminUsername, $action);
            $historyStmt->execute();
            $historyStmt->close();

            // Redirect to the super admin dashboard after success
            header("Location: super_admin_dashboard.php");
            exit();
        } else {
            // If something goes wrong with the query execution
            echo "Error archiving admin. Please try again later.";
        }

        // Close the statement
        $stmt->close();
    } else {
        // If the ID is invalid (not an integer)
        echo "Invalid Admin ID provided.";
    }
} else {
    // If 'id' is not set in the URL
    echo "Admin ID not provided in the URL.";
}
?>
