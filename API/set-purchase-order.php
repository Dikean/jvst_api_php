<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post
   
    if (isset($_POST['user_id']) && trim($_POST['user_id']) != ''  && isset($_POST['rate_id']) && trim($_POST['rate_id']))
    { //se validan que los parámetros no vayan vacíos
        

        $sql = "INSERT INTO purchase_order (user_id, purchase_date, rate_id, installments, last_advice_date, vigente)VALUES(?,?,?,?,?,?)"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia
        $stmt->bind_param("ssssss", $_POST['user_id'], $date, $_POST['rate_id'], $date, $_POST['installments'], $_POST['last_advice_date'], $_POST['vigente']);
        $result=$stmt->execute();

        if ($result)
        {

            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Datos Ingresados correctamente.');
        }
        else
        {
            $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'1','message'=>$stmt->error);
        }  
      
       
    }
    else
    {
        
    
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Los campos marcados user_id rate_id  no pueden ir vacíos.');
       
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