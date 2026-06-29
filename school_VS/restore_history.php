<?php
include 'db_connect.php';

// Restore all archived transaction history
$conn->query("UPDATE transaction_history SET status = 'active' WHERE status = 'archived'");

header("Location: super_admin_dashboard.php");
exit();
?>
