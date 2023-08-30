<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

 /*   echo 'driver_license_number: '.$_POST['driver_license_number'].', '.'expedition_driver_license_date: '.$_POST['expedition_driver_license_date'].', '.'registry_class_id: '.$_POST['registry_class_id'].', '.'cat_flesh_id: '.$_POST['cat_flesh_id'];   

     exit();*/


    if (isset($_POST['driver_license_number']) && trim($_POST['driver_license_number']) && isset($_POST['expedition_driver_license_date']) && trim($_POST['expedition_driver_license_date']) &&  isset($_POST['cat_flesh_id']) && trim($_POST['cat_flesh_id'])    && isset($_POST['email']) && trim($_POST['email'])   )
    { //se validan que los parámetros no vayan vacíos

        
        $name = $_POST['name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $sql = "UPDATE driver SET driver_license_number = ?, expedition_driver_license_date = ?,  cat_flesh_id = ? WHERE email = ?"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        $stmt->bind_param("ssss", $_POST['driver_license_number'], $_POST['expedition_driver_license_date'],  $_POST['cat_flesh_id'], $email);
        $result=$stmt->execute();

        if ($result)
        {
            header("HTTP/1.1 200 Ok"); //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'0','message'=>'Datos Actualizados correctamente.');
        }
        else
        {
             header("HTTP/1.1 422 Unprocessable Entity"); //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code'=>'1','message'=>$stmt->error);
        }  
      
        echo json_encode($response);
    }
    else
    {
        header("HTTP/1.1 422 Unprocessable Entity"); // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, Los campos reqieridos no pueden ir vacíos.');
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