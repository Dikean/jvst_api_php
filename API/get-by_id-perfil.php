<?php

include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Conectar a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    // Verifica si se proporcionó un ID en la solicitud POST
    if (isset($_POST['id'])) {
        // Obtén el ID del usuario de la solicitud POST
        $userId = $_POST['id'];

        // Define la consulta SQL para obtener los datos del usuario por su ID
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $userId); // "i" indica un parámetro entero (ID)

        // Ejecuta la consulta para obtener los datos del usuario
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si se encontró un usuario con el ID proporcionado
        if ($result->num_rows == 1) {
            // Usuario encontrado, obtén los datos
            $userData = $result->fetch_assoc();

            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'data' => $userData);
        } else {
            // No se encontró un usuario con el ID proporcionado
            $header = "HTTP/1.1 404 Not Found";
            $response = array('code' => '1', 'message' => 'No se encontró un usuario con el ID proporcionado.');
        }
    } else {
        // ID no proporcionado en la solicitud POST
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Debes proporcionar un ID de usuario.');
    }

    // Establece el tipo de contenido de la respuesta como JSON
    header('Content-Type: application/json');
} else {
    // Método de solicitud no admitido
    $header = "HTTP/1.1 405 Method Not Allowed";
    $response = array('code' => '1', 'message' => 'Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); // Desconectar de la base de datos
?>
