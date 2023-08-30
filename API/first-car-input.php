<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post


    if (isset($_POST['brand']) && trim($_POST['brand']) != ''  && isset($_POST['model']) && trim($_POST['model']) &&  isset($_POST['frame_number']) && trim($_POST['frame_number']) && isset($_POST['client_id'])  && trim($_POST['client_id']) && isset($_POST['mileage']) && trim($_POST['mileage']) && isset($_POST['first_registration_date']) && trim($_POST['first_registration_date']) && isset($_POST['vehicle_image']) && trim($_POST['vehicle_image'])    )
    { //se validan que los parámetros no vayan vacíos
        
     

       // $sql = "insert into car (brand, model, frame_number, client_id, mileage, first_registration_date, client_id, vehicle_image)values(?, ?, ?, ?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
        $sql = "insert into car (brand, model, frame_number, mileage, first_registration_date, vehicle_image, client_id)values(?, ?, ?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        //$stmt->bind_param("ss", $_POST['brand'],  $_POST['model'], $_POST['frame_number'], $_POST['client_id'], $_POST['mileage'], $_POST['first_registration_date'], $_POST['client_id'], $_POST['vehicle_image']);
        $stmt->bind_param("sssssss", $_POST['brand'], $_POST['model'], $_POST['frame_number'], $_POST['mileage'], $_POST['first_registration_date'], $_POST['vehicle_image'], $_POST['client_id']);
        $result=$stmt->execute();

        if ($result)
        {

            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Datos Ingresados correctamente.', 'last_id' => mysqli_insert_id($link) );
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