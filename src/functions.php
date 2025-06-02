<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    $subject = 'Your Verification Code';
    $message = "<p>Your verification code is: <strong>$code</strong></p>";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    // Using MailHog SMTP
    ini_set("SMTP", "localhost");
    ini_set("smtp_port", "1025");

    return mail($email, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */

function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

    if (!in_array($email, $emails)) {
        return file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
    }
    return false;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return false;

    $emails = file($file, FILE_IGNORE_NEW_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== trim($email));

    return file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL, LOCK_EX) !== false;
}

function verifyCode(string $email, string $code): bool {
    return isset($_SESSION['verification'][$email]) && $_SESSION['verification'][$email] === $code;
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(): string {
    $randomId = random_int(1, 2800); // XKCD has ~2800+ comics
    $url = "https://xkcd.com/{$randomId}/info.0.json";

    $response = @file_get_contents($url);
    if (!$response) return "<p>Could not fetch comic.</p>";

    $data = json_decode($response, true);
    $img = htmlspecialchars($data['img']);
    $title = htmlspecialchars($data['title']);

    return "<h2>XKCD Comic</h2>
            <img src=\"$img\" alt=\"$title\">
            <p><a href=\"http://localhost/src/unsubscribe.php\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
}

/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $content = fetchAndFormatXKCDData();
    $subject = 'Your XKCD Comic';
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-type: text/html\r\n";

    foreach ($emails as $email) {
        mail($email, $subject, $content, $headers);
    }
}


function sendUnsubscribeVerificationEmail(string $email, string $code): bool {
    $subject = 'Confirm Un-subscription';
    $message = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-type: text/html\r\n";
    return mail($email, $subject, $message, $headers);
}
