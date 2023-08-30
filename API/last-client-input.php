<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post


    if (isset($_POST['name']) && trim($_POST['name']) != ''  && isset($_POST['email']) && trim($_POST['email']) &&  isset($_POST['street']) && trim($_POST['street']) && isset($_POST['house_number'])  && trim($_POST['house_number']) && isset($_POST['company_phone']) && trim($_POST['company_phone']) && isset($_POST['zip_code']) && trim($_POST['zip_code']) && isset($_POST['municipality_id']) && trim($_POST['municipality_id'])    )
    { //se validan que los parámetros no vayan vacíos
        
       
        
    


        $sql = "UPDATE clients SET NAME = ?,  street = ?, house_number = ?, phone = ?, zip_code = ?, municipality_id = ? WHERE email = ?"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        $stmt->bind_param("sssssss", $_POST['name'],  $_POST['street'], $_POST['house_number'], $_POST['company_phone'], $_POST['zip_code'], $_POST['municipality_id'], $_POST['email']);
        $result=$stmt->execute();

        if ($result)
        {
            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Datos Actualizados correctamente.');
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
        $response = array('code' => '1','message' => 'Los campos marcados como obligatorios no pueden ir vacíos');
       
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