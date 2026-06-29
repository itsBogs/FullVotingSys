<?php
$conn = new mysqli("localhost:3306", "root", "", "voting_db");

if (isset($_POST['toggle_voting'])) {
    $status_query = $conn->query("SELECT status FROM voting_status WHERE id = 1");
    $status_row = $status_query->fetch_assoc();
    $new_status = ($status_row['status'] == 'open') ? 'closed' : 'open';

    $conn->query("UPDATE voting_status SET status = '$new_status' WHERE id = 1");
}

header("Location: super_admin_control.php");
exit();
?>
