<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['db']) && trim($_POST['db']) != ''  )
    { //se validan que los parámetros no vayan vacíos
        
        $conexion = new conexion($host, $user, $password, $_POST['db'], $port);
        $link= $conexion->conectar(); //nos conectamos a la base de datos

        $sql = "SELECT email,first_name FROM factura_usuarios WHERE email = ? "; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("s",  $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        if(is_null($data))
        {
            $header = "HTTP/1.1 200 Ok";
            $response = array('message' => 'No hay registros asociados', 'code' => '1');
        }
        else
        {
            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Datos del usuario', 'email' => $data['email'], 'first_name' => $data['first_name']);
        }

        $conexion->desconectar(); //nos desconectamos de la base de datos   
    }
    else
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, El campo email y db no pueden ir vacíos.');
    }
}
else 
{
    $header = "HTTP/1.1 405 Method not supported"; //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response = array('code' => '1','message' => 'Unprocessable Entity, Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);
