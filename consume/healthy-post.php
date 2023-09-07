<?php

include('../params/connectParams.php');
include('../inlcude/headers.php');

$curl = curl_init();

// Crear un array de datos a enviar
$data = array(
    'alergia' => $_POST['alergia'],
    'enfermedad' => $_POST['enfermedad'],
    'prescripcionMedica' => $_POST['prescripcionMedica'],
    'incapacidad' => $_POST['incapacidad'],
    'restriccionAlimenticia' => $_POST['restriccionAlimenticia'],
    'users_id' => $_POST['users_id']

);

curl_setopt_array($curl, array(
  CURLOPT_URL => $urlAPI."API/post-healthy_conditions_jvst.php",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => $data, // Usar el array de datos modificado
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

