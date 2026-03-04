<?php
// mailer.php - PHPMailer SMTP helper
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

function sendMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // SMTP config
        $mail->isSMTP();
        $mail->Host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USER') ?: 'josephmanizabayo7@gmail.com'; 
        $mail->Password = getenv('SMTP_PASS') ?: 'xibc jqdf xapm ausl';         
        $mail->SMTPSecure = getenv('SMTP_SECURE') ?: 'ssl';                       
        $mail->Port = getenv('SMTP_PORT') ?: 465;                               

        $mail->setFrom(getenv('SMTP_FROM_EMAIL') ?: 'josephmanizabayo7@gmail.com', getenv('SMTP_FROM_NAME') ?: 'Quincaillerie Manager');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);
        echo 'Mailer Error: ' . $mail->ErrorInfo; // Show error on screen for debugging
        return false;
    }
}
