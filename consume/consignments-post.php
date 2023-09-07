<?php

include('../params/connectParams.php');
include('../inlcude/headers.php');

$curl = curl_init();

// Crear un array de datos a enviar
$data = array(
    'date' => $_POST['date'],
    'amount' => $_POST['amount'],
    'bank' => $_POST['bank'],
    'users_id' => $_POST['users_id'],
);

// Crear un archivo temporal para cargar el archivo
$tempFile = tempnam(sys_get_temp_dir(), 'voucher_');
file_put_contents($tempFile, file_get_contents($_FILES['voucher']['tmp_name']));

// Agregar el archivo al array de datos con un "@" antes del nombre del archivo
$data['voucher'] = curl_file_create($tempFile, $_FILES['voucher']['type'], $_FILES['voucher']['name']);

curl_setopt_array($curl, array(
  CURLOPT_URL =>  $urlAPI. "API/post-consignments_jvst.php",
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

// Borrar el archivo temporal
unlink($tempFile);
