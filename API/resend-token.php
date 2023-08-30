<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post
    if(isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['user_type']) && trim($_POST['user_type']) != '')
    {
        $response = array();
        $obj =array(); 
        $sql = "SELECT token FROM send_token WHERE email = ? AND STATUS = 'ACTIVE'"; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result

        $data = $result->fetch_assoc(); // fetch data  

        if(is_null($data))
        {
            $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
            $response=array('code'=>'1','message' => 'No hay datos.');
        }
        else
        {
                $email = $_POST['email'];
                $subject = 'Renvio de token';
                $token = $data['token'];
                $type = $_POST['user_type'];
                $body = "<p>Estimado usuario , se le reenvía el siguiente token: ".$data['token']."</p>
                <a href='".$urlPrefix."get-confirm-token.php?email=$email&token=$token&user_type=$type'>O haga click en el siguiente   enlace para confirmar su registro</a>"; 

                $sent = sendMail::send($email, $subject, $body);
       
                if($sent == '0')
                {
                    $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                    $response= array('code'=>'0','message'=>'Correo Enviado Exitosamente.');
                }
                else
                {
                   $header = "HTTP/1.1 422 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                   $response= array('code'=>'1','message'=>$sent);
                   
                }
        }
    }
    else
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Los campos email  y user_type son obligatrios.');
    }
}
else 
{

    $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
    $response = array('code' => '1','message' => 'Método no soportado para este endpoint.');
   
}

header($header);
echo json_encode($response);

$conexion->desconectar(); //nos desconectamos de la base de datos   