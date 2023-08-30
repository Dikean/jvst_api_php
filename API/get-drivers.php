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
        $conditionalID = 'WHERE driver.id = ?';
    }

    //Obtener el id
    $sql = " SELECT
    `driver`.`id`
    , `driver`.`name`
    , `driver`.`last_name`
    , `driver`.`birthdate`
    , `driver`.`street`
    , `driver`.`house_number`
    , `driver`.`country_id`
    , `driver`.`phone`
    , `driver`.`driver_license_number`
    , `driver`.`expedition_driver_license_date`
    , `driver`.`cat_flesh_id`
    , `driver`.`tax_number`
    , `driver`.`farm_registry_id`
    , `driver`.`iva_tax`
    , `driver`.`work_other_companies`
    , `driver`.`email`
    , `driver`.`zip_code`
    , `kyc`.`front_card_side`
    , `kyc`.`back_card_side`
    , `kyc`.`judicial_record`
    , `country`.`description` AS country_description
    , `municipalities`.`municipality_description`
    , `municipalities`.`capital_description`
    , `farm_registry`.`description` AS farm_registry_description
   , `cat_flesh`.`description` AS cat_flesh_description
FROM
    `driver`
     INNER JOIN `country` 
       ON (`driver`.`country_id` = `country`.`id`)
     INNER JOIN `municipalities` 
       ON (`driver`.`municipality_id` = `municipalities`.`id`)

    INNER JOIN `cat_flesh` 
        ON (`driver`.`cat_flesh_id` = `cat_flesh`.`id`)
    INNER JOIN `farm_registry` 
       ON (`driver`.`farm_registry_id` = `farm_registry`.`id`)
    INNER JOIN `kyc` 
       ON (`kyc`.`driver_id` = `driver`.`id`) " . $conditionalID; // SQL with parameters

    
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
            $row =  array('id' => $data['id'], 'name' => $data['name'],'last_name' => $data['last_name'], 'birthdate' => $data['birthdate'], 'street' => $data['street'], 'house_number' => $data['house_number'], 'country_id' => $data['country_id'], 'phone' => $data['phone'], 'driver_license_number' => $data['driver_license_number'], 'expedition_driver_license_date' => $data['expedition_driver_license_date'],  'cat_flesh_id' => $data['cat_flesh_id'], 'tax_number' => $data['tax_number'], 'farm_registry_id' => $data['farm_registry_id'], 'iva_tax' => $data['iva_tax'], 'work_other_companies' => $data['work_other_companies'], 'email' => $data['email'], 'zip_code' => $data['zip_code'], 'country_description' => $data['country_description'],  'front_card_side' => $data['front_card_side'],'back_card_side' => $data['back_card_side'], 'judicial_record' => $data['judicial_record'], 'municipality_description' => $data['municipality_description'], 'capital_description' => $data['capital_description'], 'farm_registry_description' => $data['farm_registry_description'], 'cat_flesh_description' => $data['cat_flesh_description'] );
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