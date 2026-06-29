<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['voter_id'])) {
    echo "<script>alert('You must log in to vote.'); window.location.href='login.php';</script>";
    exit();
}

$voter_id = $_SESSION['voter_id'];
$candidate_id = $_POST['candidate_id'];

// Check if the selected candidate is still active
$stmt = $conn->prepare("SELECT status FROM candidates WHERE id = ?");
$stmt->bind_param("i", $candidate_id);
$stmt->execute();
$result = $stmt->get_result();
$candidate = $result->fetch_assoc();

if (!$candidate || $candidate['status'] !== 'active') {
    echo "<script>alert('Error: You cannot vote for an archived candidate!'); window.history.back();</script>";
    exit();
}

// Insert the vote
$stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id) VALUES (?, ?)");
$stmt->bind_param("ii", $voter_id, $candidate_id);

if ($stmt->execute()) {
    echo "<script>alert('Vote submitted successfully!'); window.location.href='voter_dashboard.php';</script>";
} else {
    echo "<script>alert('Error submitting vote.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
