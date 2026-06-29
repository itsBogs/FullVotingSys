<?php
session_start();
include 'db_connect.php';
include 'update_batch_history.php';

$batch_query = $conn->query("SELECT batch_number FROM current_batch WHERE current_batchID = 1");
$batch_row = $batch_query->fetch_assoc();
$current_batch = (int)$batch_row['batch_number'];

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$studentQuery = $conn->prepare("SELECT last_name, first_name, middle_name, batch FROM students WHERE student_ID = ?");
$studentQuery->bind_param("i", $student_id);
$studentQuery->execute();
$studentResult = $studentQuery->get_result()->fetch_assoc();

$last_name = htmlspecialchars($studentResult['last_name']);
$first_name = htmlspecialchars($studentResult['first_name']);
$middle_name = htmlspecialchars($studentResult['middle_name']);
$middle_initial = $middle_name ? strtoupper(substr($middle_name, 0, 1)) . '.' : '';
$student_name = "$last_name, $first_name $middle_initial";
$student_batch = (int)$studentResult['batch'];

if ($student_batch !== $current_batch) {
    echo "<script>
        alert('You are not part of the current batch and cannot vote.');
        window.location.href='index.php';
    </script>";
    exit();
}

$status_query = $conn->query("SELECT status FROM voting_status WHERE id = 1");
$status_row = $status_query->fetch_assoc();
$current_status = $status_row['status'];

if ($current_status == 'closed') {
    echo "<script>
        alert('Voting is currently closed. You will be redirected.');
        window.location.href='index.php';
    </script>";
    exit();
}

