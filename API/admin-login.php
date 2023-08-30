<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['password']) && trim($_POST['password']) != ''  )
    { //se validan que los parámetros no vayan vacíos
        $password = sha1($_POST['password']);
        
        $sql = "SELECT id,Mail_REG,token,Date_REG FROM gx_register WHERE Mail_REG = ? AND Pass_REG = ? "; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("ss",  $_POST['email'], $password);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        if(is_null($data))
        {
            $header = "HTTP/1.1 200 Ok";
            $response = array('message' => 'Usuario o Password inválido', 'code' => '1');
        }
        else
        {
            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Ingreso exitoso.', 'email' => $data['Mail_REG'], 'token' => $data['token'], 'fecha_regisgtro' => $data['Date_REG']);
        }
    }
    else
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, El campo email y password no pueden ir vacíos.');
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