<?php
require_once 'functions.php';


session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Step 1: User submits email to start unsubscribe process
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = $code;

            if (sendUnsubscribeVerificationEmail($email, $code)) {
                $message = "A verification code has been sent to your email.";
            } else {
                $message = "Failed to send email. Please try again.";
            }
        } else {
            $message = "Invalid email address.";
        }

    // Step 2: User submits the code to confirm unsubscription
    } elseif (isset($_POST['verification_code'])) {
        $code = trim($_POST['verification_code']);
        $sessionEmail = $_SESSION['unsubscribe_email'] ?? '';
        $sessionCode = $_SESSION['unsubscribe_code'] ?? '';

        if ($code === $sessionCode) {
            if (unsubscribeEmail($sessionEmail)) {
                $message = "You have been unsubscribed successfully.";
                unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
            } else {
                $message = "Unsubscription failed. Email not found.";
            }
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe</title>
</head>
<body>
    <h2>Unsubscribe from XKCD Comics</h2>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="unsubscribe_email">Email:</label><br>
        <input type="email" name="unsubscribe_email" required><br><br>
        <button id="submit-unsubscribe">Unsubscribe</button>
    </form>

    <hr>

    <form method="POST">
        <label for="verification_code">Enter Verification Code:</label><br>
        <input type="text" name="verification_code" maxlength="6" required><br><br>
        <button id="submit-verification">Verify</button>
    </form>
</body>
</html>