$checkVote = $conn->prepare("SELECT votes.candidate_id, votes.position,
                             CONCAT(candidates.last_name, ', ', candidates.first_name, ' ', LEFT(candidates.middle_name, 1), '.') AS name,
                             candidates.image
                             FROM votes 
                             LEFT JOIN candidates ON votes.candidate_id = candidates.candidateID
                             WHERE votes.student_id = ? AND (candidates.archived = 0 OR votes.candidate_id IS NULL)");
$checkVote->bind_param("i", $student_id);
$checkVote->execute();
$checkVoteResult = $checkVote->get_result();

$voted_candidates = [];
while ($voteRow = $checkVoteResult->fetch_assoc()) {
    $voted_candidates[] = [
        'position' => $voteRow['position'],
        'name' => $voteRow['candidate_id'] === null ? 'None' : $voteRow['name'],
        'image' => $voteRow['image'] ?? ''
    ];
}
$already_voted = !empty($voted_candidates);

// Fetch only positions with candidates in current batch
$positions_stmt = $conn->prepare("SELECT DISTINCT position FROM candidates WHERE batch = ? AND status = 'active' AND archived = 0");
$positions_stmt->bind_param("i", $current_batch);
$positions_stmt->execute();
$positions_result = $positions_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_voted) {
    $vote_stmt = $conn->prepare("INSERT INTO votes (student_id, candidate_id, position, batch) VALUES (?, ?, ?, ?)");

    $positions_stmt->execute(); // Re-execute to reuse
    $all_positions = $positions_stmt->get_result();

    while ($position_row = $all_positions->fetch_assoc()) {
        $position_name = $position_row['position'];

        if (empty($_POST['votes'][$position_name])) {
            $null_candidate_id = null;
            $vote_stmt->bind_param("issi", $student_id, $null_candidate_id, $position_name, $current_batch);
            $vote_stmt->execute();

            $voted_candidates[] = [
                'position' => $position_name,
                'name' => 'None',
                'image' => ''
            ];
            continue;
        }

        $candidate_id = $_POST['votes'][$position_name][0];
        $vote_stmt->bind_param("issi", $student_id, $candidate_id, $position_name, $current_batch);
        $vote_stmt->execute();

        $candidateQuery = $conn->prepare("SELECT last_name, first_name, middle_name, image FROM candidates WHERE candidateID = ?");
        $candidateQuery->bind_param("i", $candidate_id);
        $candidateQuery->execute();
        $candidateResult = $candidateQuery->get_result()->fetch_assoc();

        $full_name = $candidateResult['last_name'] . ', ' . $candidateResult['first_name'] . ' ' . strtoupper(substr($candidateResult['middle_name'], 0, 1)) . '.';

        $voted_candidates[] = [
            'position' => $position_name,
            'name' => $full_name,
            'image' => $candidateResult['image']
        ];
    }

    $updateVoteStatus = $conn->prepare("UPDATE students SET voted = 1 WHERE student_ID = ?");
    $updateVoteStatus->bind_param("i", $student_id);
    $updateVoteStatus->execute();

    $conn->close();

    $already_voted = true;

    echo "<script>
        alert('Your vote has been successfully submitted. You will be redirected.');
        window.location.href='index.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SSG Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { height: 100%; }
        .wrapper { min-height: 100%; display: flex; flex-direction: column; }
        .content { flex: 1; }
        footer { background-color: #17a2b8; color: white; text-align: center; padding: 10px; }
    </style>
</head>
<body class="bg-light">
<div class="wrapper">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3">
        <img src="temp.png" alt="School Logo" class="rounded-circle mt-2" width="80" style="margin-right: 20px;">
        <a class="navbar-brand fw-bold text-white" href="index.php">Malacanang National Highschool</a>
    </nav>

    <div class="text-center bg-info text-white py-4">
        <h1 class="fs-4 fw-bold">SSG Voting System</h1>
    </div>

    <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Voter Dashboard - Batch <?= $current_batch ?></div>

    <div class="container my-4 content d-flex justify-content-center">
        <div class="col-md-8">
            <h3 class="text-center">Vote for Your Preferred Candidates</h3>
            <p class="text-center fw-bold">Welcome, <?= htmlspecialchars($student_name) ?></p>

            <?php if ($already_voted): ?>
                <div class="alert alert-warning text-center fw-bold">You have already voted! Here is your vote summary:</div>
                <div class="table-responsive">
                    <table class="table table-bordered text-center mx-auto" style="max-width: 500px;">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Position</th>
                                <th>Candidate Voted</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($voted_candidates as $vote): ?>
                                <tr>
                                    <td><?= htmlspecialchars($vote['position']) ?></td>
                                    <td><?= htmlspecialchars($vote['name']) ?></td>
                                    <td>
                                        <?php if (!empty($vote['image']) && file_exists("uploads/" . $vote['image'])): ?>
                                            <img src="uploads/<?= htmlspecialchars($vote['image']) ?>" width="60" height="60" class="rounded-circle">
                                        <?php elseif ($vote['name'] === 'None'): ?>
                                            <span class="text-muted">No Vote</span>
                                        <?php else: ?>
                                            <span class="text-muted">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <script>setTimeout(() => window.location.href = "index.php", 5000);</script>
            <?php else: ?>
                <form method="POST" class="text-center mx-auto" style="max-width: 500px;">
                    <?php
                    $positions_result->data_seek(0); // Reset pointer for reuse
                    while ($position = $positions_result->fetch_assoc()):
                        $position_name = htmlspecialchars($position['position']);
                    ?>
                    <h5 class="mt-3"><?= $position_name ?></h5>
                    <div class="btn-group-vertical w-100" role="group">
                        <?php
                        $candidateStmt = $conn->prepare("SELECT candidateID, 
                            CONCAT(last_name, ', ', first_name, ' ', LEFT(middle_name, 1), '.') AS name, 
                            image 
                            FROM candidates 
                            WHERE position = ? 
                            AND batch = ? 
                            AND status = 'active' 
                            AND archived = 0");
                        $candidateStmt->bind_param("si", $position_name, $current_batch);
                        $candidateStmt->execute();
                        $candidatesResult = $candidateStmt->get_result();

                        while ($candidate = $candidatesResult->fetch_assoc()): ?>
                        <input type="checkbox" class="btn-check candidate-option" name="votes[<?= $position_name ?>][]" id="candidate<?= $candidate['candidateID'] ?>" value="<?= $candidate['candidateID'] ?>" data-position="<?= $position_name ?>">
                        <label class="btn btn-outline-primary w-100 mb-3 py-3 d-flex align-items-center justify-content-start" for="candidate<?= $candidate['candidateID'] ?>" style="cursor: pointer; font-size: 16px; border-radius: 8px;">
                            <?php if (!empty($candidate['image']) && file_exists("uploads/" . $candidate['image'])): ?>
                                <img src="uploads/<?= htmlspecialchars($candidate['image']) ?>" width="60" height="60" class="rounded-circle me-3">
                            <?php else: ?>
                                <span class="text-muted">No Image</span>
                            <?php endif; ?>
                            <span class="ms-3"><?= htmlspecialchars($candidate['name']) ?></span>
                        </label>
                        <?php endwhile; $candidateStmt->close(); ?>
                    </div>
                    <?php endwhile; ?>

                    <button type="submit" class="btn btn-success mt-3 w-100 py-3" style="font-size: 18px; border-radius: 8px;">Submit Vote</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <footer>&copy; 2025 SSG Voting System | All Rights Reserved</footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle-like behavior for checkboxes (act like radio, but uncheckable)
document.querySelectorAll('.candidate-option').forEach(input => {
    input.addEventListener('click', function () {
        const position = this.dataset.position;
        const group = document.querySelectorAll(`input[data-position='${position}']`);

        if (this.checked) {
            group.forEach(box => {
                if (box !== this) box.checked = false;
            });
        }
    });
});
</script>
</body>
</html>
