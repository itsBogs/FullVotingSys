<?php
$servername = "localhost:3306";
$username = "root"; // Default XAMPP MySQL username
$password = ""; // Default XAMPP has no password
$database = "voting_db"; // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
