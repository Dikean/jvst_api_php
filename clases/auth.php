<?php
/*Esta clase es para manejar las autenticaciones de los usuarios en el sistema*/
class auth
{
 protected $respuesta;
 protected $url;
 protected $email;
 protected $urlPicture='https://pix.uniminuto.edu/pix/';

   public function usuario($param1,$param2,$tipoUsuario)//función para autenticar a un colaborador, se recibe como parámetro el correo y la contraseña
   { 
      //Los graduados se loguean con la cédula y la fecha de nacimiento
      //Los activos se loguean con el usuario y la contraseña
    $curl = curl_init();
      $login=$param1.'/'.$param2; //La url se consume con la combinación de usuario y password
      //validar si el que se quiere autenticar es un colaborador o un estudiante, según el valor de la variable tipoUsuario
      if($tipoUsuario==1) //en el caso de estudiantes y demás colaboradores
      {
        $this->url = 'https://zonaestudiantes.uniminuto.edu/ServiciosAPI/API/LDAP/AutenticarUsuario/'.$login.'/ACTIVOS';
      }
      else
      {  //en el caso de que sea un graduado
        $this->url = 'https://webapi.uniminuto.edu/API/LDAP/AutenticarUsuario/'.$login.'/GRADUADO';
      }
      curl_setopt_array($curl, array(
        CURLOPT_URL => $this->url,
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
$info= (array) json_decode(stripslashes($response));

if(property_exists((object)$info,'Pager'))
      {//En el caso de que el usuario exista, se setea el mensaje de bienvenida
        //$this->respuesta= 'Bienvenido(a) '.$info['FirstName'].' '.$info['LastName'];
        if($info['Descripcion']=='GRADUADO' || $info['Descripcion']=='ESTUDIANTE' ){
          $identificacion = $info['Cn'];
        }
        else{
          $identificacion = $info['Pager'];
        }

        //para retornar el e-mail;
        $mail= $this->getEmail($param1,$info['Mail']);

          $url = $this->getUrl($identificacion);
          


  $this->respuesta= array('nombre'=>$info['FirstName'].' '.$info['LastName'],'identificacion'=>$identificacion,'email'=>$mail,'pictureUrl'=>$url,'rol'=>$info['Descripcion']);

}
else
{
 if(property_exists((object)$info,'Mensaje'))
 {
         $this->respuesta= $info['Mensaje'];//Si el usuario no existe, se setea el mensaje diciendo que el usuario y/o password son inválidos
       }
     }
     return json_encode($this->respuesta);
}

    public function getEmail($parametroLogueo,$parametroEndPoint){//esto es para devolver el email cuando un usuario se loguea 
     if (filter_var($parametroLogueo, FILTER_VALIDATE_EMAIL)) 
     {
       $this->email= $parametroLogueo;
     }
     else
     {
       if($parametroEndPoint ==null){
         $this->email='';
       } 
       else{
        $this->email = $parametroEndPoint;
      }
    }
    return $this->email;
  }

  public function getUrl($id){//Este método es para obtener la url que devuelve la foto del candidato logueado
    $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://comunidad.uniminuto.edu/api/select/index.php/image/'.$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'accept: application/json'
          ),
        ));

      $response = curl_exec($curl);

curl_close($curl);
$info= (array) json_decode(stripslashes($response));
return $info['img'];
}

public function getProgramas($id){
  $array=[];
  $curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://zonaestudiantes.uniminuto.edu/ServiciosAPI/API/BannerEstudiante/ConsultarProgramas/'.$id,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'accept: application/json'
  ),
));
$response = curl_exec($curl);

  curl_close($curl);
  $info= (array) json_decode(stripslashes($response));
  foreach ($info as  $value) {
    if($value->NivelAcademicoID != 'CL' && $value->NivelAcademicoID != 'DP' ){
       array_push($array,array('Id'=>$value->Id,'programaAcademicoDescripcion'=>$value->programaAcademicoDescripcion,'programaAcademicoId'=>$value->programaAcademicoId,'Facultad'=>$value->Facultad,'Modalidad'=>$value->Modalidad,'Semestre'=>$value->Semestre,'SedeDescripcion'=>$value->SedeDescripcion,'sedeId'=>$value->sedeId,'RectoriaDescripcion'=>$value->RectoriaDescripcion,'PeriodoPrograma'=>$value->PeriodoPrograma,'NivelAcademicoID'=>$value->NivelAcademicoID,'NivelAcademicoDescripcion'=>$value->NivelAcademicoDescripcion));
    }
  }

  print_r(json_encode($array ));


}


}  
?> 