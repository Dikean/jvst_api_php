<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function build_url()
{  
    define('BASE_URL','/var/www/html/apidian2021/storage/app/public'); //Without last slash
    $Path = BASE_URL;
    foreach(func_get_args() as $path_part)
    {
        $Path .= '/' . $path_part;
    }
    return $Path;
}

function send($recipiente,$pdfName,$nit){
        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
           // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'facele@estrateg.com';                     //SMTP username
         
            $mail->Password   = '91szQ20wmpWGLZJH';                               //SMTP password
          
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Remitente
            $mail->setFrom('nomina@estrateg.com','Descarga tu comprobante de nomina');
           //Destinatario
            $mail->addAddress($recipiente);     //Add a recipient

            //Attachments esta es la parte de los adjuntos
          // $nit='/'.$nit; $pdfName='/'.$pdfName;
             //$paquete=build_url($nit,$pdfName);  
            //$mail->addAttachment($paquete);         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            $paquete='https://backend.estrateg.com/apidian2021/storage/app/public/'.$nit.'/'.$pdfName;
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Comprobante de nomina';
            $mail->Body    = '<p>Estimado usuario, En este link, encontrataras tu documento de nomina </p><p>Atentamente,</p><p>GASTROCENTRO S.A.S.</p> <a href="'.$paquete.'"></a>';
           

            $mail->send();
            $response= array('response_code'=>'00','message'=>'Mail enviado con exito.');
  
        } catch (Exception $e) {
            
            $response= array('response_code'=>'01','message'=>'Message could not be sent. Mailer Error: {$mail->ErrorInfo}');

        }
    return $response;
}        
  ///
if ($_SERVER['REQUEST_METHOD'] == 'POST') {//Este endpoint es solo de tipo post
    //send('jailtonyanesromero@gmail.com','NIS-NI102.pdf','890100275');
    $response=  send($_POST['destinatario'],$_POST['pdfName'],$_POST['nit']);
} else {
    header("HTTP/1.1 405 Method not supported"); //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response= array('response_code'=>'405','message'=>'Unprocessable Entity, Método no soportado para este endpoint.');
    
}
  echo json_encode($response);

?>

