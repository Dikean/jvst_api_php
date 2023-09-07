<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Este endpoint solo acepta solicitudes GET

    // Obtener todos los datos de la tabla consignments con el nombre del usuario
    $sql = "SELECT c.*, u.name AS user_name
            FROM consignments c
            JOIN users u ON c.users_id = u.id"; // Realizar un JOIN para obtener el nombre del usuario
    $stmt = $link->prepare($sql);
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
        $response = array('code' => '1', 'message' => 'No se encontraron datos.');
    } else {
        $header = "HTTP/1.1 200 OK";
        $response['code'] = '0';
        $response['data'] = $data;
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

?>
