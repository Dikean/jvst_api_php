<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');



include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {//Este endpoint es solo de tipo post

    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['subject']) && trim($_POST['subject']) != '' && isset($_POST['body']) && trim($_POST['body']) != ''  ) { //se validan que los parámetros no vayan vacíos

               
        $email = $_POST['email'];
        $subject = md5($_POST['subject']);
        $body = $_POST['body'];
 
        $sent = sendMail::send($email, $subject, $body);
       
        if($sent == '0')
        {
            
            if ($result) {
                header("HTTP/1.1 200 Ok"); //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'0','message'=>'Correo Enviado Exitosamente.');
                
            }
        }
        else
        {
           header("HTTP/1.1 422 Ok"); //mensaje de salida  con su respectivo JSON de respuesta
           $response= array('code'=>'1','message'=>$sent);
           
        }
        
    } else {
        header("HTTP/1.1 422 Unprocessable Entity"); // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, Los campos email, subject y body no deben ir vacios.');
       
    }
} else {
    header("HTTP/1.1 405 Method not supported"); //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response = array('code' => '1','message' => 'Unprocessable Entity, Método no soportado para este endpoint.');
    
}


echo json_encode($response);

  