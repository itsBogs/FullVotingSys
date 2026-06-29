<?php
session_start();
include 'db_connect.php';

// Handle adding new admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $stmt = $conn->prepare("INSERT INTO admins (username, password, status) VALUES (?, ?, 'active')");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->close();

    // Log transaction
    $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
    $action = "Added new admin: $username";
    $stmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUsername, $action);
    $stmt->execute();
    $stmt->close();
    
    header("Location: super_admin_dashboard.php");
    exit();
}

// Fetch admins
$admins = $conn->query("SELECT admin_ID, username, password, status FROM admins");
// Fetch transaction history
$history = $conn->query("SELECT admin_username, action, timestamp FROM transaction_history WHERE status = 'active' ORDER BY timestamp DESC");

?>
<style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        /* Style for the navbar links */
.navbar-nav .nav-link {
    color: white !important; /* Ensures text is always white */
    transition: background-color 0.3s ease, color 0.3s ease; /* Smooth transition */
}

/* Hover effect: Light background with rounded corners */
.navbar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2); 
    border-radius: 5px;
}

/* Active link styling: Bold text with a subtle background */
.navbar-nav .nav-link.active {
    font-weight: bold;
    color: #fff !important;
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 5px;
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
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script>
        function confirmClear() {
            if (confirm("Are you sure you want to clear all transaction history? This action cannot be undone.")) {
                window.location.href = "clear_history.php";
            }
        }
    </script>
</head>
<body class="bg-light">
 <!-- Navigation Bar -->
 <?php
                $conn = new mysqli("localhost:3306", "root", "", "voting_db");
                $current_page = basename($_SERVER['PHP_SELF']);
                $status_query = $conn->query("SELECT status FROM voting_status WHERE id = 1");
                $status_row = $status_query->fetch_assoc();
                $current_status = $status_row['status'];
            ?>
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
                <img src="temp.png" alt="School Logo" class="rounded-circle mt-2" width="80" style="margin-left: 20px; margin-right: 20px;">
                <a class="navbar-brand fw-bold text-white" href="admin_dashboard.php">Malacanang National Highschool</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'super_admin_dashboard.php') ? 'active' : ''; ?>" href="super_admin_dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'super_admin_history.php') ? 'active' : ''; ?>" href="super_admin_history.php">History</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'super_admin_control.php') ? 'active' : ''; ?>" href="super_admin_control.php">Voting Control</a></li>
                        <li class="nav-item"><a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Log Out</a></li>
                    </ul>
                </div>
            </nav>

            <div class="text-center bg-info text-white py-4">
                <h1 class="fs-4 fw-bold" style="padding-top: 10px;">SSS Voting System</h1>
            </div>
            <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Super Admin Dashboard</div>


    <!-- Transaction History -->
    <div class="card p-4 mt-4 shadow-sm">
        <h4 class="mb-3">Transaction History</h4>
        <div class="table-responsive">
        <!-- Clear All History Button -->
<!-- Clear All History Button -->
<a href="clear_history.php" class="btn btn-danger mb-3" onclick="return confirm('Are you sure you want to clear history?');">
    <i class="fa-solid fa-trash"></i> Clear All History
</a>

<!-- Restore History Button -->
<a href="restore_history.php" class="btn btn-warning mb-3">
    <i class="fa-solid fa-undo"></i> Restore History
</a>

            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $history->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['admin_username']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="text-center bg-info text-white py-3 mt-4">
        &copy; 2025 SSS Voting System
    </footer>

</body>
</html>
