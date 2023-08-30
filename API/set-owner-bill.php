<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');
include('../inlcude/functions.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post
   
    if (isset($_POST['email']) && trim($_POST['email']) != ''  && isset($_POST['nit']) && trim($_POST['nit'])  && isset($_POST['owner']) && trim($_POST['owner'])   )
    { //se validan que los parámetros no vayan vacíos
        
        $date = date('y-m-d');
        if($_POST['owner'] == 'tercero')
        {
            $sql = "insert into owner_bill (name, last_name, nit, date, email, owner, observacion, nit_usuario, email_usuario, payment_reference)values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt = $link->prepare($sql); //se corre la sentencia
            $stmt->bind_param("ssssssssss", $_POST['name'], $_POST['last_name'], $_POST['nit'], $date, $_POST['email'], $_POST['owner'], $_POST['observacion'], $_POST['nit_usuario'], $_POST['email_usuario'], $_POST['payment_reference']);
        }
        else
        {
            $sql = "insert into owner_bill (name ,last_name, nit, date, owner, observacion,nit_usuario, email_usuario ,email,payment_reference)values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt = $link->prepare($sql); //se corre la sentencia
            $stmt->bind_param("ssssssssss",$_POST['name'], $_POST['last_name'], $_POST['nit'], $date, $_POST['owner'], $_POST['observacion'], $_POST['nit_usuario'], $_POST['email_usuario'], $_POST['email'], $_POST['payment_reference']);
        }

        $result=$stmt->execute();

        if ($result)
        {
            $purchaseDate = date('Y-m-d');
            $installments = $_POST['rate_id'] == 1 ? 1 : 2; 
            $vigente = 'S'; 
            if($installments == 1)
            {
                $adviceDate = addTime(date('Y-m-d'),'P1Y');
            }
            else
            {
                $adviceDate = addTime(date('Y-m-d'),'P6M');
            }  

            $sql2 = "insert into purchase_order (email_usuario, purchase_date, rate_id, installments, last_advice_date, vigente, payment_reference)values(?, ?, ?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt2 = $link->prepare($sql2); //se corre la sentencia
            $stmt2->bind_param("sssssss", $_POST['email'], $purchaseDate, $_POST['rate_id'], $installments, $adviceDate, $vigente, $_POST['payment_reference']);

            $result2=$stmt2->execute();
            $orderId = $stmt2->insert_id;


            $sql3 = "insert into payments (email_usuario, order_id, payment_date, payment_reference)values(?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt3 = $link->prepare($sql3); //se corre la sentencia
            $stmt3->bind_param("ssss", $_POST['email'], $orderId, date('Y-m-d'), $_POST['payment_reference']);
            $result3=$stmt3->execute();


            
            if($result2)
            {
                $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'0','message'=>'Datos Ingresados correctamente.');
            }
            else
            {
                $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'1','message'=>$stmt2->error);
            }



            
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
        $response = array('code' => '1','message' => 'Los campos marcados email y nit y titular de la factura no pueden ir vacíos.');
       
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