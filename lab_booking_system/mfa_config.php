<?php
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMFA($recipient, $code) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'stormie8work@gmail.com';
        $mail->Password = 'dbua nmht pobb hjgf';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('stormie8work@gmail.com', 'Lab Booking System');
        $mail->addAddress($recipient);
        $mail->Subject = 'Your MFA Code';
        $mail->Body = "Your one-time login code is: $code";

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "<h3>Mailer Error: " . $mail->ErrorInfo . "</h3>";
        return false;
    }
}
