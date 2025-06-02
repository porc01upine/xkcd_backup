<?php
session_start();
require_once 'functions.php';

$email = $_POST['email'] ?? '';
$code = $_POST['verification_code'] ?? '';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($email) && empty($code)) {
        // Step 1: Send verification code
        $verificationCode = generateVerificationCode();
        $_SESSION['verification'][$email] = $verificationCode;
        if (sendVerificationEmail($email, $verificationCode)) {
            $success = "Verification code sent to $email.";
        } else {
            $error = "Failed to send verification email.";
        }
    } elseif (!empty($email) && !empty($code)) {
        // Step 2: Verify and register
        if (verifyCode($email, $code)) {
            if (registerEmail($email)) {
                $success = "Email verified and registered!";
                unset($_SESSION['verification'][$email]);
            } else {
                $error = "Email already registered.";
            }
        } else {
            $error = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>XKCD Email Subscription</title>
</head>
<body>
    <h1>Subscribe to Daily XKCD Comic</h1>

    <?php if ($success): ?>
        <p style="color: green;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Enter your email to subscribe:</label><br>
        <input type="email" name="email" required><br><br>

        <button type="submit" id="submit-email">Submit</button>
    </form>

    <hr>

    <form method="POST">
        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label for="verification_code">Enter verification code:</label><br>
        <input type="text" name="verification_code" maxlength="6" required><br><br>

        <button type="submit" id="submit-verification">Verify</button>
    </form>
</body>
</html>

