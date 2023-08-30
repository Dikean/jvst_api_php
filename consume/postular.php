<?php
include('../clases/auth.php');
include('../params/connectParams.php');

$auth = new auth();
$respuesta =  $auth->usuario('80442360',base64_encode('14011972'),2);

 $info= (array) json_decode(stripslashes($respuesta));
 //var_dump($info);exit();

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => $urlPrefix.'Elecciones/API/postular.php',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS => array('nombre' => 'Jailton','identificacion' => '72260','email' => 'jailtonyanesromero@gmail.com','picture' => 'foto.jpg','rol' => 'admin'),
	CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'accept: application/json'
	),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
?>