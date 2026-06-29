<?php
include 'db_connect.php';
session_start();

if (!isset($_GET['id'])) {
    header("Location: super_admin_dashboard.php");
    exit();
}

$id = $_GET['id'];
$section_result = $conn->query("SELECT * FROM sections WHERE sectionID = $id");
$section = $section_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade = $_POST['grade'];
    $section_name = $_POST['section'];

   $stmt = $conn->prepare("UPDATE sections SET grade = ?, section = ? WHERE sectionID = ?");
$stmt->bind_param("isi", $grade, $section_name, $id);

    $stmt->execute();
    $stmt->close();

    // Log transaction
    $adminUsername = $_SESSION['admin_username'] ?? 'Unknown';
    $action = "Edited section: Grade $grade - $section_name";
    $stmt = $conn->prepare("INSERT INTO transaction_history (admin_username, action) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminUsername, $action);
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
    <title>Edit Section</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4">
            <h3>Edit Section</h3>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Grade:</label>
                    <select name="grade" class="form-select" required>
                        <?php for ($i = 7; $i <= 12; $i++) { ?>
                            <option value="<?php echo $i; ?>" <?= ($section['grade'] == $i) ? 'selected' : '' ?>>Grade <?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Section Name:</label>
                    <input type="text" name="section" class="form-control" value="<?php echo htmlspecialchars($section['section']); ?>" required>
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
                <a href="super_admin_dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
