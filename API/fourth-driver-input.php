<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

/*    echo 'umst_id: '.$_POST['umst_id'].', '.'iva_tax: '.$_POST['iva_tax'].', '.'tax_number: '.$_POST['tax_number'].', farm_registry_id: '.$_POST['farm_registry_id'].', email: '.$_POST['email'];   

     exit();*/


    if (isset($_POST['umst_id']) && trim($_POST['umst_id']) && isset($_POST['iva_tax']) && trim($_POST['iva_tax']) && isset($_POST['tax_number']) && trim($_POST['tax_number']) && isset($_POST['farm_registry_id']) && trim($_POST['farm_registry_id'])    && isset($_POST['email']) && trim($_POST['email'])   )
    { //se validan que los parámetros no vayan vacíos

        
        $name = $_POST['name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $sql = "UPDATE driver SET umst_id = ?, iva_tax = ?, tax_number = ?, farm_registry_id = ? WHERE email = ?"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        $stmt->bind_param("sssss", $_POST['umst_id'], $_POST['iva_tax'], $_POST['tax_number'], $_POST['farm_registry_id'], $email);
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