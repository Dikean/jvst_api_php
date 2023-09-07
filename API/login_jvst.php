<?php
error_reporting(0); // Desactiva la notificación de errores y advertencias

include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    if (isset($_POST['email']) && trim($_POST['email']) != '' && isset($_POST['password']) && trim($_POST['password']) != '') {
        // Se validan que los parámetros no estén vacíos
        $email = $_POST['email'];
        $password = $_POST['password'];

       // Verificar si el usuario existe
        $sql = "SELECT id, name, phone, email, password, role, token FROM users WHERE email = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El usuario existe, obtenemos la contraseña hasheada almacenada
            $row = $result->fetch_assoc();
            $storedPassword = $row['password'];

            // Verificamos la contraseña ingresada con la almacenada
            if (password_verify($password, $storedPassword)) {
                // Contraseña válida, inicio de sesión exitoso
                header("HTTP/1.1 200 OK");
                $response = array(
                    'code' => '0',
                    'message' => 'Inicio de sesión exitoso.',
                    'phone'=> $row['phone'],
                    'token'=> $row['token'],
                    'name'=> $row['name'],
                    'id' => $row['id'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                );
            } else {
                // Contraseña incorrecta
                header("HTTP/1.1 200 OK");
                $response = array('code' => '1', 'message' => 'Contraseña incorrecta.');
            }
        } else {
            // El usuario no existe
            header("HTTP/1.1 200 OK");
            $response = array('code' => '1', 'message' => 'Usuario no encontrado.');
        }

    } else {
        // Falta el parámetro de correo electrónico o contraseña o están vacíos
        header("HTTP/1.1 422 Unprocessable Entity");
        $response = array('code' => '1', 'message' => 'Unprocessable Entity, El campo email y contraseña no pueden ir vacíos.');
    }
} else {
    // Método de solicitud no admitido
    header("HTTP/1.1 405 Method not supported");
    $response = array('code' => '1', 'message' => 'Método no soportado para este endpoint.');
}

header("Content-Type: application/json");
echo json_encode($response);
$conexion->desconectar(); // Nos desconectamos de la base de datos
?>
