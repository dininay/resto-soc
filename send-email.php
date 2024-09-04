<?php

// Set timezone to Jakarta
date_default_timezone_set('Asia/Jakarta');

// Include PHPMailer library files manually
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-6.8.1/src/Exception.php';
require 'PHPMailer-6.8.1/src/PHPMailer.php';
require 'PHPMailer-6.8.1/src/SMTP.php';

// Initialize PHPMailer
$mail = new PHPMailer(true);
function sendEmail() {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';  // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'ff811f556f5d12'; // Replace with your username
        $mail->Password = 'c60c92868ce0f8'; // Replace with your password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        // Recipients
        $mail->setFrom('from@example.com', 'Mailer');
        $mail->addAddress('dininaylulizzah248@gmail.com', 'Recipient Name');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Daily Report';
        $mail->Body    = 'This is the daily report sent at 4:00 PM Jakarta time.';

        // Send email
        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Calculate the time until the next 4:00 PM
function getSecondsUntilNextSchedule($hour = 16, $minute = 0) {
    $now = new DateTime();
    $nextSchedule = new DateTime();
    $nextSchedule->setTime($hour, $minute);
    
    if ($now > $nextSchedule) {
        // If it's already past the scheduled time today, schedule for the next day
        $nextSchedule->modify('+1 day');
    }
    
    return $nextSchedule->getTimestamp() - $now->getTimestamp();
}

// Main loop
while (true) {
    $interval = getSecondsUntilNextSchedule(); // Get the seconds until the next scheduled time
    sleep($interval); // Wait until the next scheduled time
    sendEmail(); // Send the email
}
?>