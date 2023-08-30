<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function send($recipiente,$factura){
        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'facele@estrateg.com';                     //SMTP username
         
            $mail->Password   = '91szQ20wmpWGLZJH';                               //SMTP password
          
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Remitente
            $mail->setFrom('facturaciongastrocentro@gmail.com','Tu factura se encuentra lista');
           //Destinatario
            $mail->addAddress($recipiente);     //Add a recipient

            //Attachments esta es la parte de los adjuntos
            $mail->addAttachment('/var/www/html/createZipFiles/z090058103600021000'.$factura.'.zip');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = '900581036;GASTROCENTRO S.A.S.;FE'.$factura.';01;GASTROCENTRO S.A.S.';
            $mail->Body    = '<p>Estimado usuario, adjunto en este e-mail encontrarás el detalle de tu factura </p><p>Atentamente,</p><p>GASTROCENTRO S.A.S.</p>';
           

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
}        


$indice=10193;
while($indice<=10289){
  send('facturacion_electronica@mutualser.com',$indice);
  $indice++;
}
?>

