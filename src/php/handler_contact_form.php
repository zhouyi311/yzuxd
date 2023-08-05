<?php
session_start();

$to = 'xiaosad311@gmail.com';
$from = 'hello@yzuxd.com';
$subject = 'User message form yzuxd.com';

function alert($message, $type = 'primary')
{
    $message = htmlspecialchars($message);

    // Define the valid alert types
    $valid_alert_types = [
        'primary',
        'secondary',
        'success',
        'danger',
        'warning',
        'info',
        'light',
        'dark',
    ];

    // Check if the provided type is valid, if not, default to 'primary'
    $type = in_array($type, $valid_alert_types) ? $type : 'primary';

    echo "<div class='alert alert-$type alert-dismissible border-0' role='alert'>";
    echo "<div>".$message."</div>";
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">';
    echo "</div>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromEmail = $_POST['fromEmail'];
    $message = $_POST['message'];

    // Prevent sending more than one email per minute
    if (isset($_SESSION['last_email_sent_at']) && time() - $_SESSION['last_email_sent_at'] < 60) {
        alert('You can send one message per minute. Please wait a bit.', 'danger');
        exit();
    }

    // Validate email
    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        alert('Invalid email format', 'danger');
        exit();
    }

    // Check if message is empty
    if (trim($message) == '') {
        alert('Message cannot be emptyt', 'danger');
        exit();
    }

    $fullMessage = "From: $fromEmail\n\n$message";
    $headers = "From: " . $from . "\r\n" .
                "Reply-To: " . $from . "\r\n" .
                "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $fullMessage, $headers)) {
        $_SESSION['last_email_sent_at'] = time();
        alert('Email sent successfully', 'info');
    } else {
        alert('Oops, service error, please try again later.', 'danger');
    }
}
?>
