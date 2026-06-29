<?php
$conn = new mysqli("localhost:3306", "root", "", "voting_db");
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $candidateID = $conn->real_escape_string($_POST["candidateID"]);
    $lrn = $conn->real_escape_string($_POST["lrn"]);
    $name = $conn->real_escape_string($_POST["name"]);
    $position = $conn->real_escape_string($_POST["position"]);
    $grade = $conn->real_escape_string($_POST["grade"]);
    $section = $conn->real_escape_string($_POST["section"]);
    $general_average = $conn->real_escape_string($_POST["general_average"]);

    // Fetch old data before updating
    $oldDataQuery = "SELECT candidateID, lrn, name, position, grade, section, general_average, image FROM candidates WHERE candidateID = ?";
    $stmt = $conn->prepare($oldDataQuery);
    $stmt->bind_param("i", $candidateID);
    $stmt->execute();
    $oldDataResult = $stmt->get_result();
    $oldData = $oldDataResult->fetch_assoc();
    $stmt->close();

    if (!$oldData) {
        die("Candidate not found.");
    }

    $updateFields = [];
    $logChanges = [];

    // Check each field for changes
    if ($oldData['lrn'] !== $lrn) {
        $updateFields[] = "lrn='$lrn'";
        $logChanges[] = "LRN changed from '{$oldData['lrn']}' to '$lrn'";
    }
    if ($oldData['name'] !== $name) {
        $updateFields[] = "name='$name'";
        $logChanges[] = "Name changed from '{$oldData['name']}' to '$name'";
    }
    if ($oldData['position'] !== $position) {
        $updateFields[] = "position='$position'";
        $logChanges[] = "Position changed from '{$oldData['position']}' to '$position'";
    }
    if ($oldData['grade'] !== $grade) {
        $updateFields[] = "grade='$grade'";
        $logChanges[] = "Grade changed from '{$oldData['grade']}' to '$grade'";
    }
    if ($oldData['section'] !== $section) {
        $updateFields[] = "section='$section'";
        $logChanges[] = "Section changed from '{$oldData['section']}' to '$section'";
    }
    if ($oldData['general_average'] != $general_average) {
        $updateFields[] = "general_average='$general_average'";
        $logChanges[] = "General Average changed from '{$oldData['general_average']}' to '$general_average'";
    }

    // Image upload check
    $newImage = $oldData['image'];
    if (!empty($_FILES["image"]["name"])) {
        $image_name = basename($_FILES["image"]["name"]);
        $image_path = "uploads/" . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        $updateFields[] = "image='$image_name'";
        $logChanges[] = "Image updated";
        $newImage = $image_name;
    }

    // Perform update if changes exist
    if (!empty($updateFields)) {
        $updateSQL = "UPDATE candidates SET " . implode(", ", $updateFields) . " WHERE candidateID='$candidateID'";
        if ($conn->query($updateSQL) === TRUE) {
            // Log the transaction
            $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
            $action = "Edited candidate: {$oldData['name']} ({$oldData['position']})\n" . implode("\n", $logChanges);

            $logStmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
            $logStmt->bind_param("ss", $adminUsername, $action);
            $logStmt->execute();
            $logStmt->close();

            echo "success";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "No changes made.";
    }
}

$conn->close();
?>
