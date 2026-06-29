<?php
// Include database connection
include('db_connect.php');

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize current_batch variable
$current_batch = 1;  // Set a default value

// Get current batch from the current_batch table
$batch_result = $conn->query("SELECT batch_number FROM current_batch LIMIT 1");

// Ensure the query returns a valid result
if ($batch_result && $batch_result->num_rows > 0) {
    $batch_row = $batch_result->fetch_assoc();
    $current_batch = isset($batch_row['batch_number']) ? intval($batch_row['batch_number']) : 1;
}

// Fetch all sections
$sections_stmt = $conn->prepare("SELECT * FROM sections ORDER BY grade ASC, section ASC");
$sections_stmt->execute();
$sections_result = $sections_stmt->get_result();

// Handle form submission for adding candidate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['lrn'])) {
    $lrn = $_POST['lrn'];
    $position = $_POST['position'];
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $grade = $_POST['grade'];
    $section = $_POST['section'];
    $general_average = $_POST['general_average'];

    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    // Check for duplicate LRN
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM candidates WHERE lrn = ? AND batch = ?");
    $check_stmt->bind_param("si", $lrn, $current_batch);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo "<script>alert('Error: LRN already exists for the current batch. Please use a unique LRN.');</script>";
    } else {
        // Upload image if provided
        if (!empty($image)) {
            $upload_path = "uploads/" . basename($image);
            move_uploaded_file($image_tmp, $upload_path);
        } else {
            $upload_path = "";
        }

        // Insert candidate
        $stmt = $conn->prepare("INSERT INTO candidates (lrn, position, last_name, first_name, middle_name, grade, section, general_average, image, batch) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssi", $lrn, $position, $last_name, $first_name, $middle_name, $grade, $section, $general_average, $upload_path, $current_batch);

        if ($stmt->execute()) {
            echo "<script>alert('New candidate added successfully.');</script>";
        } else {
            echo "<script>alert('Error adding candidate: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}

// Handle form submission for adding section
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_section'])) {
    $grade = $_POST['grade'];
    $section = $_POST['section'];

    // Get current batch number
$batch_result = $conn->query("SELECT batch_number FROM batch WHERE batchID = 1");
    $batch_row = $batch_result->fetch_assoc();
    $batch_number = $batch_row['batch_number'];

    // Insert new section
    $stmt = $conn->prepare("INSERT INTO sections (grade, section, batch) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $grade, $section, $batch_number);
    $stmt->execute();
    $stmt->close();

    // Log transaction
    session_start();
    $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
    $action = "Added new section: Grade $grade - $section";
    $stmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUsername, $action);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_dashboard.php");
    exit();
}

// Archive section
if (isset($_GET['archive_section'])) {
    $section_id = $_GET['archive_section'];
    $stmt = $conn->prepare("UPDATE sections SET status = 'archived' WHERE sectionID = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}

// Unarchive section
if (isset($_GET['unarchive_section'])) {
    $section_id = $_GET['unarchive_section'];
    $stmt = $conn->prepare("UPDATE sections SET status = 'active' WHERE sectionID = ?");
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin_dashboard.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>SSS Voting System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
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
        /* Fix Add Section Form Width */
.card.p-4.mb-4 {
    width: 100%;
    margin: auto;
}


/* Fix Sections List Table Width */
.table-responsive {
    width: 100%; /* Ensure the table takes up the full width of its container */
    overflow-x: auto; /* Enable horizontal scroll if the table overflows on smaller screens */
}

table {
    width: 100%; /* Ensure table takes up full width */
    table-layout: fixed; /* Ensures a consistent layout for table columns */
}

th, td {
    white-space: nowrap; /* Prevent text from wrapping */
    overflow: hidden; /* Hide overflowing text */
    text-overflow: ellipsis; /* Add ellipsis for overflowed text */
}

    </style>
</head>
<body class="bg-light">
<div class="container-fluid px-0">
        <div class="whole">
            <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
                <img src="temp.png" alt="School Logo" class="rounded-circle mt-2" width="80" style="margin-left: 20px; margin-right: 20px;">
                <a class="navbar-brand fw-bold text-white" href="admin_dashboard.php">Malacanang National Highschool</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="canprev.php">Candidate List</a></li>
                        <li class="nav-item"><a class="nav-link" href="voters.php">Voters List</a></li>
                        <li class="nav-item"><a class="nav-link" href="voted.php">Voters Status</a></li>
                        <li class="nav-item"><a class="nav-link" href="winners.php">Candidate Status</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
                    </ul>
                </div>
            </nav>

            <div class="text-center bg-info text-white py-4">
                <h1 class="fs-4 fw-bold">SSS Voting System</h1>
            </div>
            <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Admin Dashboard</div>

            <div class="text-center py-3 bg-info text-white fw-bold fs-5">Add Candidate</div>

            <!-- Display Current Batch -->
            <div class="text-center py-3 bg-light text-dark fw-bold fs-5">
                <h3>Current Batch: <?= $current_batch; ?></h3>
            </div>

        

            <!-- Add Candidate Form -->
            <div class="container my-4">
                <div class="card shadow p-4" style="width: 100%; margin: auto;">
                <form action="" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label fw-bold">LRN:</label>
        <input type="text" class="form-control" name="lrn" required pattern="\d{12}" maxlength="12" placeholder="Enter 12-digit LRN">
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Position:</label>
        <select name="position" class="form-select" required>
            <option value="President">President</option>
            <option value="Vice President">Vice President</option>
            <option value="Secretary">Secretary</option>
            <option value="Treasurer">Treasurer</option>
            <option value="Auditor">Auditor</option>
            <option value="P.I.O.">P.I.O.</option>
            <option value="Peace Officer">Peace Officer</option>
            <option value="G7 representative">G7 representative</option>
            <option value="G8 representative">G8 representative</option>
            <option value="G9 representative">G9 representative</option>
            <option value="G10 representative">G10 representative</option>
            <option value="G11 representative">G11 representative</option>
            <option value="G12 representative">G12 representative</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Last Name:</label>
        <input type="text" class="form-control" name="last_name" required pattern="[A-Za-z ]+" minlength="2" maxlength="50">
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">First Name:</label>
        <input type="text" class="form-control" name="first_name" required pattern="[A-Za-z ]+" minlength="2" maxlength="50">
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Middle Name:</label>
        <input type="text" class="form-control" name="middle_name" required pattern="[A-Za-z ]+" minlength="2" maxlength="50">
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Grade:</label>
        <select class="form-control" name="grade" id="grade" required>
            <option value="">Select Grade</option>
            <option value="7">7</option>
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Section:</label>
        <select class="form-control" name="section" id="section" required>
            <option value="">Select Section</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">General Average:</label>
        <input type="number" class="form-control" name="general_average" step="0.01" min="60" max="100" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-bold">Image:</label>
        <input type="file" class="form-control" name="image">
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-success me-2">Submit</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
    </div>
</form>

                </div>
            </div>

          <!-- Add Section Form -->
<div class="card p-4 mb-4 shadow-sm" style="width: 80%; margin: auto;">
    <h4 class="mb-3">Add New Section</h4>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Grade:</label>
            <select name="grade" class="form-select" required>
                <option value="">Select Grade</option>
                <?php for ($i = 7; $i <= 12; $i++) { ?>
                    <option value="<?php echo $i; ?>">Grade <?php echo $i; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Section Name:</label>
            <input type="text" name="section" class="form-control" required placeholder="e.g., Rizal, Bonifacio">
        </div>
        <button type="submit" name="add_section" class="btn btn-primary w-100">Add Section</button>
    </form>
</div>

<!-- Sections List -->
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <div class="card p-4 shadow-sm">
                <h4 class="mb-3">Sections List</h4>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover text-center">
                        <thead class="thead-dark">
                            <tr>
                                <th>Grade</th>
                                <th>Section Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($section = $sections_result->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($section['grade']); ?></td>
                                <td><?php echo htmlspecialchars($section['section']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($section['status'] == 'active') ? 'success' : 'secondary'; ?>">
                                        <?php echo htmlspecialchars($section['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_section.php?id=<?php echo $section['sectionID']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </a>
                                    <?php if ($section['status'] == 'active') { ?>
                                        <a href="?archive_section=<?php echo $section['sectionID']; ?>" class="btn btn-danger btn-sm">
                                            <i class="fa-solid fa-archive"></i> Archive
                                        </a>
                                    <?php } else { ?>
                                        <a href="?unarchive_section=<?php echo $section['sectionID']; ?>" class="btn btn-info btn-sm">
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
        
    </div>
</div>
<footer class="text-center bg-info text-white py-3 mt-4">
        &copy; 2025 SSS Voting System
    </footer>
        </div>

       
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       document.getElementById('grade').addEventListener('change', function() {
    const grade = this.value;
    const sectionSelect = document.getElementById('section');
    sectionSelect.innerHTML = '<option>Loading...</option>';

    fetch('get_sections.php?grade=' + grade)
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            data.forEach(section => {
                const option = document.createElement('option');
                option.value = section;
                option.textContent = section;
                sectionSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading sections:', error);
            sectionSelect.innerHTML = '<option>Error loading sections</option>';
        });
});
    </script>

    
</body>

</html>

