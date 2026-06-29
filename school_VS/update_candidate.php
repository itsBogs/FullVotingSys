<?php
$conn = new mysqli("localhost:3306", "root", "", "voting_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$last_name = $_POST['last_name'];
$first_name = $_POST['first_name'];
$middle_name = $_POST['middle_name'];
$grade = $_POST['grade'];
$section = $_POST['section'];
$position = $_POST['position'];
$general_average = $_POST['general_average'];

$image_name = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $image_name = basename($_FILES['image']['name']);
    $target = "uploads/" . $image_name;
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("UPDATE candidates SET last_name=?, first_name=?, middle_name=?, grade=?, section=?, position=?, general_average=?, image=? WHERE candidateID=?");
    $stmt->bind_param("ssssssssi", $last_name, $first_name, $middle_name, $grade, $section, $position, $general_average, $image_name, $id);
} else {
    $stmt = $conn->prepare("UPDATE candidates SET last_name=?, first_name=?, middle_name=?, grade=?, section=?, position=?, general_average=? WHERE candidateID=?");
    $stmt->bind_param("sssssssi", $last_name, $first_name, $middle_name, $grade, $section, $position, $general_average, $id);
}

$stmt->execute();
echo "success";
$conn->close();
