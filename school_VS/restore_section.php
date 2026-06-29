<?php
include 'db_connect.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("UPDATE sections SET status = 'active' WHERE id = $id");

    // Log the action
    $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
    $action = "Restored section ID: $id";
    $stmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUsername, $action);
    $stmt->execute();
    $stmt->close();
}

header('Location: super_admin_dashboard.php');
exit();
?>
