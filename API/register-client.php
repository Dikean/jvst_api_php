<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

    if (isset($_POST['email']) && trim($_POST['email']) != '' )
    { //se validan que los parámetros no vayan vacíos

        
        $sql = "SELECT id FROM clients WHERE email = ?"; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("s",  $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        if(is_null($data))
        {
            $response = array('message' => 'Cliente no existe', 'code' => '0');
        }
        else
        {
            header("HTTP/1.1 200 Ok"); //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'1','message'=>'Cliente ya se encuentra registrado.');
        }
        echo json_encode($response);
    }
    else
    {
        header("HTTP/1.1 422 Unprocessable Entity"); // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, El campo token no puede ir vacío.');
        echo json_encode($response);
    }
}
else 
{
    header("HTTP/1.1 405 Method not supported"); //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response = array('code' => '1','message' => 'Unprocessable Entity, Método no soportado para este endpoint.');
    echo json_encode($response);
}

$conexion->desconectar(); //nos desconectamos de la base de datos   