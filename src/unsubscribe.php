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
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Unsubscribe from XKCD Comics</title>
<style>
  /* Basic Reset */
  * {
    box-sizing: border-box;
  }

  body {
    background: #f7f7f7;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .container {
    background: white;
    padding: 2rem 3rem;
    max-width: 400px;
    width: 90%;
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    text-align: center;
  }

  h2 {
    color: #333;
    margin-bottom: 1.5rem;
  }

  form {
    margin-bottom: 2rem;
  }

  label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #555;
    text-align: left;
  }

  input[type="email"],
  input[type="text"] {
    width: 100%;
    padding: 0.5rem 0.75rem;
    margin-bottom: 1rem;
    border: 1.8px solid #ccc;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.2s;
  }

  input[type="email"]:focus,
  input[type="text"]:focus {
    border-color: #c62828;
    outline: none;
  }

  button {
    background: #c62828;
    color: white;
    padding: 0.7rem 1.5rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
    width: 100%;
  }

  button:hover {
    background: #a81f1f;
  }

  .message {
    margin-bottom: 1.5rem;
    font-weight: 600;
  }

  .success {
    color: #2e7d32;
  }

  .error {
    color: #d32f2f;
  }

  hr {
    border: none;
    border-top: 1px solid #eee;
    margin: 2rem 0;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Unsubscribe from XKCD Comics</h2>

    <?php if ($message): ?>
      <p class="message <?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
      </p>
    <?php endif; ?>

    <!-- Step 1: Enter email -->
    <form method="POST" autocomplete="off" novalidate>
      <label for="unsubscribe_email">Enter your email:</label>
      <input type="email" id="unsubscribe_email" name="unsubscribe_email" required placeholder="your.email@example.com" />
      <button id="submit-unsubscribe">Send Verification Code</button>
    </form>

    <hr />

    <!-- Step 2: Enter verification code -->
    <form method="POST" autocomplete="off" novalidate>
      <label for="verification_code">Enter Verification Code:</label>
      <input type="text" id="verification_code" name="verification_code" maxlength="6" required placeholder="6-digit code" />
      <button id="submit-verification">Verify & Unsubscribe</button>
    </form>
  </div>
</body>
</html>