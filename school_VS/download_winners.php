<?php
require('fpdf/fpdf186/fpdf.php');
include 'db_connect.php';

// Get current batch number
$batchQuery = "SELECT batch_number FROM current_batch LIMIT 1";
$batchResult = $conn->query($batchQuery);

if ($batchResult && $batchResult->num_rows > 0) {
    $batchRow = $batchResult->fetch_assoc();
    $current_batch = (int)$batchRow['batch_number'];
} else {
    $current_batch = 1;
}

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Election Winners', 0, 1, 'C');
        $this->Ln(5);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Query to get candidates for current batch
$query = "SELECT 
            c.position, 
            CONCAT_WS(' ', c.first_name, c.middle_name, c.last_name) AS name,
            c.grade, 
            c.section, 
            c.general_average, 
            c.image, 
            COUNT(v.candidate_id) AS total_votes 
          FROM candidates c
          LEFT JOIN votes v ON c.candidateID = v.candidate_id
          WHERE c.status = 'active' AND c.batch = ?
          GROUP BY c.position, c.candidateID
          ORDER BY c.position, total_votes DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_batch);
$stmt->execute();
$result = $stmt->get_result();

// Prepare winners array
$winners = [];
while ($row = $result->fetch_assoc()) {
    $position = $row['position'];
    if (!isset($winners[$position])) {
        $winners[$position] = [];
    }
    if (count($winners[$position]) < 3) {
        $winners[$position][] = $row;
    }
}

// Display batch number at top
$pdf->Cell(0, 10, "Batch: " . $current_batch, 0, 1, 'C');

foreach ($winners as $position => $candidates) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, $position, 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);

    // Find max votes for this position to highlight winner(s)
    $max_votes = max(array_column($candidates, 'total_votes'));

    foreach ($candidates as $candidate) {
        $imagePath = 'uploads/' . $candidate['image'];

        // Check image type to skip webp
        if (pathinfo($imagePath, PATHINFO_EXTENSION) == 'webp') {
            continue;
        }

        // Highlight winner by bold font and add "WINNER" label
        $is_winner = ($candidate['total_votes'] == $max_votes);

        if ($is_winner) {
            $pdf->SetFont('Arial', 'B', 11);
        } else {
            $pdf->SetFont('Arial', '', 10);
        }

        // Show image if exists
        if (!empty($candidate['image']) && file_exists($imagePath)) {
            $pdf->Image($imagePath, 90, $pdf->GetY(), 30);
            $pdf->Ln(35);
        }

        // Print candidate info, add "WINNER" for the highest votes
        $winnerText = $is_winner ? "  <-- WINNER" : "";
        $pdf->Cell(0, 10, "Name: " . $candidate['name'] . $winnerText, 0, 1, 'C');
        $pdf->Cell(0, 10, "Grade: " . $candidate['grade'] . " - Section: " . $candidate['section'], 0, 1, 'C');
        $pdf->Cell(0, 10, "General Average: " . $candidate['general_average'], 0, 1, 'C');
        $pdf->Cell(0, 10, "Total Votes: " . $candidate['total_votes'], 0, 1, 'C');
        $pdf->Ln(5);

        // Reset font after each candidate
        $pdf->SetFont('Arial', '', 10);
    }
}

$pdf->Output('D', 'Winners_List.pdf');
