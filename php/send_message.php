<?php
/**
 * Receives a message from the Contact Us form and saves it to the database.
 * Responds with JSON because the form is sent in the background by script.js.
 */

declare(strict_types=1);

require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['success' => false, 'message' => 'Method not allowed.']);
}

// ---------- Read and validate ----------
$name    = cleanText($_POST['name'] ?? '', 100);
$email   = cleanText($_POST['email'] ?? '', 150);
$phone   = cleanText($_POST['phone'] ?? '', 30);
$subject = cleanText($_POST['subject'] ?? 'general', 50);
$message = cleanText($_POST['message'] ?? '', 2000);

$allowedSubjects = ['general', 'order', 'catering', 'feedback'];

$errors = [];

if (mb_strlen($name) < 2) {
    $errors[] = 'Please enter your name.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($phone !== '' && !preg_match('/^[0-9+\s()\-]{7,30}$/', $phone)) {
    $errors[] = 'Please enter a valid phone number or leave it blank.';
}

if (!in_array($subject, $allowedSubjects, true)) {
    $subject = 'general';
}

if (mb_strlen($message) < 5) {
    $errors[] = 'Please enter a message.';
}

if (!empty($errors)) {
    jsonResponse(400, ['success' => false, 'message' => implode(' ', $errors)]);
}

// ---------- Save to database ----------
try {
    $stmt = getDb()->prepare(
        'INSERT INTO messages (name, email, phone, subject, message)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $name,
        $email,
        $phone !== '' ? $phone : null,
        $subject,
        $message,
    ]);

    jsonResponse(200, [
        'success' => true,
        'message' => 'Thank you! Your message has been sent.',
    ]);

} catch (Throwable $error) {
    error_log('Message save failed: ' . $error->getMessage());
    jsonResponse(500, [
        'success' => false,
        'message' => 'Sorry, something went wrong. Please call or WhatsApp us instead.',
    ]);
}
