<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';


class sendMail{

    public static function send($recipiente, $subject, $body){
       require '../params/connectParams.php';
       //Instantiation and passing `true` enables exceptions
       $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0; 
            $mail->isSMTP(); 
            $mail->Host = $mailHost; 
            $mail->SMTPAuth = true; 
            $mail->Username = $mailUserName; 
            $mail->Password = $mailPassword; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  //'tls'; 
            $mail->Port = 25; 
            $mail->setFrom($mailSetFrom); 
            $mail->addAddress($recipiente); // where you want to send mail 
            $mail->isHTML(true);
            $mail->CharSet = $mailCharSet; 
            $mail->Subject = $subject; 
            $mail->Body = $body;
            
            $mail->send(); 
            return '0';
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

}

?>

