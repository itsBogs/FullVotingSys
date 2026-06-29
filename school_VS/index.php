<?php 
session_start();
include 'db_connect.php';

$warning_message = '';
$voting_closed = false;

// Get the current batch number
$current_batch = '';
$batch_query = $conn->query("SELECT batch_number FROM current_batch ORDER BY updated_at DESC LIMIT 1");
if ($batch_query && $batch_query->num_rows > 0) {
    $batch_row = $batch_query->fetch_assoc();
    $current_batch = $batch_row['batch_number'];
} else {
    $warning_message = "Current batch not set. Please contact administrator.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($warning_message)) {
    $lrn = trim($_POST['lrn']);
    $full_name = trim($_POST['full_name']);

    $name_parts = array_filter(array_map('trim', explode(' ', $full_name)));

    if (count($name_parts) < 2) {
        $warning_message = 'Please enter your full name at least Firstname and Lastname.';
    } else {
        $first_name = ucwords(strtolower(array_shift($name_parts)));
        $last_name = ucwords(strtolower(array_pop($name_parts)));
        $middle_name = $name_parts ? ucwords(strtolower(implode(' ', $name_parts))) : '';

        if ($middle_name === '') {
            $stmt = $conn->prepare("
                SELECT student_ID FROM students
                WHERE lrn = ?
                AND LOWER(TRIM(first_name)) = LOWER(TRIM(?))
                AND LOWER(TRIM(last_name)) = LOWER(TRIM(?))
                AND (middle_name IS NULL OR TRIM(middle_name) = '')
                AND batch = ?
                AND status = 'active'
            ");
            $stmt->bind_param("sssi", $lrn, $first_name, $last_name, $current_batch);
        } else {
            $stmt = $conn->prepare("
                SELECT student_ID FROM students
                WHERE lrn = ?
                AND LOWER(TRIM(first_name)) = LOWER(TRIM(?))
                AND LOWER(TRIM(last_name)) = LOWER(TRIM(?))
                AND LOWER(TRIM(middle_name)) = LOWER(TRIM(?))
                AND batch = ?
                AND status = 'active'
            ");
            $stmt->bind_param("ssssi", $lrn, $first_name, $last_name, $middle_name, $current_batch);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $student = $result->fetch_assoc();
            $_SESSION['student_id'] = $student['student_ID'];
            $_SESSION['student_name'] = $full_name;
            header("Location: voter_dashboard.php");
            exit();
        } else {
            $warning_message = "Student not found, inactive, or not eligible for this batch.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSG Voting System - Voter Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            width: 100%;
            max-width: 700px;
            padding: 20px;
        }
        footer {
            background-color: #17a2b8;
            color: white;
            text-align: center;
            padding: 10px;
            width: 100%;
        }
        .card .col-md-6 {
            padding: 15px;
        }
        .logo-img {
            max-width: 100%;
            max-height: 200px;
        }
    </style>
</head>
<body class="bg-light">
<div class="wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold text-white d-flex align-items-center" href="index.php">
                <img src="temp.png" alt="School Logo" class="rounded-circle" width="60" style="margin-right: 10px;">
                Malacanang National Highschool
            </a>
            <div class="ms-auto d-flex">
                <a href="admin_login.php" class="btn btn-light me-2">
                    <i class="fa-solid fa-user-tie"></i> Admin
                </a>
                <a href="superadmin_login.php" class="btn btn-light">
                    <i class="fa-solid fa-user-shield"></i> Super Admin
                </a>
            </div>
        </div>
    </nav>

    <div class="text-center bg-info text-white py-3">
        <h1 class="fs-4 fw-bold">SSG Voting System</h1>
    </div>

    <div class="content">
        <div class="card shadow-lg">
            <div class="row">
                <!-- Left Column: Form -->
                <div class="col-md-6 border-end">
                    <h3 class="text-center mb-3">Voter Login</h3>

                    <?php if (!empty($warning_message)) : ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $warning_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($voting_closed): ?>
                        <div class="alert alert-warning text-center">
                            Voting is currently closed. Please check back later.
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold">LRN:</label>
                                <input type="text" name="lrn" class="form-control" placeholder="12-digits" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Full Name:</label>
                                <input type="text" name="full_name" class="form-control" placeholder="e.g. Mark Lhemuel Galang Arenas" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Right Column: Logo Image -->
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <img src="temp.png" alt="School Logo" class="logo-img">
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2025 SSG Voting System | All Rights Reserved
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
