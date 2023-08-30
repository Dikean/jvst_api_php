<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');


$conexion = new conexion($host, $user, $password, $db, $port);
echo $host; exit();
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post

    if (isset($_POST['token']) && trim($_POST['token']) != '' && isset($_POST['email']) && trim($_POST['email']) != '')
    { //se validan que los parámetros no vayan vacíos

        
        $token = $_POST['token'];
        $status = 0;

        $sql = "SELECT email,send_token.`password` FROM send_token WHERE token = ? and email = ? AND STATUS = 'ACTIVE'"; // SQL with parameters
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("ss", $token, $_POST['email']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data   

        if(is_null($data))
        {
            $response = array('message' => 'No hay datos', 'code' => '1');
        }
        else
        {
            
            $sql = "insert into clients(email,password,status)values(?,?,?)"; //se escribe la consulta para la sentencia preparada
            $stmt = $link->prepare($sql); //se corre la sentencia

            $stmt->bind_param("sss", $data['email'], $data['password'], $status);
            $result=$stmt->execute();
           

            if ($result) {

                $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code' => '0','message' => 'Datos ingresados correctamente.');
                
            }
            else
            {
                $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code' => '1','message' => $stmt->error);
            }
        }
       
    }
    else
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, El campo token no puede ir vacío.');
        
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