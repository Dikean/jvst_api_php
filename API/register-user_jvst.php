<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    if (
        isset($_POST['email']) && trim($_POST['email']) != '' &&
        isset($_POST['password']) && trim($_POST['password']) != '' &&
        isset($_POST['name']) && trim($_POST['name']) != '' &&
        isset($_POST['phone']) && trim($_POST['phone']) != ''&&
        isset($_POST['date']) && trim($_POST['date']) != ''
    ) {
        // Se validan que los parámetros no estén vacíos
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = "user";
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $date = $_POST['date'];

        // Generar un token aleatorio
    function generateToken($length = 32)
     {
     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
     $token = '';
      for ($i = 0; $i < $length; $i++) {
         $token .= $characters[rand(0, strlen($characters) - 1)];
     }
    
     return $token;
     }

     // Uso de la función para generar un token
     $token = generateToken(); // Genera un token de 32 caracteres


        // Generar un token (puedes utilizar una biblioteca de generación de tokens o generar uno manualmente)
        $token = generateToken(); // Debes implementar esta función para generar un token seguro

        // Hash de la contraseña antes de almacenarla en la base de datos
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Verificar si el usuario ya existe
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El usuario ya existe
            header("HTTP/1.1 200 OK");
            $response = array('code' => '1', 'message' => 'usuario ya se encuentra registrado.');
        } else {
            // El usuario no existe, por lo que insertamos el nuevo usuario con la contraseña hash y el token
            $insertSql = "INSERT INTO users (email, password, role, name, phone, date, token) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $link->prepare($insertSql);
            $insertStmt->bind_param("sssssss", $email, $hashedPassword, $role, $name, $phone, $date, $token);
        
            if ($insertStmt->execute()) {
                // Usuario insertado exitosamente
                $user_id = $insertStmt->insert_id; // Obtener el ID del usuario insertado directamente
                header("HTTP/1.1 201 Created");
                $response = array(
                    'code' => '0',
                    'message' => 'usuario registrado exitosamente.',
                    'id' => $user_id,
                    'token' => $token,
                    'email' => $email,
                    'name' => $name,
                    'phone' => $phone,
                    'role' => $role
                );
            } else {
                // Error durante la inserción del usuario
                header("HTTP/1.1 500 Internal Server Error");
                $response = array('code' => '2', 'message' => 'Error interno del servidor.');
            }
        }
        
        //aparte  
    } else {
        // Falta alguno de los parámetros o están vacíos
        header("HTTP/1.1 422 Unprocessable Entity");
        $response = array('code' => '1', 'message' => 'Unprocessable Entity, Todos los campos deben estar completos.');
    }
} else {
    // Método de solicitud no admitido
    header("HTTP/1.1 405 Method not supported");
    $response = array('code' => '1', 'message' => 'Método no soportado para este endpoint.');
}

header("Content-Type: application/json");
echo json_encode($response, JSON_PRETTY_PRINT);
$conexion->desconectar(); // Nos desconectamos de la base de datos
 
?>
