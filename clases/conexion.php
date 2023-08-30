<?php
  /*Esta clase es para manejar las conexiones a las bases de datos*/
  class conexion{
  	 protected $host;
  	 protected $user;
  	 protected $password;
  	 protected $db;
  	 protected $port;
  	 protected $link;

  	 public function __construct($host, $user, $password,$db,$port){
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->db=$db;
        $this->port=$port;
    }

    public function conectar(){
    	$this->link = mysqli_connect($this->host,$this->user,$this->password,$this->db,$this->port);
    	if($this->link){
          $response = $this->link;
    	}
    	else{
          $response = 'Error en el proceso de conexion.';   
    	}
      return $response;
    }

    public function desconectar()
    {
      $this->link->close();
    }


  }
?>