<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['token']) && trim($_POST['token']) != ''  )
    { //se validan que los parámetros no vayan vacíos
        
        
        $sql = "SELECT token FROM users WHERE email = ? "; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("s",  $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        if(is_null($data))
        {
            $header = "HTTP/1.1 200 Ok";
            $response = array('message' => 'No hay registros asociados a la consulta.', 'code' => '1');
        }
        else
        {
            if($data['token'] == $_POST['token'])
            {
                $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'0','message'=>'Token Válido');   
            }
            else
            {
                $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'1','message'=>'Token Inválido');  
            }

            
        }
    }
    else
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, El campo email y token no pueden ir vacíos.');
    }
}
else 
{
    $header = "HTTP/1.1 405 Method not supported"; //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response = array('code' => '1','message' => 'Unprocessable Entity, Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);
$conexion->desconectar(); //nos desconectamos de la base de datos 
