<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{//Este endpoint es solo de tipo post

  if(isset($_GET['email']) && trim($_GET['email']))
  {
      //Obtener el id
    $sql = "    SELECT work_other_companies.`company_name`,work_other_companies.`start_date`,work_other_companies.`end_date` 
   FROM driver JOIN work_other_companies ON (driver.id = work_other_companies.`driver_id`)
   WHERE driver.`email` = ?"; // SQL with parameters
    $stmt = $link->prepare($sql); 
    $stmt->bind_param("s", $_GET['email']);
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
            $row =  array('company_name' => $data['company_name'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date']);
            array_push($obj, $row);
        }

        $response['code'] = '0';
        $response['info'] = $obj;
        
    }
  }
  else
  {
      $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
      $response=array('code'=>'1','message' => 'El campo email es obligatorio'); 
  }
}
else 
{

    $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
    $response = array('code' => '1','message' => 'Método no soportado para este endpoint.');
   
}

header($header);
echo json_encode($response);

$conexion->desconectar(); //nos desconectamos de la base de datos    