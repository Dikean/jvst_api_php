<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link= $conexion->conectar(); //nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{//Este endpoint es solo de tipo post


    if (isset($_POST['email']) && trim($_POST['email']) )
    { //se validan que los parámetros no vayan vacíos
        function generateRandomPassword($length = 7) {
            // Caracteres permitidos para la contraseña
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()-_';
        
            // Longitud de la cadena de caracteres permitidos
            $charLength = strlen($characters);
        
            // Inicializa la contraseña vacía
            $password = '';
        
            // Genera la contraseña aleatoria
            for ($i = 0; $i < $length; $i++) {
                // Selecciona un carácter aleatorio del conjunto de caracteres permitidos
                $randomChar = $characters[rand(0, $charLength - 1)];
        
                // Agrega el carácter a la contraseña
                $password .= $randomChar;
            }
        
            return $password;
        }
        
        // Uso de la función para generar una contraseña de 7 caracteres
        $randomPassword = generateRandomPassword(7);
        
        // Hash de la contraseña generada (puedes usar password_hash si lo necesitas)
        $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
        

        $sql = "UPDATE users SET password = ? WHERE email = ? "; 
        $stmt = $link->prepare($sql); //se corre la sentencia

        
        $stmt->bind_param("ss", $randomPassword, $_POST['email']);
        $result=$stmt->execute();

      // ...

if ($result) {
    // Restablecimiento de contraseña exitoso
    $header = "HTTP/1.1 200 Ok"; // Mensaje de salida con su respectivo JSON de respuesta
    $response = array('code' => '0', 'message' => 'Contraseña restablecida exitosamente.');

    // Envío de correo electrónico con la nueva contraseña
    $email = $_POST['email'];
    $subject = "Restablecimiento de contraseña";
    $body = "Su nueva contraseña es: $randomPassword"; 


    $sent = sendMail::send($email, $subject, $body);

    if ($mailResult) {
        // El correo electrónico se envió con éxito
        // Puedes agregar un mensaje adicional o lógica aquí si lo deseas
    } else {
        // Si hubo un error al enviar el correo electrónico, puedes manejarlo aquí
        // Por ejemplo, puedes agregar un mensaje de error a la respuesta JSON
        $response['message'] = 'Contraseña restablecida exitosamente, pero hubo un error al enviar el correo electrónico.';
    }
} else {
    // Restablecimiento de contraseña fallido
    $header = "HTTP/1.1 422 Unprocessable Entity"; // Mensaje de salida con su respectivo JSON de respuesta
    $response = array('code' => '1', 'message' => $stmt->error);
}
    }

}

header($header);
echo json_encode($response);

$conexion->desconectar(); // Nos desconectamos de la base de datos
