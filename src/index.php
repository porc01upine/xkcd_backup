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
<meta charset="UTF-8" />
<title>XKCD Email Subscription</title>
<style>
  /* Basic Reset */
  * {
    box-sizing: border-box;
  }

  body {
    background: #f9f9f9;
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
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
  }

  h1 {
    margin-bottom: 1.5rem;
    color: #333;
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
    border-color: #0077cc;
    outline: none;
  }

  button {
    background: #0077cc;
    color: white;
    padding: 0.7rem 1.5rem;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
  }

  button:hover {
    background: #005fa3;
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
  <h1>Subscribe to Daily XKCD Comic</h1>

  <?php if ($success): ?>
    <p class="message success"><?= htmlspecialchars($success) ?></p>
  <?php endif; ?>

  <?php if ($error): ?>
    <p class="message error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <!-- Step 1: Request verification code -->
  <form method="POST" autocomplete="off" novalidate>
    <label for="email">Enter your email to subscribe:</label>
    <input type="email" id="email" name="email" required placeholder="your.email@example.com" value="<?= htmlspecialchars($email) ?>" />
    <button type="submit" id="submit-email">Send Verification Code</button>
  </form>

  <hr />

  <!-- Step 2: Verify and register -->
  <form method="POST" autocomplete="off" novalidate>
    <label for="email_verify">Email:</label>
    <input type="email" id="email_verify" name="email" required placeholder="your.email@example.com" value="<?= htmlspecialchars($email) ?>" />

    <label for="verification_code">Enter verification code:</label>
    <input type="text" id="verification_code" name="verification_code" maxlength="6" required placeholder="6-digit code" />

    <button type="submit" id="submit-verification">Verify & Register</button>
  </form>
</div>
</body>
</html>