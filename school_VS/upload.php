<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_file'])) {
    $file = $_FILES['upload_file'];
    $fileName = $_FILES['upload_file']['name'];
    $fileTmpName = $_FILES['upload_file']['tmp_name'];
    $fileSize = $_FILES['upload_file']['size'];
    $fileError = $_FILES['upload_file']['error'];
    $fileType = $_FILES['upload_file']['type'];

    // Allowable file types
    $allowed = array('csv');

    // Extract file extension
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Check for file errors
    if ($fileError === 0) {
        // Validate file type
        if (in_array($fileExt, $allowed)) {
            if ($fileSize < 5000000) { // Max file size: 5MB
                // Generate a unique name for the file
                $fileNewName = uniqid('', true) . "." . $fileExt;
                // Set upload directory
                $fileDestination = 'uploads/' . $fileNewName;

                // Move the file to the upload directory
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    echo "CSV file uploaded successfully!";

                    // Optional: Process the CSV file
                    if (($handle = fopen($fileDestination, 'r')) !== FALSE) {
                        // Read the CSV file line by line
                        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                            // Example: print each row
                            // var_dump($data);
                            // You can add logic here to insert the data into a database or perform other actions
                        }
                        fclose($handle);
                    }
                } else {
                    echo "Error uploading the file.";
                }
            } else {
                echo "File is too large. Max file size is 5MB.";
            }
        } else {
            echo "Invalid file type. Only CSV files are allowed.";
        }
    } else {
        echo "There was an error uploading your file.";
    }
}
?>
