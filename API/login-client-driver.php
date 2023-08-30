<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post


    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['password']) && trim($_POST['password']) != '' && isset($_POST['user_type']) && trim($_POST['user_type']) != '')
    { //se validan que los parámetros no vayan vacíos
      $email = $_POST['email'];
      $password = md5($_POST['password']);  

      if($_POST['user_type'] == 'driver')
      {
          $sql = "SELECT id, driver.name,last_name, 'driver' AS user_type FROM driver WHERE email = ? AND PASSWORD = ?";
      }
      else
      {
          $sql = "SELECT id,clients.name, 'client' AS user_type FROM clients WHERE email = ? AND PASSWORD = ?";
      }  


       //Obtener el id
        $stmt = $link->prepare($sql); 
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $data = $result->fetch_assoc(); // fetch data 
        
        if(is_null($data))
        {
            $header= "HTTP/1.1 200 Ok";
            $response = array('message' => 'No hay datos', 'code' => '1');
        }
        else
        {
            $header= "HTTP/1.1 200 Ok";
            if($data['user_type'] == 'client')
            {
                $response = array('name' => $data['name'], 'user_type' => $data['user_type'], 'code' => '0');
            }
            else
            {
                $response = array('name' => $data['name'] .' '. $data['last_name'] , 'user_type' => $data['user_type'], 'code' => '0'); 
            }
            
            
        }            

    }
    else
    {
        $header= "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'Unprocessable Entity, Los campos reqieridos no pueden ir vacíos.');
    }
}
else 
{
    $header= "HTTP/1.1 405 Method not supported"; //en el caso de que se haga una petición diferente a post, se arroja este mensaje
    $response = array('code' => '1','message' => 'Unprocessable Entity, Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);
$conexion->desconectar(); //nos desconectamos de la base de datos   