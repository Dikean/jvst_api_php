<?php

include('../params/connectParams.php');
include('../inlcude/headers.php');

$curl = curl_init();

// Crear un array de datos a enviar
$data = array(
    'name_references' => $_POST['name_references'],
    'document_references' => $_POST['document_references'],
    'email_references' => $_POST['email_references'],
    'phone_references' => $_POST['phone_references'],
    'address_references' => $_POST['address_references'],
    'country_references' => $_POST['country_references'],
    'city_references' => $_POST['city_references'],
    'users_id' => $_POST['users_id'],

);

curl_setopt_array($curl, array(
  CURLOPT_URL => $urlAPI."API/post-references_jvst.php",
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

