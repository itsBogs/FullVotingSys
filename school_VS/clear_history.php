<?php
include 'db_connect.php';

// Archive all transaction history instead of deleting
$conn->query("UPDATE transaction_history SET status = 'archived' WHERE status = 'active'");

header("Location: super_admin_dashboard.php");
exit();
?>
