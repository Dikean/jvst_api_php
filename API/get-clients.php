<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') 
{//Este endpoint es solo de tipo post
    $conditionalID = '';

    if($_GET['id'] != 'NO')
    {
        $conditionalID = 'WHERE company.id = ?';
    }

    //Obtener el id
    $sql = "SELECT
    `company`.`name` AS company_name
    , `company`.`street` AS company_street
    , `company`.`house_number` AS company_house_number
    , `company`.`zip_code` AS company_zip_code
    , `company`.`municipality_id` AS company_municipality_id
    , `company`.`website` AS company_website
    , `company`.`company_phone` AS company_phone
    , `company`.`contact_person_name` AS company_contact_person_name
    , `company`.`contact_person_phone` AS company_contact_person_phone
    , `company`.`contact_person_email` AS company_contact_person_email
    , `company`.`client_id` AS company_client_id
    , `company`.`merchant_registry_info` AS company_merchant_registry_info
    , `company`.`registry_court_id` AS company_registry_court_id
    , `company`.`registry_date` AS company_registry_date
    , `company`.`merchant_registry_type` AS companny_merchant_registry_type
    , `company`.`merchant_registry_number` AS company_merchant_registry_number
    , `company`.`company_type_id` AS company_company_type_id
    , `company`.`specify_company_id` AS company_specify_company_id
    , `company`.`other_specify` AS company_other_specify
    , `company`.`id` AS company_id
    , `municipalities`.`municipality_description` AS company_municipality_description
    , `municipalities`.`capital_description` AS company_capital_description
    , `clients`.`name` AS clients_name
    , `clients`.`street` AS clients_street
    , `clients`.`house_number` AS clients_house_number
    , `clients`.`phone` AS clients_phone
    , `clients`.`email` AS client_email
    , `clients`.`zip_code` AS clients_zip_code
    , `clients`.`municipality_id` AS clients_municipality_id
    , `registry_court`.`description` AS company_registry_court
    , `company_type`.`description` AS client_company_type_description
   , `specify_company`.`description` AS client_specify_company
    , `m2`.`municipality_description` AS client_municipality_description
    , `m2`.`capital_description` AS client_capital_description
   
FROM
    `company`
     INNER JOIN `municipalities` 
        ON (`company`.`municipality_id` = `municipalities`.`id`)
    INNER JOIN `clients` 
        ON (`company`.`client_id` = `clients`.`id`)
    INNER JOIN `registry_court` 
        ON (`company`.`registry_court_id` = `registry_court`.`id`)
    INNER JOIN `company_type` 
        ON (`company`.`company_type_id` = `company_type`.`id`)
    LEFT JOIN `specify_company` 
        ON (`company`.`specify_company_id` = `specify_company`.`id`)
    JOIN municipalities AS m2 ON(clients.`municipality_id` = m2.`id`) ". $conditionalID; // SQL with parameters
    $stmt = $link->prepare($sql); 

    if($_GET['id'] != 'NO')
    {
        $stmt->bind_param("s", $_GET['id']);
    }


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
            $row =  array('company_id' => utf8_encode($data['company_id']), 'company_name' => utf8_encode($data['company_name']),'company_house_number' => utf8_encode($data['company_house_number']), 'company_zip_code' => utf8_encode($data['company_zip_code']), 'company_municipality_id' => utf8_encode($data['company_municipality_id']), 'company_website' => utf8_encode($data['company_website']), 'company_phone' => utf8_encode($data['company_phone']), 'company_contact_person_name' => utf8_encode($data['company_contact_person_name']), 'company_contact_person_phone' => utf8_encode($data['company_contact_person_phone']), 'company_contact_person_email' => utf8_encode($data['company_contact_person_email']), 'company_client_id' => utf8_encode($data['company_client_id']), 'company_merchant_registry_info' => utf8_encode($data['company_merchant_registry_info']), 'company_registry_court_id' => utf8_encode($data['company_registry_court_id']), 'company_registry_date' => utf8_encode($data['company_registry_date']), 'companny_merchant_registry_type' => utf8_encode($data['companny_merchant_registry_type']), 'company_merchant_registry_number' => utf8_encode($data['company_merchant_registry_number']), 'company_company_type_id' => utf8_encode($data['company_company_type_id']), 'company_specify_company_id' => utf8_encode($data['company_specify_company_id']), 'company_other_specify' => utf8_encode($data['company_other_specify']), 'company_municipality_description' => utf8_encode($data['company_municipality_description']), 'company_capital_description' => utf8_encode($data['company_capital_description']),'clients_name' => utf8_encode($data['clients_name']), 'clients_street' => utf8_encode($data['clients_street']), 'clients_house_number' => utf8_encode($data['clients_house_number']), 'clients_phone' => utf8_encode($data['clients_phone']), 'client_email' => utf8_encode($data['client_email']), 'clients_zip_code' => utf8_encode($data['clients_zip_code']), 'clients_municipality_id' => utf8_encode($data['clients_municipality_id']), 'company_registry_court' => utf8_encode($data['company_registry_court']), 'client_company_type_description' => utf8_encode($data['client_company_type_description']), 'client_specify_company' => utf8_encode($data['client_specify_company']), 'client_municipality_description' => utf8_encode($data['client_municipality_description']), 'client_capital_description' => utf8_encode($data['client_capital_description']), );
            array_push($obj, $row);
        }

        $response['code'] = '0';
        $response['info'] = $obj;
        
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