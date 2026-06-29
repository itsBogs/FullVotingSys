<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['voter_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch only active candidates
$sql = "SELECT * FROM candidates WHERE status = 'active'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Now</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Vote for Your Preferred Candidates</h2>
    <form action="submit_vote.php" method="post">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <label>
                <input type="radio" name="candidate_id" value="<?= $row['id']; ?>" required>
                <?= htmlspecialchars($row['name']) . " - " . htmlspecialchars($row['position']); ?>
            </label><br>
        <?php } ?>
        <button type="submit">Submit Vote</button>
    </form>
</body>
</html>
