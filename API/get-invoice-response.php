<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {//Este endpoint es solo de tipo post

    //Obtener el id
    $sql = "SELECT order_id,amount,payment_date,email_usuario FROM payments WHERE email_usuario = ? order by payment_date asc"; // SQL with parameters
    $stmt = $link->prepare($sql);
    $stmt->bind_param("s",  $_GET['email_usuario']);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
   

    //  $data = $result->fetch_assoc(); // fetch data
    $response = array();
    $obj =array();

    if (is_null($user)) {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response=array('code'=>'1','message' => 'No hay datos.');
    } else {
        $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta

        while ($data = $result->fetch_assoc()) {
            $row =  array('order_id' => $data['order_id'], 'amount' => $data['amount'], 'payment_date' => $data['payment_date'], 'email_usuario' => $data['email_usuario']);
            array_push($obj, $row);
        }

        $response['code'] = '0';
        $response['info'] = $obj;
    }
} else {
    $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
    $response = array('code' => '1','message' => 'Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); //nos desconectamos de la base de datos   

