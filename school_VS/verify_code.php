<?php
session_start();

$message = "";
$code_valid = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify the code entered by the user
    if (isset($_POST['code']) && $_POST['code'] == $_SESSION['verification_code']) {
        $code_valid = true;
    } else {
        $message = "Invalid verification code!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-center">Enter Verification Code</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger text-center"><?= $message; ?></div>
        <?php endif; ?>

        <?php if ($code_valid): ?>
            <form method="POST" action="reset_password.php" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Enter New Password:</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        <?php else: ?>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Enter the verification code:</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify Code</button>
            </form>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
