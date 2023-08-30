<?php
  /*Esta clase es para manejar las conexiones a las bases de datos*/
  class consumeResoruces{
  	 protected $route;
  	 protected $result;
  	
  	 public function __construct($route){
        $this->route = $route;
     }

    public function getResource(){
    	  $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->route,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $result = (array) json_decode(stripslashes($response));

        return $result;
    }

    


  }
?>