<?php
include('../params/connectParams.php');
include('../inlcude/headers.php');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL =>  $urlAPIWompi . 'API/set-owner-bill.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('email' => $_POST['email'], 'nit' => $_POST['nit'], 'owner' => $_POST['owner'], 'name' => $_POST['name'], 'last_name' => $_POST['last_name'], 'observacion' => $_POST['observacion'], 'nit_usuario' => $_POST['nit_usuario'], 'email_usuario' => $_POST['email_usuario'], 'rate_id' => $_POST['rate_id'], 'payment_reference' => $_POST['payment_reference']),
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
