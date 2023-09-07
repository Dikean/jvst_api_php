<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    // Verifica si se proporcionó un ID de usuario en la solicitud
    //el id es un del usuario
    if (isset($_POST['id'])) {
        // Obtén el ID de usuario de la solicitud
        $userId = $_POST['id'];

        // Define la consulta SQL para eliminar todos los documentos del usuario
        $sql = "DELETE FROM documents WHERE users_id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $userId); // "i" indica un parámetro entero (ID de usuario)

        // Ejecuta la consulta para eliminar todos los documentos del usuario
        if ($stmt->execute()) {
            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'message' => 'Todos los documentos del usuario han sido eliminados correctamente.');
        } else {
            $header = "HTTP/1.1 422 Unprocessable Entity";
            $response = array('code' => '1', 'message' => 'No se pudieron eliminar los documentos del usuario.');
        }
    } else {
        // ID de usuario no proporcionado en la solicitud
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

$conexion->desconectar(); // Nos desconectamos de la base de datos
?>
