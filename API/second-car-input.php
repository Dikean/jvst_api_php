<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post


    if (isset($_POST['where_to_pick_up']) && trim($_POST['where_to_pick_up']) != ''  && isset($_POST['pick_up_address']) && trim($_POST['pick_up_address']) &&  isset($_POST['pick_up_city_code']) && trim($_POST['pick_up_city_code']) && isset($_POST['initial_pick_up_date'])  && trim($_POST['initial_pick_up_date']) && isset($_POST['end_pick_up_date']) && trim($_POST['end_pick_up_date'])   && isset($_POST['id']) && trim($_POST['id'])    )
    { //se validan que los parámetros no vayan vacíos



        $sql = "UPDATE car SET where_to_pick_up = ?, pick_up_address = ?, pick_up_city_code = ?, initial_pick_up_date = ?, initial_pick_up_initial_time = ?, end_pick_up_initial_time = ?, end_pick_up_date = ?, initial_pick_up_end_time = ?, end_pick_up_end_time = ? WHERE id = ?"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        $stmt->bind_param("ssssssssss", $_POST['where_to_pick_up'], $_POST['pick_up_address'], $_POST['pick_up_city_code'], $_POST['initial_pick_up_date'], $_POST['initial_pick_up_initial_time'], $_POST['end_pick_up_initial_time'], $_POST['end_pick_up_date'], $_POST['initial_pick_up_end_time'], $_POST['end_pick_up_end_time'], $_POST['id']);
        $result=$stmt->execute();

          if ($result)
        {
            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Datos Actualizados correctamente.');
        }
        else
        {
             $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'1','message'=>'Error en el proceso de actualización.');
        }  
      
        
    }
    else
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, Los campos reqieridos no pueden ir vacíos.');
    
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