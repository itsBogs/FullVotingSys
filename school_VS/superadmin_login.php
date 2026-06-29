<?php
session_start();
$conn = new mysqli("localhost:3306", "root", "", "voting_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM super_admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['super_admin'] = $row['username'];
            header("Location: super_admin_dashboard.php");
            exit();
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "Username not found!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        html, body {
            height: 100%;
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
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold text-white d-flex align-items-center" href="index.php">
                <img src="temp.png" alt="School Logo" class="rounded-circle" width="60" style="margin-right: 10px;">
                Malacanang National Highschool
            </a>
            <a href="admin_login.php" class="btn btn-light">
                <i class="fa-solid fa-user-shield"></i> Admin
            </a>
        </div>
    </nav>

    <!-- Header -->
    <div class="text-center bg-info text-white py-3">
        <h1 class="fs-4 fw-bold">SSS Voting System</h1>
    </div>

    <!-- Login Card -->
    <div class="content">
        <div class="card shadow-lg">
            <div class="row">
                <!-- Form Column -->
                <div class="col-md-6 border-end">
                    <h3 class="text-center mb-3">Super Admin Login</h3>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger text-center"><?= $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="request_reset.php" class="text-primary">Forgot Password?</a>
                    </div>
                </div>

                <!-- Logo Column -->
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <img src="temp.png" alt="School Logo" class="logo-img">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; 2025 SSS Voting System | All Rights Reserved
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
