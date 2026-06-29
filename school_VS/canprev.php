<?php
// Connect to the database
$conn = new mysqli("localhost:3306", "root", "", "voting_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current batch
$current_batch_query = "SELECT batch_number FROM current_batch ORDER BY current_batchID DESC LIMIT 1";
$current_batch_result = $conn->query($current_batch_query);
if ($current_batch_result->num_rows > 0) {
    $current_batch_row = $current_batch_result->fetch_assoc();
    $current_batch = $current_batch_row['batch_number']; // Get the batch number
} else {
    $current_batch = 1; // Default to batch 1 if nothing is found
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        /* Custom styling for the page */
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
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container-fluid {
            flex: 1;
        }
        footer {
            width: 100%;
            text-align: center;
            background-color: #17a2b8;
            color: white;
            padding: 10px;
            position: relative;
            bottom: 0;
            margin-top: auto;
            left: 0;
        }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid px-0">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
            <img src="temp.png" alt="School Logo" class="rounded-circle mt-2" width="80" style="margin-left: 20px; margin-right: 20px;">
            <a class="navbar-brand fw-bold text-white" href="admin_dashboard.php">Malacanang National Highschool</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link " href="admin_dashboard.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="canprev.php">Candidate List</a></li>
                        <li class="nav-item"><a class="nav-link" href="voters.php">Voters List</a></li>
                        <li class="nav-item"><a class="nav-link" href="voted.php">Voters Status</a></li>
                        <li class="nav-item"><a class="nav-link" href="winners.php">Candidate Status</a></li>
                        <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
                    </ul>
                </div>
        </nav>

        <div class="text-center bg-info text-white py-4">
            <h1 class="fs-4 fw-bold" style="padding-top: 10px;">SSS Voting System</h1>
        </div>
        <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Admin Dashboard</div>
        <div class="text-center py-3 bg-info text-white fw-bold fs-5">Candidate List</div>

        <!-- Dlay Current Batch below Candidate List -->
        <div class="text-center py-3 bg-light text-dark fw-bold fs-5">
            Current Batch: <?= $current_batch; ?>
        </div>

        <div class="container my-4">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#archivedModal">
                    <i class="fas fa-archive"></i> View Archived Candidates
                </button>
            </div>

            <?php
                // Your code to fetch and display the active candidates
                $conn = new mysqli("localhost:3306", "root", "", "voting_db");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT * FROM candidates WHERE status = 'active' AND batch = $current_batch ORDER BY position, last_name, first_name";
                $result = $conn->query($sql);
                $candidates_by_position = [];
                while ($row = $result->fetch_assoc()) {
                    $candidates_by_position[$row['position']][] = $row;
                }
            ?>

            <?php foreach ($candidates_by_position as $position => $candidates): ?>
                <table class="table table-striped table-bordered">
                    <thead class="table-primary">
                        <tr>
                            <th colspan="7" class="text-center bg-primary text-white p-2"><?= htmlspecialchars($position); ?></th>
                        </tr>
                        <tr class="text-center">
                            <th>Name</th>
                            <th>LRN</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>General Average</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidates as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['last_name']) . ", " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['middle_name']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['lrn']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['grade']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['section']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['general_average']); ?></td>
                                <td class="text-center">
                                    <?php if (!empty($row['image']) && file_exists("uploads/" . $row['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['image']); ?>" width="80" height="80" class="rounded">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $row['candidateID']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="archive_candidate.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $row['candidateID']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-archive"></i> Archive
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        </div>
    </div>
    <footer class="text-center bg-info text-white py-3 mt-4">&copy; 2025 SSS Voting System</footer>


<?php $conn->close(); ?>


        <!-- Bootstrap Modal for Archived Candidates -->
        <!-- Modal for Archived Candidates -->
<div class="modal fade" id="archivedModal" tabindex="-1" aria-labelledby="archivedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archivedModalLabel">Archived Candidates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">  
                <?php
                $conn = new mysqli("localhost:3306", "root", "", "voting_db");
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = "SELECT * FROM candidates WHERE status = 'archived' AND batch = $current_batch";
                $result = $conn->query($sql);
                ?>

                <table class="table table-striped table-bordered">
                    <thead class="table-secondary">
                        <tr class="text-center">
                            <th>Name</th>
                            <th>LRN</th> <!-- LRN column -->
                            <th>Position</th>
                            <th>Grade</th>
                            <th>Section</th>
                            <th>General Average</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['last_name']) . ", " . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['middle_name']); ?></td>
                                <td><?= htmlspecialchars($row['lrn']); ?></td> <!-- Display LRN -->
                                <td><?= htmlspecialchars($row['position']); ?></td>
                                <td><?= htmlspecialchars($row['grade']); ?></td>
                                <td><?= htmlspecialchars($row['section']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['general_average']); ?></td>
                                <td class="text-center">
                                    <?php if (!empty($row['image']) && file_exists("uploads/" . $row['image'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['image']); ?>" width="60" height="60" class="rounded">
                                    <?php else: ?>
                                        <span class="text-muted">No Image</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="restore_candidate.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['candidateID']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php $conn->close(); ?>
            </div>
        </div>
    </div>
</div>


<!-- Edit Candidate Modal -->
<!-- Edit Candidate Modal -->
<div class="modal fade" id="editCandidateModal" tabindex="-1" aria-labelledby="editCandidateLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editCandidateForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_id">

                    <!-- LRN Input -->
                    <div class="mb-3">
                        <label class="form-label">LRN</label>
                        <input type="text" class="form-control" name="lrn" id="edit_lrn" disabled >
                    </div>

                    <!-- Last Name, First Name, Middle Name Inputs -->
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Middle Name</label>
                        <input type="text" class="form-control" name="middle_name" id="edit_middle_name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <select class="form-select" name="grade" id="edit_grade" required>
                            <option value="" disabled selected>Select Grade</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                            <option value="10">Grade 10</option>
                            <option value="11">Grade 11</option>
                            <option value="12">Grade 12</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Section:</label>
                        <select class="form-control" name="section" id="edit_section" required>
                            <option value="">Select Section</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">General Average</label>
                        <input type="number" step="0.01" class="form-control" name="general_average" id="edit_general_average" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select" id="edit_position" required>
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
                        <label class="form-label">Image</label>
                        <input type="file" class="form-control" name="image">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Open Edit Candidate Modal
const modalElement = document.getElementById('editCandidateModal');
const modal = new bootstrap.Modal(modalElement);

// Listen for modal close event
modalElement.addEventListener('hidden.bs.modal', function () {
    // Ensure proper backdrop removal
    const backdrop = document.querySelector('.modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
    document.body.classList.remove('modal-open'); // Remove the modal-open class
});

// Attach event listeners to Edit buttons
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        
        // Fetch candidate data for editing
        fetch(`get_candidate.php?id=${id}`)
            .then(response => response.json())
            .then(data => {
                // Populate the modal form with the candidate data
                document.getElementById('edit_id').value = data.id;
                document.getElementById('edit_last_name').value = data.last_name;
                document.getElementById('edit_first_name').value = data.first_name;
                document.getElementById('edit_middle_name').value = data.middle_name;
                document.getElementById('edit_grade').value = data.grade;
                document.getElementById('edit_section').value = data.section;

                // Trigger the change event for grade to load sections
                document.getElementById('edit_grade').dispatchEvent(new Event('change'));

                // Show the modal
                modal.show();
            })
            .catch(error => console.error('Error fetching candidate data:', error)); // Optional: Error handling
    });
});

