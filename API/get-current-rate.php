<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);


$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{//Este endpoint es solo de tipo post

  if(isset($_GET['tipo_tarifa']) && trim($_GET['tipo_tarifa']))
  {
      //Obtener el id
    $sql = "SELECT rate, id FROM rates WHERE tipo_tarifa = ? AND vigente = 'S'"; // SQL with parameters
  

    $stmt = $link->prepare($sql); 
    $stmt->bind_param("s", $_GET['tipo_tarifa']);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
   

  //  $data = $result->fetch_assoc(); // fetch data  
    $response = array();
    $obj =array(); 

    if(is_null($user))
    {
        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response=array('code'=>'1','message' => 'No hay datos.');
    }
    else
    {
         
        $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta

        while ( $data = $result->fetch_assoc() )
        {
            
            if( $_GET['tipo_tarifa'] == 'contado')
            {
                $row =  array('rate' => $data['rate'], 'id' => $data['id']);
            }
            else
            {
                $row = array('rate' => $data['rate'], 'cuotas' => 2, 'cuota_a_pagar' => $data['rate'] / 2, 'id' => $data['id']);
            }
            array_push($obj, $row);
        }

        $response['code'] = '0';
        $response['info'] = $obj;
        
    }
  }
  else
  {
      $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
      $response=array('code'=>'1','message' => 'El campo tipo_tarifa es obligatorio'); 
  }
}
else 
{

    $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
    $response = array('code' => '1','message' => 'Método no soportado para este endpoint.');
   
}

header($header);
echo json_encode($response);

