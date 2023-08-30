<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

 

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post
   
    if (isset($_POST['amount']) && trim($_POST['amount'])  && isset($_POST['payment_reference']) && trim($_POST['payment_reference']) && isset($_POST['transaction_id']) && trim($_POST['transaction_id'])       )
    { //se validan que los parámetros no vayan vacíos
        
        $sql = "UPDATE payments SET amount = ?, transaction_id = ? WHERE payment_reference = ? "; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia
        $stmt->bind_param("sss", $_POST['amount'], $_POST['transaction_id'], $_POST['payment_reference']);

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
        $response = array('code' => '1','message' => 'Verifique que los campos email_usuario, order_id, amount, payment_date, payment_reference y transaction_id no estén vacíos.' );
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