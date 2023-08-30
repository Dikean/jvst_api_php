<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post


    if (isset($_POST['email']) && trim($_POST['email']) != '')
    { //se validan que los parámetros no vayan vacíos

        //Obtener el id
        $sql = "SELECT id FROM clients WHERE email = ? "; // SQL with parameters
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
            
          
            $sql = "insert into company(name, street, house_number, zip_code, municipality_id, website, company_phone, contact_person_name, contact_person_phone, contact_person_email, client_id, merchant_registry_info, registry_court_id, registry_date, merchant_registry_type,merchant_registry_number,company_type_id)values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; //se escribe la consulta para la sentencia preparada
            $stmt = $link->prepare($sql); //se corre la sentencia

            $stmt->bind_param("sssssssssssssssss", $_POST['name'], $_POST['street'], $_POST['house_number'], $_POST['zip_code'], $_POST['municipality_id'], $_POST['website'], $_POST['company_phone'], $_POST['contact_person_name'], $_POST['contact_person_phone'], $_POST['contact_person_email'], $data['id'], $_POST['merchant_registry_info'], $_POST['registry_court_id'], $_POST['registry_date'], $_POST['merchant_registry_type'], $_POST['merchant_registry_number'], $_POST['company_type_id'] );

            $result=$stmt->execute();

            if ($result) {
    
                $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
                $response= array('code'=>'0','message'=>'Datos ingresados correctamente.');
            }
            else
            {
                $header = "HTTP/1.1 422 Unprocessable Entity";
                $response = array('code' => '1','message' => $stmt->error);
                 
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