<?php
include 'db_connect.php';

// Get current batch
$current_batch_query = "SELECT batch_number FROM current_batch ORDER BY current_batchID DESC LIMIT 1";
$current_batch_result = $conn->query($current_batch_query);
$current_batch = 1;
if ($current_batch_result->num_rows > 0) {
    $current_batch_row = $current_batch_result->fetch_assoc();
    $current_batch = $current_batch_row['batch_number'];
}

// Get total voters for this batch
$total_voters = 0;
$total_voters_query = "SELECT COUNT(DISTINCT student_id) AS total FROM votes WHERE batch = $current_batch";
$total_voters_result = $conn->query($total_voters_query);
if ($total_voters_result && $total_voters_result->num_rows > 0) {
    $total_voters = (int)$total_voters_result->fetch_assoc()['total'];
}

// Fetch total votes per candidate grouped by position
$query = "SELECT c.position, 
                 CONCAT(c.first_name, ' ', c.middle_name, ' ', c.last_name) AS full_name, 
                 c.candidateID,
                 COUNT(v.candidate_id) AS total_votes 
          FROM candidates c
          LEFT JOIN votes v ON c.candidateID = v.candidate_id
          WHERE c.batch = $current_batch
          GROUP BY c.position, c.candidateID, c.first_name, c.middle_name, c.last_name
          ORDER BY c.position, total_votes DESC";

$result = $conn->query($query);

$data = [];
$top_candidates = [];
$position_vote_counts = [];

// Process each candidate
while ($row = $result->fetch_assoc()) {
    $position = $row['position'];
    $candidate_id = $row['candidateID'];
    $full_name = $row['full_name'];
    $votes = (int)$row['total_votes'];

    if (!isset($data[$position])) {
        $data[$position] = ['candidates' => [], 'votes' => []];
        $top_candidates[$position] = [];
        $position_vote_counts[$position] = 0;
    }

    $data[$position]['candidates'][] = $full_name;
    $data[$position]['votes'][] = $votes;
    $position_vote_counts[$position] += $votes;

    $top_candidates[$position][] = [
        'full_name' => $full_name,
        'total_votes' => $votes
    ];
}

// Include "None" for skipped/non-voters per position
foreach ($data as $position => $value) {
    $non_voters = $total_voters - $position_vote_counts[$position];

    if ($non_voters > 0) {
        $data[$position]['candidates'][] = "None";
        $data[$position]['votes'][] = max(0, $non_voters);

        $none_candidate = [
            'full_name' => "None",
            'total_votes' => $non_voters
        ];

        $exists = false;
        foreach ($top_candidates[$position] as $tc) {
            if ($tc['full_name'] === "None") {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $top_candidates[$position][] = $none_candidate;
        }
    }

    usort($top_candidates[$position], function($a, $b) {
        return $b['total_votes'] <=> $a['total_votes'];
    });
    $top_candidates[$position] = array_slice($top_candidates[$position], 0, 3);
}

$positionsJSON = json_encode(array_keys($data));
$dataJSON = json_encode($data);
$topCandidatesJSON = json_encode($top_candidates);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Winners</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 250px;
            height: 250px;
            display: inline-block;
            margin: 20px;
            text-align: center;
        }
        canvas {
            width: 100% !important;
            height: 100% !important;
        }
        .navbar-nav .nav-link {
            color: white !important;
            transition: background-color 0.3s ease;
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
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="canprev.php">Candidate List</a></li>
                    <li class="nav-item"><a class="nav-link" href="voters.php">Voters List</a></li>
                    <li class="nav-item"><a class="nav-link" href="voted.php">Voters Status</a></li>
                    <li class="nav-item"><a class="nav-link active" href="winners.php">Candidate Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php">Log Out</a></li>
                </ul>
            </div>
        </nav>

        <div class="text-center bg-info text-white py-4">
            <h1 class="fs-4 fw-bold">SSS Voting System</h1>
        </div>
        <div class="text-center py-3 bg-primary text-white fw-bold fs-5">Admin Dashboard</div>
        <div class="text-center py-3 bg-info text-white fw-bold fs-5">Official Winners</div>
        <div class="text-center py-3 bg-light text-dark fw-bold fs-5">Current Batch: <?= $current_batch; ?></div>

        <div class="container text-center my-4">
            <h3 class="text-success">Election Results</h3>
            <div id="charts-container"></div>
            <div id="tables-container" class="mt-4"></div>
        </div>
    </div>

    <script>
        var positions = <?= $positionsJSON ?>;
        var data = <?= $dataJSON ?>;
        var topCandidates = <?= $topCandidatesJSON ?>;
        var totalVoters = <?= $total_voters ?>;

        var container = document.getElementById("charts-container");
        var tablesContainer = document.getElementById("tables-container");

        positions.forEach(function(position, index) {
            var chartContainer = document.createElement("div");
            chartContainer.className = "chart-container";
            chartContainer.innerHTML = `<h4>${position}</h4><canvas id="chart-${index}"></canvas>`;
            container.appendChild(chartContainer);

            var ctx = document.getElementById(`chart-${index}`).getContext("2d");

            var percentages = data[position]['votes'].map(voteCount => {
                return totalVoters > 0 ? ((voteCount / totalVoters) * 100).toFixed(2) : 0;
            });

            new Chart(ctx, {
                type: "pie",
                data: {
                    labels: data[position]['candidates'],
                    datasets: [{
                        data: percentages,
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4caf50', '#9966ff', '#c45850', '#3cba9f', '#e8c3b9', '#8e5ea2', '#3cba9f'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: "bottom",
                            labels: { font: { size: 10 } }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Table
            var tableHtml = `<div class="mt-4">
                                <h4 class="text-primary">Top Candidates for ${position}</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr><th>Candidate Name</th><th>Percentage</th></tr>
                                    </thead>
                                    <tbody>`;

            topCandidates[position].forEach(candidate => {
                let percentage = totalVoters > 0 ? ((candidate.total_votes / totalVoters) * 100).toFixed(2) : "0.00";
                tableHtml += `<tr><td>${candidate.full_name}</td><td>${percentage}%</td></tr>`;
            });

            tableHtml += `</tbody></table>
                          <p class="text-muted fst-italic small">
                            <strong>None</strong> represents students who did not vote or skipped voting for the position of <strong>${position}</strong>.
                          </p>
                          </div>`;
            tablesContainer.innerHTML += tableHtml;
        });

        document.addEventListener("DOMContentLoaded", function () {
            let currentPath = window.location.pathname.split("/").pop();
            document.querySelectorAll(".navbar-nav .nav-link").forEach(link => {
                if (link.getAttribute("href") === currentPath) {
                    link.classList.add("active");
                }
            });
        });
    </script>

    <footer class="text-center bg-info text-white py-3 mt-4">
        &copy; 2025 SSS Voting System
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
