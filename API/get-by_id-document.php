<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes GET

    // Verifica si se proporcionó un nombre en la solicitud
    if (isset($_POST['id'])) {
        // Obtén el nombre de usuario de la solicitud
        $requestedName = $_POST['id'];

        // Define la consulta SQL para obtener el ID del usuario a partir de su nombre
        $sql = "SELECT id FROM users WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $requestedName); // "s" indica un parámetro de cadena (nombre)

        // Ejecuta la consulta para obtener el ID del usuario
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si se encontró un usuario con el nombre proporcionado
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $userId = $row['id'];

            // Define la consulta SQL para obtener las consignaciones del usuario por su ID
            $sql = "SELECT * FROM documents WHERE users_id = ?";
            $stmt = $link->prepare($sql);
            $stmt->bind_param("i", $userId); // "i" indica un parámetro entero (ID)

            // Ejecuta la consulta para obtener las consignaciones
            $stmt->execute();
            $result = $stmt->get_result();

            $response = array();
            $data = array();

            while ($row = $result->fetch_assoc()) {
                // Agregar cada fila de consignación a la respuesta
                $data[] = $row;
            }

            if (empty($data)) {
                $header = "HTTP/1.1 404 Not Found"; // Si no se encontraron consignaciones
                $response = array('code' => '1', 'message' => 'No se encontraron documentos para el usuario.');
            } else {
                $header = "HTTP/1.1 200 OK";
                $response['code'] = '0';
                $response['data'] = $data;
            }
        } else {
            // No se encontró un usuario con el nombre proporcionado
            $header = "HTTP/1.1 404 Not Found";
            $response = array('code' => '1', 'message' => 'No se encontró un usuario con el nombre proporcionado.');
        }
    } else {
        // Nombre no proporcionado en la solicitud
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Debes proporcionar un nombre de usuario.');
    }
    
     // Set the response content type to JSON
     header('Content-Type: application/json');
} else {
    // Método de solicitud no admitido
    $header = "HTTP/1.1 405 Method Not Allowed";
    $response = array('code' => '1', 'message' => 'Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); // Nos desconectamos de la base de datos
