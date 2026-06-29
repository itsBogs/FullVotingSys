<?php
include 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['upload_file']) || $_FILES['upload_file']['error'] != 0) {
        die("File upload error: " . $_FILES['upload_file']['error']);
    }

    $fileTmp = $_FILES['upload_file']['tmp_name'];
    $fileName = $_FILES['upload_file']['name'];
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);

    // Variable to hold extracted text
    $text = '';

    if ($ext === 'txt' || $ext === 'csv') {
        $text = file_get_contents($fileTmp);
    } else {
        die("Unsupported file type. Only .txt and .csv files are allowed.");
    }

    echo "Extracted Text:<br><pre>" . htmlspecialchars($text) . "</pre><br>";

    $lines = explode("\n", $text);
    $inserted = 0;
    foreach ($lines as $line) {
        $line = trim($line);

        // Skip empty lines
        if ($line === '') continue;

        $parts = array_map('trim', explode(',', $line));

        if (count($parts) === 5) { // 5 parts: last_name, first_name, middle_name, grade, section, lrn
            list($last_name, $first_name, $middle_name, $grade, $section, $lrn) = $parts;

            // Clean LRN of potential non-numeric chars
            $lrn = preg_replace('/\D/', '', $lrn);

            // Debug output
            echo "Processing: $last_name, $first_name, $middle_name, $grade, $section, $lrn<br>";

            if (is_numeric($grade) && is_numeric($lrn) && strlen($lrn) === 12) {
                $stmt = $conn->prepare("SELECT id FROM students WHERE lrn = ?");
                $stmt->bind_param("s", $lrn);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows === 0) {
                    $stmt = $conn->prepare("INSERT INTO students (last_name, first_name, middle_name, grade, section, lrn, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
                    $stmt->bind_param("sssiis", $last_name, $first_name, $middle_name, $grade, $section, $lrn);
                    if ($stmt->execute()) {
                        $inserted++;
                    } else {
                        echo "Insert error for LRN $lrn: " . $stmt->error . "<br>";
                    }
                } else {
                    echo "Duplicate LRN found: $lrn<br>";
                }
            } else {
                echo "Invalid grade or LRN format for line: $line<br>";
            }
        } else {
            echo "Skipping malformed line: $line<br>";
        }
    }

    echo "<script>alert('Upload successful! $inserted students added.'); window.location.href='voters.php';</script>";
}
?>
