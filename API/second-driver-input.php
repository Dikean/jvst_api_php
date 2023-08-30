<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

    /*echo 'name: '.$_POST['name'].', '.'last_name: '.$_POST['last_name'].', '.'street: '.$_POST['street'].', '.'house_number: '.$_POST['house_number'].', '.'phone: '.$_POST['phone'].', '.'zip_code: '.$_POST['zip_code'].', '.'municipality_id: '.$_POST['municipality_id'].', '.'birthdate: '.$_POST['birthdate'].', '.'country_id: '.$_POST['country_id'].', email: '.$_POST['email'];   

     exit();*/


    if (isset($_POST['name']) && trim($_POST['name']) != '' && isset($_POST['last_name']) && trim($_POST['last_name']) && isset($_POST['email']) && trim($_POST['email']) &&  isset($_POST['street']) && trim($_POST['street']) && isset($_POST['house_number'])  && trim($_POST['house_number']) && isset($_POST['phone']) && trim($_POST['phone']) && isset($_POST['zip_code']) && trim($_POST['zip_code']) && isset($_POST['municipality_id']) && trim($_POST['municipality_id']) && isset($_POST['birthdate']) && trim($_POST['birthdate']) && isset($_POST['country_id']) && trim($_POST['country_id']))
    { //se validan que los parámetros no vayan vacíos

        
        $name = $_POST['name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];

        $sql = "UPDATE driver SET NAME = ?, last_name = ?, street = ?, house_number = ?, phone = ?, zip_code = ?, municipality_id = ?, birthdate = ?, country_id = ? WHERE email = ?"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        $stmt->bind_param("ssssssssss", $name, $last_name, $_POST['street'], $_POST['house_number'], $_POST['phone'], $_POST['zip_code'], $_POST['municipality_id'], $_POST['birthdate'], $_POST['country_id'], $email);
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