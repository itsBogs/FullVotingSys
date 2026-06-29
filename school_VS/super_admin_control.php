<?php
session_start();
include 'db_connect.php';

// Fetch the voting status
// Fetch the voting status
$status_query = $conn->query("SELECT status FROM voting_status WHERE id = 1");
$status_row = $status_query->fetch_assoc();
$current_status = $status_row['status'];

// Fetch all unique batches from the candidates table (only active ones)
$batch_query = $conn->query("SELECT DISTINCT batch FROM batch_history ORDER BY election_date DESC");

// Always enable the download button
$disable_download = '';

$batches = [];
while ($batch_row = $batch_query->fetch_assoc()) {
    $batches[] = $batch_row['batch'];
}


?>
<?php
// Display error or success messages
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
    unset($_SESSION['success_message']);
}
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    .navbar-nav .nav-link {
        color: white !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 5px;
    }
    .navbar-nav .nav-link.active {
        font-weight: bold;
        color: #fff !important;
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }
    .container {
        max-width: 900px;
        margin-top: 50px;
    }
    .btn-control {
        width: 100%;
        padding: 10px;
        font-size: 1.2rem;
    }
    .status-text {
        font-size: 1.1rem;
    }
    .card {
        margin-top: 30px;
    }
    .download-btn {
        width: 100%;
        padding: 12px;
        font-size: 1.2rem;
    }
    footer {
        width: 100%;
        background-color: #17a2b8;
        padding: 15px 0;
        position: relative;
        bottom: 0;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard - Voting Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
    <img src="temp.png" alt="School Logo" class="rounded-circle mt-2" width="80" style="margin-left: 20px; margin-right: 20px;">
    <a class="navbar-brand fw-bold text-white" href="admin_dashboard.php">Malacanang National Highschool</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="super_admin_dashboard.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="super_admin_history.php">History</a></li>
            <li class="nav-item"><a class="nav-link active" href="super_admin_control.php">Voting Control</a></li>
            <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
        </ul>
    </div>
</nav>

<div class="text-center bg-info text-white py-4">
    <h1 class="fs-4 fw-bold" style="padding-top: 10px;">SSS Voting System</h1>
</div>
<div class="text-center py-3 bg-primary text-white fw-bold fs-5">Super Admin Dashboard</div>

<div class="container text-center">

    <!-- Display error or success messages -->
    <?php
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    ?>

    <!-- Voting Control Card -->
    <div class="card p-4 shadow-sm">
        <h3 class="mb-3">Voting Control</h3>

        <!-- Voting Status Button -->
        <form action="toggle_voting.php" method="POST">
            <button type="submit" name="toggle_voting" class="btn btn-<?= $current_status == 'open' ? 'danger' : 'success' ?> btn-control">
                <?= $current_status == 'open' ? 'Stop Voting' : 'Start Voting' ?>
            </button>
        </form>

        <p class="mt-2 fw-bold text-<?= $current_status == 'open' ? 'success' : 'danger' ?> status-text">
            Voting is currently <span class="text-uppercase"> <?= $current_status ?> </span>
        </p>
    </div>

    <!-- Batch Selection Dropdown -->
    <div class="card p-4 shadow-sm mt-4">
        <h4>Select Batch</h4>
        <form action="batch_select.php" method="POST">
            <div class="mb-3">
                <select name="batch" class="form-select" required>
                    <option value="" disabled selected>Select a Batch</option>
                    <?php foreach ($batches as $batch): ?>
    <option value="<?= $batch ?>">Batch <?= htmlspecialchars($batch) ?></option>
<?php endforeach; ?>

                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-control">Select Batch</button>
        </form>
    </div>

    <!-- Add New Batch Form -->
    <div class="card p-4 shadow-sm mt-4">
        <h4>Add New Batch</h4>
        <form action="add_batch.php" method="POST">
            <div class="mb-3">
                <input type="number" name="new_batch" class="form-control" placeholder="Enter new batch number" required>
            </div>
            <button type="submit" class="btn btn-success btn-control">Add New Batch</button>
        </form>
    </div>

    <!-- Download PDF Button -->
    <div class="card p-4 shadow-sm">
        <h4>Download Election Results</h4>
        <a href="download_winners.php" class="btn btn-danger download-btn <?= $disable_download ?>">
            Download PDF
        </a>
       

    </div>

</div>

<footer class="text-center bg-info text-white py-3 mt-4">
    &copy; 2025 SSS Voting System
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
