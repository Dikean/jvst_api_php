<?php
include('../params/connectParams.php');
include('../clases/conexion.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos





    if (isset($_POST['nombre']) && trim($_POST['nombre'])!=''  && isset($_POST['identificacion']) && trim($_POST['identificacion'])!='' && isset($_POST['email']) && trim($_POST['email'])!='' && isset($_POST['picture']) && trim($_POST['picture'])!='' && isset($_POST['rol']) && trim($_POST['rol'])!='') { //se validan que los parámetros no vayan vacíos

        
        $nombre= $_POST['nombre'];
        $identificacion= $_POST['identificacion'];
        $email= $_POST['email'];
        $picture= $_POST['picture'];
        $rol= $_POST['rol'];
        
        
        $sql = "insert into postulados(identificacion,nombre,email,foto,rol)values(?,?,?,?,?)"; //se escribe la consulta para la sentencia preparada
         $stmt = $link->prepare($sql); //se corre la sentencia

         $stmt->bind_param("sssss", $nombre, $identificacion, $email, $picture, $rol);
        $result=$stmt->execute();
        
        if ($result) {
             //mensaje de salida  con su respectivo JSON de respuesta
            $response= array('response_code'=>'200','message'=>'Postulación realizada exitosamente.');
            echo json_encode($response);
        }
    } else {
        // en caso de que los campos vayan vacíos, esta es la respuesta que arroja el endpoint.
        $response= array('response_code'=>'422','message'=>'Unprocessable Entity, Los campos nombre,identificación,email,picture y rol no deben ir vacíos.');
        echo json_encode($response);
    }


$conexion->desconectar(); //nos desconectamos de la base de datos
