<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

function send_otp_mail($to_email, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vaibhavilohar84@gmail.com'; // your Gmail
        $mail->Password = 'ddaokqeydwspfdtj'; // app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('vaibhavilohar84@gmail.com', 'StreetFood App');
        $mail->addAddress($to_email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body    = "Your OTP is <b>$otp</b>. It will expire in 10 minutes.";

        $mail->send();
    } catch (Exception $e) {
        echo "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
