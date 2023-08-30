<?php
  include('../params/connectParams.php');
  include('../clases/conexion.php');

  include('../inlcude/headers.php');


  if ($_SERVER['REQUEST_METHOD'] == 'POST') 
  {//Este endpoint es solo de tipo post
      
      $curl = curl_init();

      curl_setopt_array($curl, array(
      CURLOPT_URL =>  $urlPrefix . 'resend-token.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('email' => $_GET['email']),
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json'
      ),
    ));

    $response = curl_exec($curl);

    

      var_dump($response);exit();

      curl_close($curl);
      $info= (array) json_decode(stripslashes($response));

      if($info['code'] == '0')
      {
        $header = "HTTP/1.1 200 Ok"; //mensaje de salida  con su respectivo JSON de respuesta
        $response= array('code' => '0' , 'message' => 'Correo enviado exitosamente.');
      }
      else
      {
           $header = "HTTP/1.1 422 Unprocessable Entity"; //mensaje de salida  con su respectivo JSON de respuesta
           $response= array('code' => '1' , 'message' => $info['message']);
      }
     
  }
  else{
    $header = "HTTP/1.1 422 Unprocessable Entity"; // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
    $response = array('code' => '1','message' => 'Método no soportado para este endpoint.');
  } 

  header($header);
  echo json_encode($response);   
?>