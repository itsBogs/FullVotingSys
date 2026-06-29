<?php
include 'db_connect.php';

// Reset all active students to archived, and make sure this will be a new batch
$query = "UPDATE students SET status='archived' WHERE status='active'";
$conn->query($query);

// After resetting, you can proceed to show only the new batch
header('Location: your_current_page.php');
exit();
?>
