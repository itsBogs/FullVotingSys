<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: super_admin_dashboard.php");
    exit();
}

$id = $_GET['id'];

// Fetch admin details
$stmt = $conn->prepare("SELECT username FROM admins WHERE admin_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

// Handle admin update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    if ($password) {
        $stmt = $conn->prepare("UPDATE admins SET username = ?, password = ? WHERE admin_ID = ?");
        $stmt->bind_param("ssi", $username, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $username, $id);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: super_admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
        }

        .container {
            max-width: 500px;
            margin-top: 50px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 12px 20px;
            width: 100%;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            padding: 12px 20px;
            width: 100%;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        .form-label {
            font-weight: 600;
            font-size: 1rem;
            color: #333;
        }

        .form-control {
            border-radius: 5px;
            box-shadow: none;
            padding: 12px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .page-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            color: #007bff;
        }

        .footer {
            background-color: #17a2b8;
            color: white;
            padding: 15px 0;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="page-title">Edit Admin</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password (Optional):</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="super_admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <div class="footer">
        &copy; 2025 SSS Voting System
    </div>
</body>
</html>

