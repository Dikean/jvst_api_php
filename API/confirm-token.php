<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post



    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['token']) && trim($_POST['token']) )
    { //se validan que los parámetros no vayan vacíos

        
        $sql = "SELECT send_token.status, user_type FROM send_token WHERE email = ? AND token = ?"; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("ss", $_POST['email'], $_POST['token']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        if(is_null($data))
        {
            $response = array('status' => '','message' => 'no hay datos', 'code' => '1');
        }
        else
        {
            
            if($data['status'] == 'ACTIVE')
            {
                $sql = "UPDATE send_token SET status = 'INACTIVE' WHERE email = ? and token = ?"; //se escribe la consulta para la sentencia preparada
                $stmt = $link->prepare($sql); //se corre la sentencia

                $stmt->bind_param("ss", $_POST['email'], $_POST['token']);
                $result=$stmt->execute();

                if ($result)
                {
                    $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                    $response= array('code' => '0' , 'message' => 'Datos Actualizados correctamente.', 'method' => 'POST', 'user' => $data['user_type']);
                }
                else
                {
                    $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
                    $response= array('code'=>'1','message'=>'Error en el proceso de actialización.');
                } 
            }
            else
            {
                $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'1','message'=>'Token Inválido.');
            }    
           
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