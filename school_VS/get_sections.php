<?php
include('db_connect.php');

if (isset($_GET['grade'])) {
    $grade = $_GET['grade'];

    // Query to fetch only active sections for the selected grade
    $stmt = $conn->prepare("SELECT section FROM sections WHERE grade = ? AND status = 'active'");
    if ($stmt) {
        $stmt->bind_param("s", $grade);
        $stmt->execute();
        $result = $stmt->get_result();

        $sections = [];
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row['section'];
        }

        echo json_encode($sections);
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Failed to prepare the query.']);
    }
} else {
    echo json_encode(['error' => 'Grade parameter is missing.']);
}

$conn->close();
?>
