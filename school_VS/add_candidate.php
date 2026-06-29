<?php 
// Database Connection
$conn = new mysqli("localhost:3306", "root", "", "voting_db");
session_start(); // Ensure session is started to track admin username

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if Form is Submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lrn = trim($conn->real_escape_string($_POST["lrn"]));  // Added LRN input
    $fullname = trim($conn->real_escape_string($_POST["fullname"]));
    $position = $conn->real_escape_string($_POST["position"]);
    $grade = $conn->real_escape_string($_POST["grade"]);
    $section = $conn->real_escape_string($_POST["section"]);
    $general_average = $conn->real_escape_string($_POST["general_average"]);

    // 🔒 Check for duplicate based on LRN and position (case-insensitive)
    $checkStmt = $conn->prepare("SELECT * FROM candidates WHERE LOWER(lrn) = LOWER(?) AND LOWER(position) = LOWER(?)");
    $checkStmt->bind_param("ss", $lrn, $position);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('Candidate with the same LRN and position already exists.'); window.history.back();</script>";
        exit();
    }
    $checkStmt->close();

    // Handle File Upload (with validation for image type and size)
    $image_name = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Validate the image file (allowed types: jpg, jpeg, png, gif)
        $allowed_types = ["image/jpeg", "image/png", "image/gif"];
        $file_type = $_FILES["image"]["type"];

        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Invalid image type. Allowed types: JPG, PNG, GIF.'); window.history.back();</script>";
            exit();
        }

        // Check image size (max 2MB)
        if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            echo "<script>alert('Image size too large. Max size is 2MB.'); window.history.back();</script>";
            exit();
        }

        // Save the image
        $image_name = basename($_FILES["image"]["name"]);
        $image_path = "uploads/" . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    // Insert Data into MySQL
    $sql = "INSERT INTO candidates (lrn, name, position, grade, section, general_average, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssds", $lrn, $fullname, $position, $grade, $section, $general_average, $image_name);

    if ($stmt->execute()) {
        // Log transaction
        $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
        $action = "Added candidate: $fullname (Position: $position, Grade: $grade, Section: $section, Average: $general_average, LRN: $lrn)";

        $logStmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
        $logStmt->bind_param("ss", $adminUsername, $action);
        $logStmt->execute();
        $logStmt->close();

        echo "<script>alert('Candidate added successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
