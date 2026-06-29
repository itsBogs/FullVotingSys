<?php
session_start();
include 'db_connect.php';  // This includes your mysqli connection

$message = "";
$password_reset = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    // Get the new password and hash it
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $email = $_SESSION['email'];  // Get the email from session

    // Update password in the database
    if ($conn) {
        // Prepare the statement
        $stmt = $conn->prepare("UPDATE super_admins SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email); // "ss" indicates two string parameters

        // Execute the statement
        if ($stmt->execute()) {
            $password_reset = true;
            unset($_SESSION['verification_code']);  // Clear the verification code
            unset($_SESSION['email']);  // Clear the email

            // Redirect to login page after successful password reset
            header("Location: superadmin_login.php");
            exit();  // Make sure no further code is executed
        } else {
            $message = "Password update failed. Please try again.";
        }
    } else {
        $message = "Database connection failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-center">Reset Password</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger text-center"><?= $message; ?></div>
        <?php endif; ?>

        <?php if ($password_reset): ?>
            <div class="alert alert-success text-center">Password has been successfully reset!</div>
        <?php else: ?>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Enter New Password:</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
