<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    // Verifica si se proporcionó el parámetro 'name' en la solicitud POST
    if (isset($_POST['name'])) {
        $name = $_POST['name']; // Obtén el valor del parámetro 'name'

        // Construye la consulta SQL para filtrar los datos por 'name'
        $sql = "SELECT d.*, u.name AS user_name
                FROM contacts d
                JOIN users u ON d.users_id = u.id
                WHERE u.name = ?"; // Filtrar por el nombre de usuario

        $stmt = $link->prepare($sql);
        $stmt->bind_param("s", $name); // Enlaza el parámetro 'name' a la consulta
        $stmt->execute();
        $result = $stmt->get_result();

        $response = array();
        $data = array();

        while ($row = $result->fetch_assoc()) {
            // Agregar cada fila de datos a la respuesta
            $data[] = $row;
        }

        if (empty($data)) {
            $header = "HTTP/1.1 404 Not Found"; // Si no se encontraron datos
            $response = array('code' => '1', 'message' => 'No se encontraron datos para el nombre proporcionado.');
        } else {
            $header = "HTTP/1.1 200 OK";
            $response['code'] = '0';
            $response['data'] = $data;
        }
    } else {
        // El parámetro 'name' no se proporcionó en la solicitud POST
        $header = "HTTP/1.1 400 Bad Request";
        $response = array('code' => '1', 'message' => 'El parámetro "name" es obligatorio en la solicitud POST.');
    }

    // Establece el tipo de contenido de la respuesta en JSON
    header('Content-Type: application/json');
} else {
    // Método de solicitud no admitido
    $header = "HTTP/1.1 405 Method Not Allowed";
    $response = array('code' => '1', 'message' => 'Método no soportado para este endpoint.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); // Desconéctate de la base de datos


?>