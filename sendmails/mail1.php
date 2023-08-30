<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function send($recipiente, $factura)
{
    //Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'solucionesshaima.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'smartcarlogist@solucionesshaima.com';
        $mail->Password = 'tNm~%Sv.{amY';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  //'tls';
        $mail->Port = 25;
        $mail->setFrom('info@gratisfactura.com', 'Gratis Factura - Registro');
        $mail->addAddress($recipiente); // where you want to send mail


        $mail->isHTML(true);
        $mail->Subject = 'Confirma tu correo - Gratis Factura';
        $mail->Body = 'hola';
        $mail->AltBody = 'Para continuar, ingresa el codigo  O abre una pestaña en tu navegador con la URL: https://registration.gratisfactura.com/';
        $mail->send();
        header('Location: ../gf_codemail.php');
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}


$indice=8702;
while ($indice<=8703) {
    send('jailtonyanesromero@gmail.com', $indice);
    $indice++;
}
?>

