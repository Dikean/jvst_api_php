<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {//Este endpoint es solo de tipo post

    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['password']) && trim($_POST['password']) != '' ) { //se validan que los parámetros no vayan vacíos

               
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $type = $_POST['user_type'];
        $token = rand(100000,999999);
        $status = 'ACTIVE';

        $subject = 'Sie können unser Angebot auch nutzen';
        $body = "<p>Estimado usuario $email, confirme la recepción del siguiente token ".$token."</p> <a href='".$urlPrefix."get-confirm-token.php?email=$email&token=$token&user_type=$type'>O haga click en el siguiente   enlace para confirmar su registro</a>"; 

        
        $sent = sendMail::send($email, $subject, $body);
       
        if($sent == '0')
        {
            $sql = "insert into send_token(email, token, status, password, user_type)values(?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt = $link->prepare($sql); //se corre la sentencia

            $stmt->bind_param("sssss", $email, $token, $status,$password, $_POST['user_type']);
            $result=$stmt->execute();

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
        $response = array('code' => '1','message' => 'Unprocessable Entity, Los campos email y password no deben ir vacios.');
       
    }
} else {
    header("HTTP/1.1 405 Method not supported"); //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response = array('code' => '1','message' => 'Unprocessable Entity, Método no soportado para este endpoint.');
    
}


echo json_encode($response);

$conexion->desconectar(); //nos desconectamos de la base de datos   