<?php
include 'db_connect.php';

// Fetch current batch from current_batch table
$currentBatchResult = $conn->query("SELECT batch_number FROM current_batch WHERE current_batchID = 1");
if ($currentBatchResult) {
    $currentBatch = $currentBatchResult->fetch_assoc()['batch_number'];
} else {
    echo "Error fetching current batch: " . $conn->error;
    exit();
}

// Fetch active students based on current batch and group by grade and section
$students = [];
if (!empty($currentBatch)) {
    $result = $conn->query("SELECT * FROM students WHERE status='active' AND batch = '$currentBatch' ORDER BY last_name, first_name, middle_name");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $students[$row['grade']][$row['section']][] = $row;
        }
    } else {
        echo "Error fetching students: " . $conn->error;
    }
} else {
    echo "Error: Current batch is not set!";
}

// Fetch archived students
$archivedResult = $conn->query("SELECT * FROM students WHERE status='archived' ORDER BY last_name, first_name, middle_name");
if (!$archivedResult) {
    echo "Error fetching archived students: " . $conn->error;
    exit();
}

// Get current page for navbar active class (optional)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SSS Voting System</title>
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
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
        .btn {
            min-width: 140px;
            font-weight: 500;
            border-radius: 8px;
        }
        .btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid px-0">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
        <img src="temp.png" alt="School Logo" class="rounded-circle mt-2" width="80" style="margin-left: 20px; margin-right: 20px;" />
        <a class="navbar-brand fw-bold text-white" href="admin_dashboard.php">Malacanang National Highschool</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'admin_dashboard.php') ? 'active' : ''; ?>" href="admin_dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'canprev.php') ? 'active' : ''; ?>" href="canprev.php">Candidate List</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'voters.php') ? 'active' : ''; ?>" href="voters.php">Voters List</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'voted.php') ? 'active' : ''; ?>" href="voted.php">Voters Status</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'winners.php') ? 'active' : ''; ?>" href="winners.php">Candidate Status</a></li>
                <li class="nav-item"><a class="nav-link <?= ($current_page == 'index.php') ? 'active' : ''; ?>" href="index.php">Log Out</a></li>
            </ul>
        </div>
    </nav>

    <div class="text-center bg-info text-white py-4">
        <h1 class="fs-4 fw-bold">SSS Voting System</h1>
    </div>

    <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Admin Dashboard</div>
    <div class="text-center py-3 bg-info text-white fw-bold fs-5">Voter Status</div>

    <!-- Display the current batch -->
    <div class="text-center py-3 bg-light text-dark fw-bold fs-5">
        <span class="fs-6">Current Batch: <?= htmlspecialchars($currentBatch); ?></span>
    </div>

    <div class="container my-4" style="width: 70%;">
        <div class="d-flex justify-content-between mb-3">
            <!-- Add New Student Button -->
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                <i class="fas fa-user-plus"></i> Add Student
            </button>

            <!-- Archived Students Button -->
            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#archivedStudentsModal">
                <i class="fas fa-box-archive"></i> Archived Students
            </button>

            <!-- Upload CSV Button -->
            <form action="upload_csv.php" method="POST" enctype="multipart/form-data" class="d-flex align-items-center">
                <input type="file" name="csv_file" accept=".csv" class="form-control form-control-sm me-2" required />
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-upload"></i> Upload CSV
                </button>
            </form>
        </div>

        <table class="table table-bordered mt-3 text-center">
            <thead class="table-primary">
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Grade</th>
                    <th>Section</th>
                    <th>LRN</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $grade => $sections) { ?>
                    <tr class="table-secondary">
                        <td colspan="7" class="fw-bold">Grade <?= htmlspecialchars($grade) ?></td>
                    </tr>
                    <?php foreach ($sections as $section => $studentList) { ?>
                        <tr class="table-info">
                            <td colspan="7" class="fw-bold">Section <?= htmlspecialchars($section) ?></td>
                        </tr>
                        <?php foreach ($studentList as $row) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['last_name']); ?></td>
                                <td><?= htmlspecialchars($row['first_name']); ?></td>
                                <td><?= htmlspecialchars($row['middle_name']); ?></td>
                                <td><?= htmlspecialchars($row['grade']); ?></td>
                                <td><?= htmlspecialchars($row['section']); ?></td>
                                <td><?= htmlspecialchars($row['lrn']); ?></td>
                                <td class="d-flex justify-content-center">
                                    <button class="btn btn-warning btn-sm mx-1 edit-btn"
                                        data-id="<?= $row['student_ID']; ?>"
                                        data-lastname="<?= $row['last_name']; ?>"
                                        data-firstname="<?= $row['first_name']; ?>"
                                        data-middlename="<?= $row['middle_name']; ?>"
                                        data-grade="<?= $row['grade']; ?>"
                                        data-section="<?= $row['section']; ?>"
                                        data-lrn="<?= $row['lrn']; ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStudentModal">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="archive_student.php?student_ID=<?= $row['student_ID']; ?>" class="btn btn-danger btn-sm mx-1">
                                        <i class="fas fa-archive"></i> Archive
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="add_student.php" method="POST" id="addStudentForm">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add-last-name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="add-last-name" name="last_name" required />
                        </div>
                        <div class="mb-3">
                            <label for="add-first-name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="add-first-name" name="first_name" required />
                        </div>
                        <div class="mb-3">
                            <label for="add-middle-name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="add-middle-name" name="middle_name" />
                        </div>
                        <div class="mb-3">
                            <label for="add-grade" class="form-label">Grade</label>
                            <select id="add-grade" name="grade" class="form-select" required>
                                <option value="" disabled selected>Select Grade</option>
                                <?php 
                                // Fetch distinct grades to fill options (optional)
                                $gradesResult = $conn->query("SELECT DISTINCT grade FROM sections ORDER BY grade");
                                while ($gradeRow = $gradesResult->fetch_assoc()) {
                                    echo "<option value=\"" . htmlspecialchars($gradeRow['grade']) . "\">" . htmlspecialchars($gradeRow['grade']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add-section" class="form-label">Section</label>
                            <select id="add-section" name="section" class="form-select" required>
                                <option value="" disabled selected>Select Section</option>
                                <!-- Sections will be dynamically loaded by JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lrn" class="form-label">LRN (12 digits)</label>
                            <input type="text" class="form-control" name="lrn" id="lrn" pattern="\d{12}" maxlength="12" minlength="12" required title="LRN must be exactly 12 digits" />
                            <small id="lrnWarning" class="text-danger d-none">LRN must be exactly 12 digits!</small>
                        </div>
                        <input type="hidden" name="batch" value="<?= htmlspecialchars($currentBatch); ?>" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="edit_student.php" method="POST" id="editStudentForm">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit-student-id" name="student_ID" />
                        <div class="mb-3">
                            <label for="edit-last-name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit-last-name" name="last_name" required />
                        </div>
                        <div class="mb-3">
                            <label for="edit-first-name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit-first-name" name="first_name" required />
                        </div>
                        <div class="mb-3">
                            <label for="edit-middle-name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="edit-middle-name" name="middle_name" />
                        </div>
                        <div class="mb-3">
                            <label for="edit-grade" class="form-label">Grade</label>
                            <select id="edit-grade" name="grade" class="form-select" required>
                                <option value="" disabled>Select Grade</option>
                                <?php
                                // Reuse grade options
                                $gradesResult->data_seek(0); // reset pointer
                                while ($gradeRow = $gradesResult->fetch_assoc()) {
                                    echo "<option value=\"" . htmlspecialchars($gradeRow['grade']) . "\">" . htmlspecialchars($gradeRow['grade']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-section" class="form-label">Section</label>
                            <select id="edit-section" name="section" class="form-select" required>
                                <option value="" disabled>Select Section</option>
                                <!-- Sections will be dynamically loaded by JS -->
                            </select>
                        </div>
                    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Archived Students Modal -->
    <div class="modal fade" id="archivedStudentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Archived Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered text-center">
                        <thead class="table-secondary">
                            <tr>
                                <th>Last Name</th>
                                <th>First Name</th>
                                <th>Middle Name</th>
                                <th>Grade</th>
                                <th>Section</th>
                                <th>LRN</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($archived = $archivedResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($archived['last_name']); ?></td>
                                    <td><?= htmlspecialchars($archived['first_name']); ?></td>
                                    <td><?= htmlspecialchars($archived['middle_name']); ?></td>
                                    <td><?= htmlspecialchars($archived['grade']); ?></td>
                                    <td><?= htmlspecialchars($archived['section']); ?></td>
                                    <td><?= htmlspecialchars($archived['lrn']); ?></td>
                                    <td>
                                        <a href="restore_student.php?student_ID=<?= $archived['student_ID']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-undo"></i> Restore
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($archivedResult->num_rows === 0) { ?>
                                <tr><td colspan="7">No archived students.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center bg-info text-white py-3 mt-4">&copy; 2025 SSS Voting System</footer>
</div>

</body>
<script>
    // Edit modal: populate fields when clicking Edit button
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const lastName = button.getAttribute('data-lastname');
            const firstName = button.getAttribute('data-firstname');
            const middleName = button.getAttribute('data-middlename');
            const grade = button.getAttribute('data-grade');
            const section = button.getAttribute('data-section');
            const lrn = button.getAttribute('data-lrn');

            document.getElementById('edit-student-id').value = id;
            document.getElementById('edit-last-name').value = lastName;
            document.getElementById('edit-first-name').value = firstName;
            document.getElementById('edit-middle-name').value = middleName;
            document.getElementById('edit-grade').value = grade;
            loadSections(grade, 'edit-section', section);
            document.getElementById('edit-lrn').value = lrn;
        });
    });

    // Load sections for grade dropdown dynamically
    function loadSections(grade, sectionDropdownId, selectedSection = null) {
        fetch(`get_sections.php?grade=${encodeURIComponent(grade)}`)
            .then(response => response.json())
            .then(sections => {
                const sectionDropdown = document.getElementById(sectionDropdownId);
                sectionDropdown.innerHTML = '<option value="" disabled>Select Section</option>';
                sections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section;
                    option.textContent = section;
                    if (section === selectedSection) {
                        option.selected = true;
                    }
                    sectionDropdown.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Failed to fetch sections:', err);
            });
    }

    // When grade changes in Add Student modal, update sections
    document.getElementById('add-grade').addEventListener('change', function() {
        loadSections(this.value, 'add-section');
    });

    // When grade changes in Edit Student modal, update sections
    document.getElementById('edit-grade').addEventListener('change', function() {
        loadSections(this.value, 'edit-section');
    });

    // LRN validation (Add and Edit forms)
    const addLrnInput = document.getElementById('lrn');
    const addLrnWarning = document.getElementById('lrnWarning');
    addLrnInput.addEventListener('input', () => {
        addLrnWarning.classList.toggle('d-none', addLrnInput.value.length === 12 && /^\d{12}$/.test(addLrnInput.value));
    });

    const editLrnInput = document.getElementById('edit-lrn');
    const editLrnWarning = document.getElementById('editLrnWarning');
    editLrnInput.addEventListener('input', () => {
        editLrnWarning.classList.toggle('d-none', editLrnInput.value.length === 12 && /^\d{12}$/.test(editLrnInput.value));
    });
</script>

</body>
</html>
