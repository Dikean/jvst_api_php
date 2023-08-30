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

        $sql = "update company set name = ?, street = ?, house_number = ?,  zip_code = ?,  municipality_id = ?,  website =?, company_phone =?,  contact_person_name =?,  contact_person_phone =?,  contact_person_email =?,  client_id =?,  merchant_registry_info =?,  registry_court_id =?,  registry_date =?,  merchant_registry_type =?,  merchant_registry_number=?,  company_type_id =?,  specify_company_id=?,  other_specify =? WHERE id =?"; //se escribe la consulta para la sentencia preparada
        $stmt = $link->prepare($sql); //se corre la sentencia

        $stmt->bind_param("ssssssssssssssssssss", $_POST['name'], $_POST['street'], $_POST['house_number'], $data['zip_code'], $_POST['municipality_id'], $_POST['website'], $_POST['company_phone'],$_POST['contact_person_name'], $_POST['contact_person_phone'], $_POST['contact_person_email'], $data['client_id'], $_POST['merchant_registry_info'], $_POST['registry_court_id'], $_POST['registry_date'],$_POST['merchant_registry_type'], $_POST['merchant_registry_number'], $_POST['company_type_id'], $data['specify_company_id'], $_POST['other_specify'],   $_POST['id']);
        $result=$stmt->execute();

        if ($result)
        {
            $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('code' => '0','message' => 'Datos Actualizados Correctamente.');
        } 
        else
        {
           $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
           $response= array('code' => '0','message' => $stmt->error ); 
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