// Event listener for when grade is selected
document.getElementById('edit_grade').addEventListener('change', function() {
    const grade = this.value; // Get selected grade

    // Make AJAX call to fetch sections for the selected grade
    fetch(`get_sections.php?grade=${grade}`)
        .then(response => response.json())
        .then(data => {
            const sectionDropdown = document.getElementById('edit_section');
            sectionDropdown.innerHTML = '<option value="" disabled selected>Select Section</option>'; // Reset sections

            // Populate sections based on the selected grade
            data.forEach(section => {
                const option = document.createElement('option');
                option.value = section;
                option.textContent = section;
                sectionDropdown.appendChild(option);
            });
        })
        .catch(error => console.error('Error fetching sections:', error)); // Optional: Error handling
});


</script>

<script>
    $(document).ready(function() {
        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            $.ajax({
                url: 'get_candidate.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                    $('#edit_id').val(response.candidateID);
                    $('#edit_lrn').val(response.lrn);
                    $('#edit_last_name').val(response.last_name);
                    $('#edit_first_name').val(response.first_name);
                    $('#edit_middle_name').val(response.middle_name);
                    $('#edit_grade').val(response.grade);
                    $('#edit_section').val(response.section);
                    $('#edit_position').val(response.position);
                    $('#edit_general_average').val(response.general_average);

                    $('#editCandidateModal').modal('show');
                },
                error: function() {
                    alert('Failed to fetch candidate data.');
                }
            });
        });

        // Submit edit form
        $('#editCandidateForm').submit(function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            $.ajax({
                url: 'update_candidate.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    alert('Candidate updated successfully!');
                    location.reload();
                },
                error: function() {
                    alert('Failed to update candidate.');
                }
            });
        });
    });
</script>

</body>
</html>
