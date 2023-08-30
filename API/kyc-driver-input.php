<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

 /*     echo 'front_card_side: '.$_POST['front_card_side'].', '.'back_card_side: '.$_POST['back_card_side'].', '.'email: '.$_POST['email'].', '.'judicial_record: '.$_POST['judicial_record'];   

     exit();*/

    if (isset($_POST['email']) && trim($_POST['email']) != '')
    { //se validan que los parámetros no vayan vacíos

        //Obtener el id
        $sql = "SELECT id FROM driver WHERE email = ? "; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("s", $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        
        if(is_null($data))
        {
            $response = array('message' => 'No hay datos', 'code' => '1');
        }
        else
        {
            
            $sql = "insert into kyc(front_card_side, back_card_side, driver_id, judicial_record)values(?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt = $link->prepare($sql); //se corre la sentencia

            $stmt->bind_param("ssss", $_POST['front_card_side'], $_POST['back_card_side'], $data['id'], $_POST['judicial_record'] );
            $result=$stmt->execute();
            if ($result) {
    
                header("HTTP/1.1 200 Ok"); //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'0','message'=>'Datos ingresados correctamente.');
                
            }
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