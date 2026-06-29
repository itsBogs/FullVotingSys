<?php
include 'db_connect.php'; // Database connection

// Get the current batch from current_batch table
$currentBatchResult = $conn->query("SELECT batch_number FROM current_batch WHERE current_batchID = 1");
if ($currentBatchResult && $currentBatchResult->num_rows > 0) {
    $current_batch = intval($currentBatchResult->fetch_assoc()['batch_number']);
} else {
    die("Error fetching current batch");
}

// Fetch student vote statuses
$query = "
    SELECT s.student_ID, s.lrn, s.grade, s.section, s.last_name, s.first_name, s.middle_name,
           CASE WHEN COUNT(v.student_id) > 0 THEN 'Voted' ELSE 'Not Yet Voted' END AS vote_status
    FROM students s
    LEFT JOIN votes v ON s.student_ID = v.student_id
    WHERE s.batch = ?
    GROUP BY s.student_ID
    ORDER BY vote_status DESC, s.grade, s.section, s.last_name, s.first_name
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_batch);
$stmt->execute();
$result = $stmt->get_result();

$voted = [];
$not_voted = [];
while ($row = $result->fetch_assoc()) {
    if ($row['vote_status'] === 'Voted') {
        $voted[] = $row;
    } else {
        $not_voted[] = $row;
    }
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Voter Status</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Poppins', sans-serif; }
    .navbar-nav .nav-link { color: white !important; transition: .3s; }
    .navbar-nav .nav-link:hover { background: rgba(255,255,255,.2); border-radius:5px; }
    .navbar-nav .nav-link.active { font-weight:bold; background: rgba(0,0,0,.2); }
    .voted { background-color: #d4edda; }
    .not-voted { background-color: #f8d7da; }
  </style>
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid px-3">
      <img src="temp.png" width="80" class="rounded-circle me-3" alt="Logo">
      <a class="navbar-brand" href="admin_dashboard.php">Malacanang National Highschool</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <?php $current_page = basename($_SERVER['PHP_SELF']); ?>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link <?= $current_page=='admin_dashboard.php'?'active':''?>" href="admin_dashboard.php">Home</a></li>
          <li class="nav-item"><a class="nav-link <?= $current_page=='canprev.php'?'active':''?>" href="canprev.php">Candidate List</a></li>
          <li class="nav-item"><a class="nav-link <?= $current_page=='voters.php'?'active':''?>" href="voters.php">Voters List</a></li>
          <li class="nav-item"><a class="nav-link <?= $current_page=='voted.php'?'active':''?>" href="voted.php">Voter Status</a></li>
          <li class="nav-item"><a class="nav-link <?= $current_page=='winners.php'?'active':''?>" href="winners.php">Candidate Status</a></li>
          <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="text-center bg-info text-white py-4">
    <h1 class="fw-bold fs-4">SSS Voting System</h1>
  </div>
  <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Admin Dashboard</div>
  <div class="text-center py-3 bg-info text-white fw-bold fs-5">Voter Status</div>
  <div class="text-center py-3 bg-light text-dark fw-bold fs-5">Current Batch: <?= $current_batch ?></div>

  <div class="container my-4">
    <h3 class="text-center text-success">
      Voters Status - Voted
      <span class="badge bg-light text-dark"><?= count($voted) ?></span>
    </h3>
    <table class="table table-bordered table-striped mt-2">
      <thead class="table-dark text-center">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>LRN</th>
          <th>Grade</th>
          <th>Section</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($voted)): ?>
          <tr><td colspan="6" class="text-center text-danger fw-bold">No one has voted yet.</td></tr>
        <?php else: ?>
          <?php $i=1; foreach($voted as $r): ?>
            <tr class="voted">
              <td class="text-center"><?= $i++ ?></td>
              <td><?= htmlspecialchars("$r[last_name], $r[first_name] $r[middle_name]") ?></td>
              <td class="text-center"><?= $r['lrn'] ?></td>
              <td class="text-center"><?= $r['grade'] ?></td>
              <td class="text-center"><?= $r['section'] ?></td>
              <td class="text-center text-success fw-bold">
                <i class="fa-solid fa-check-circle"></i> Voted
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="container my-4">
    <h3 class="text-center text-danger">
      Voters Status - Not Yet Voted
      <span class="badge bg-light text-dark"><?= count($not_voted) ?></span>
    </h3>
    <table class="table table-bordered table-striped mt-2">
      <thead class="table-dark text-center">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>LRN</th>
          <th>Grade</th>
          <th>Section</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($not_voted)): ?>
          <tr><td colspan="6" class="text-center text-success fw-bold">Everyone has voted!</td></tr>
        <?php else: ?>
          <?php $i=1; foreach($not_voted as $r): ?>
            <tr class="not-voted">
              <td class="text-center"><?= $i++ ?></td>
              <td><?= htmlspecialchars("$r[last_name], $r[first_name] $r[middle_name]") ?></td>
              <td class="text-center"><?= $r['lrn'] ?></td>
              <td class="text-center"><?= $r['grade'] ?></td>
              <td class="text-center"><?= $r['section'] ?></td>
              <td class="text-center text-danger fw-bold">
                <i class="fa-solid fa-times-circle"></i> Not Yet Voted
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <footer class="text-center bg-info text-white py-3 mt-4">&copy; 2025 SSS Voting System</footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
