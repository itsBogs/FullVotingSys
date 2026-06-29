<?php
session_start();  // Keep this one

include 'db_connect.php';

// Handle adding new section
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_section'])) {
    $grade = $_POST['grade'];
    $section = $_POST['section'];

    // Fetch current batch number
    $batch_result = $conn->query("SELECT batch_number FROM current_batch WHERE id = 1");
    $batch_row = $batch_result->fetch_assoc();
    $batch_number = $batch_row['batch_number'];

    // Insert new section
    $stmt = $conn->prepare("INSERT INTO sections (grade, section, batch) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $grade, $section, $batch_number);
    $stmt->execute();
    $stmt->close();

    // Log transaction
    $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
    $action = "Added new section: Grade $grade - $section";
    $stmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUsername, $action);
    $stmt->execute();
    $stmt->close();

    // Redirect to prevent resubmission
    header("Location: super_admin_dashboard.php");
    exit();
}

// Check if an archive or unarchive action is triggered for sections
if (isset($_GET['archive_section'])) {
    $section_id = $_GET['archive_section'];
    $stmt = $conn->prepare("UPDATE sections SET status = 'archived' WHERE id = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();
    header("Location: super_admin_dashboard.php"); // Redirect to prevent resubmission
    exit();
}

if (isset($_GET['unarchive_section'])) {
    $section_id = $_GET['unarchive_section'];
    $stmt = $conn->prepare("UPDATE sections SET status = 'active' WHERE id = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();
    header("Location: super_admin_dashboard.php"); // Redirect to prevent resubmission
    exit();
}

// Fetch admins
$admins = $conn->query("SELECT admin_ID, username, password, status FROM admins");

// Fetch transaction history
$history = $conn->query("SELECT admin_username, action, timestamp FROM transaction_history WHERE status = 'active' ORDER BY timestamp DESC");


?>

<!-- HTML and styling as before -->

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    /* Style for the navbar links */
    .navbar-nav .nav-link {
        color: white !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Hover effect */
    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.2); 
        border-radius: 5px;
    }

    /* Active link styling */
    .navbar-nav .nav-link.active {
        font-weight: bold;
        color: #fff !important;
        background-color: rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }

    /* Footer Styles */
    footer {
        width: 100%;
        background-color: #17a2b8;
        padding: 15px 0;
        position: relative;
        bottom: 0;
    }

    /* Specific width for the Sections Table */
    /* Ensuring the table container and table width match */
    /* Ensure consistency in width with other sections */



    .card {
        max-width: 100%;
        margin: 0 auto;
    }

    .table-responsive {
        overflow-x: auto;
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
        $current_page = basename($_SERVER['PHP_SELF']);
    ?>
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

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Add Admin Form -->
        <div class="card p-4 mb-4 shadow-sm">
            <h4 class="mb-3">Add New Admin</h4>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="add_admin" class="btn btn-success w-100">Add Admin</button>
            </form>
        </div>

      

        <!-- Admins Table -->
        <div class="card p-4 shadow-sm">
            <h4 class="mb-3">Admin List</h4>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Username</th>
                            <th>Password (Hashed)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($admin = $admins->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td class="text-truncate" style="max-width: 200px;">
                                <?php echo htmlspecialchars(substr($admin['password'], 0, 20)) . "..."; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo ($admin['status'] == 'active') ? 'success' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars($admin['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_admin.php?id=<?php echo $admin['admin_ID']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-edit"></i> Edit
                                </a>
                                <?php if ($admin['status'] == 'active') { ?>
                                <a href="archive_admin.php?id=<?php echo $admin['admin_ID']; ?>" class="btn btn-danger btn-sm">
                                    <i class="fa-solid fa-archive"></i> Archive
                                </a>
                                <?php } else { ?>
                                <a href="unarchive_admin.php?id=<?php echo $admin['admin_ID']; ?>" class="btn btn-info btn-sm">
                                    <i class="fa-solid fa-unarchive"></i> Unarchive
                                </a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    


    <!-- Footer -->
    <footer class="text-center bg-info text-white py-3 mt-4">
        &copy; 2025 SSS Voting System
    </footer>
</body>
</html>
