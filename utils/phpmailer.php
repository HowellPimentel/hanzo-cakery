<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require '../vendor/autoload.php';
require 'load_env.php';

/**
 * Sends an email using PHPMailer
 * 
 * @param string $email The recipient's email address
 * @param string $firstname The recipient's first name
 * @param string $lastname The recipient's last name
 * @param string $subject The email subject
 * @param string $body The HTML email body
 * @param string $alt The plain text alternative body
 * @return bool Returns true if email was sent successfully, false otherwise
 */
function send_mail($email, $firstname, $lastname, $subject, $body, $alt)
{

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['APP_EMAIL'];
        $mail->Password = $_ENV['APP_PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        //Recipients
        $mail->setFrom($_ENV['APP_EMAIL'], $_ENV['APP_NAME']);
        $mail->addAddress($email, $firstname . " " . $lastname);

        //Content
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $alt;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}