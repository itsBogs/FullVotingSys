<?php
session_start();
include 'db_connect.php'; // Ensure the connection is set up

// Include PHPMailer
require_once 'PHPMailer-master\src\Exception.php';
require_once 'PHPMailer-master\src\PHPMailer.php';
require_once 'PHPMailer-master\src\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$code_sent = false;
$verification_code = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the form
    $email = $_POST['email'];  // Make sure to add an email field in your form

    // Check if the email exists in the super_admins table
    $stmt = $conn->prepare("SELECT * FROM super_admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Email not found in the database
        $message = "Email not found. Please try again.";
    } else {
        // Store email in session for later use
        $_SESSION['email'] = $email;

        // Generate a verification code
        $verification_code = rand(100000, 999999);  // 6-digit random code
        $_SESSION['verification_code'] = $verification_code;  // Store the code in session

        // Send the code to the email address using PHPMailer
        $mail = new PHPMailer(true);  // Create an instance of PHPMailer

        try {
            // Server settings
            $mail->isSMTP();                                        // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';                         // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                                 // Enable SMTP authentication
            $mail->Username = 'systemssgvoting@gmail.com';          // Your Gmail address
            $mail->Password = 'fwdu crmu khze ymki';     // Use the App Password here
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;     // Enable TLS encryption
            $mail->Port = 587;                                      // TCP port to connect to

            // Recipients
            $mail->setFrom('systemssgvoting@gmail.com', 'SSS Voting System');
            $mail->addAddress($email);                              // Add a recipient

            // Content
            $mail->isHTML(true);                                    // Set email format to HTML
            $mail->Subject = 'Password Reset Code';
            $mail->Body    = "Your password reset code is: $verification_code";

            // Send the email
            $mail->send();

            $code_sent = true;
        } catch (Exception $e) {
            $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<!-- HTML form (request_reset.php) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h3 class="text-center">Forgot Password</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger text-center"><?= $message; ?></div>
        <?php endif; ?>

        <?php if (!$code_sent): ?>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Enter your email address:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send Verification Code</button>
            </form>
        <?php else: ?>
            <div class="alert alert-success text-center">A verification code has been sent to your email!</div>
            <form method="POST" action="reset_password.php" class="mt-3">
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
