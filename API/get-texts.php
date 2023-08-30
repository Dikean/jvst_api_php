<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../clases/consumeResources.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {//Este endpoint es solo de tipo post

    if (isset($_GET['language']) && trim($_GET['language']) != '' && isset($_GET['section']) && trim($_GET['section']) != '') {
       
        


        $response = array();
        $section = array();
        $consume = new consumeResoruces($urlPrefix  . 'get-page-div.php?language='. $_GET['language'] . '&section=' . $_GET['section']);
        $data = $consume->getResource();
        $pageDiv = array();

        foreach ($data['info'] as  $value) {
            $consume2 = new consumeResoruces($urlPrefix  . 'get-page-components.php?language='. $_GET['language'] . '&section=' . $_GET['section']);
            $data2 = $consume2->getResource();
            print_r($data2['info']); exit();
            $pageDiv[$value->page_div] = 5;
        }
        
        $section[$_GET['section']] = $pageDiv;

        $response[strtoupper($_GET['language'])] = $section;
        $response['code'] = '0';
        $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
        ksort($response);
    }
    else{

        $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response = array('code' => '1','message' => 'El parámetro language y section no deben ir vacíos.');

    }

} else {
    $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
    $response = array('code' => '1','message' => 'Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); //nos desconectamos de la base de datos
