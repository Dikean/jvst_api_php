<?php

include('../params/connectParams.php');
include('../inlcude/headers.php');

$curl = curl_init();

// Crear un array de datos a enviar
$data = array(
    'name_family' => $_POST['name_family'],
    'document_family' => $_POST['document_family'],
    'relationship_family' => $_POST['relationship_family'],
    'birthdate_family' => $_POST['birthdate_family'],
    'age_family' => $_POST['age_family'],
    'users_id' => $_POST['users_id']

);

curl_setopt_array($curl, array(
  CURLOPT_URL => $urlAPI. "API/post-family_infos_jvst.php",
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
