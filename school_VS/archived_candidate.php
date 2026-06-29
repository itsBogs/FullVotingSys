<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Candidates</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
            <a class="navbar-brand fw-bold text-white" href="index.php">Back to Dashboard</a>
        </nav>

        <div class="text-center bg-info text-white py-4">
            <h1 class="fs-4 fw-bold">Archived Candidates</h1>
        </div>

        <div class="container my-4">
            <?php
            // Database Connection
            $conn = new mysqli("localhost:3306", "root", "", "voting_db");

            // Check Connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Fetch Archived Candidates Data
            $sql = "SELECT * FROM candidates WHERE status = 'archived'";
            $result = $conn->query($sql);
            ?>

            <table class="table table-striped table-bordered">
                <thead class="table-primary">
                    <tr class="text-center">
                        <th>LRN</th> <!-- Add LRN Column -->
                        <th>Name</th>
                        <th>Position</th>
                        <th>Grade</th>
                        <th>Section</th>
                        <th>Image</th>
                        <th>Actions</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= htmlspecialchars($row['lrn']); ?></td> <!-- Display LRN -->
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['position']); ?></td>
                            <td><?= htmlspecialchars($row['grade']); ?></td>
                            <td><?= htmlspecialchars($row['section']); ?></td>
                            <td class="text-center">
                                <?php if (!empty($row['image']) && file_exists("uploads/" . $row['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($row['image']); ?>" width="80" height="80">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <!-- Restore Button -->
                                <form action="restore_candidate.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <?php $conn->close(); ?>
        </div>
    </div>
</body>
</html>
