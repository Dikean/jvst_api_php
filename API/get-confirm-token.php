<?php
  include('../params/connectParams.php');
  include('../clases/conexion.php');

  include('../inlcude/headers.php');

  if($_GET['user_type'] == 'driver')
  {
      
      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL =>  $urlPrefix . 'first-driver-input.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('token' => $_GET['token'], 'email' => $_GET['email']),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
  }
  else
  {

    $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL =>  $urlPrefix . 'first-client-input.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('token' => $_GET['token'], 'email' => $_GET['email']),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
  }

  $info= (array) json_decode(stripslashes($response));



  if($info['code'] == '0')
  {
      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL =>  $urlPrefix . 'confirm-token.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('token' => $_GET['token'], 'email' => $_GET['email']),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json'
      ),
    ));

    $response = curl_exec($curl);

    $info= (array) json_decode(stripslashes($response));

    if($info['code'] == '0')
    {
        if($_GET['user_type'] == 'driver')
        {
            header('Location: '. $urlFront. 'wizarddriver?email=' . $_GET['email']);
        }
        else
        {
            header('Location: '. $urlFront. 'wizardcustomers?email=' . $_GET['email']);
        }
    }
    else
    {
         $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
         $response= array('code' => '1' , 'message' => $info['message']);
    }

    curl_close($curl);
  }
  else
  {
        $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
        $response= array('code' => '1' , 'message' => $info['message']);

  }

  header($header);
  echo json_encode($response);
?>