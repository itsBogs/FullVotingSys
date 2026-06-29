<?php
include 'db_connect.php';

// Combine distinct batches from both tables
$batch_result = $conn->query("
    SELECT DISTINCT batch FROM (
        SELECT batch FROM candidates
        UNION
        SELECT batch FROM students
    ) AS combined_batches
");

if ($batch_result && $batch_result->num_rows > 0) {
    while ($row = $batch_result->fetch_assoc()) {
        $batch = $row['batch'];

        // Check if batch already exists in batch_history
        $check = $conn->prepare("SELECT COUNT(*) FROM batch_history WHERE batch = ?");
        $check->bind_param("s", $batch);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count == 0) {
            $insert = $conn->prepare("INSERT INTO batch_history (batch) VALUES (?)");
            $insert->bind_param("s", $batch);
            $insert->execute();
            $insert->close();
        }
    }
}
?>
