<?php
include 'db_connect.php';

$current_batch_result = $conn->query("SELECT batch_number FROM current_batch LIMIT 1");
if ($current_batch_result && $current_batch_result->num_rows > 0) {
    $row = $current_batch_result->fetch_assoc();
    $current_batch = $row['batch_number'];
} else {
    echo "<script>alert('Error: Could not retrieve current batch.'); window.location.href = 'voters.php';</script>";
    exit();
}

$errors = [];

if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
    $csvFile = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($csvFile, 'r')) !== FALSE) {
        fgetcsv($handle); // skip header

        $stmt = $conn->prepare("INSERT INTO students (last_name, first_name, middle_name, grade, section, lrn, status, batch) VALUES (?, ?, ?, ?, ?, ?, 'active', ?)");
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $lastName = trim($data[0]);
            $firstName = trim($data[1]);
            $middleName = trim($data[2]);
            $grade = trim($data[3]);
            $section = trim($data[4]);
            $lrn = trim($data[5]);

            // Validate LRN format
            if (!preg_match('/^\d{12}$/', $lrn)) {
                $errors[] = "Invalid LRN format for student $lastName, $firstName $middleName: $lrn";
                continue;
            }

            // Check for duplicate LRN
            $checkLrnStmt = $conn->prepare("SELECT student_ID FROM students WHERE lrn = ? AND batch = ?");
            $checkLrnStmt->bind_param('si', $lrn, $current_batch);
            $checkLrnStmt->execute();
            $checkLrnStmt->store_result();

            if ($checkLrnStmt->num_rows > 0) {
                $errors[] = "Duplicate LRN for batch $current_batch: $lrn ($lastName, $firstName $middleName)";
            } else {
                $stmt->bind_param('ssssssi', $lastName, $firstName, $middleName, $grade, $section, $lrn, $current_batch);
                $stmt->execute();
            }

            $checkLrnStmt->close();
        }

        fclose($handle);
        $stmt->close();

        // Handle success and errors
        if (empty($errors)) {
            echo "<script>alert('CSV uploaded and students added successfully!'); window.location.href = 'voters.php';</script>";
        } else {
            $errorMessage = implode("\\n", $errors);
            echo "<script>alert('Upload completed with some issues:\\n$errorMessage'); window.location.href = 'voters.php';</script>";
        }

    } else {
        echo "<script>alert('Failed to open the CSV file!'); window.location.href = 'voters.php';</script>";
    }
} else {
    echo "<script>alert('No file uploaded or upload error.'); window.location.href = 'voters.php';</script>";
}
?>
