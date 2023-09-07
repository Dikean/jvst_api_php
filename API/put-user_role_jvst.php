<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    // Verifica si se proporcionó un ID de usuario y un nuevo rol en la solicitud
    if (isset($_POST['id']) && isset($_POST['role'])) {
        // Obtén el ID de usuario y el nuevo rol de la solicitud
        $userId = $_POST['id'];
        $newRole = $_POST['role'];

        // Define la consulta SQL para actualizar el campo "role" del usuario por su ID
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("si", $newRole, $userId); // "si" indica parámetros de cadena (role) y entero (ID)

        // Ejecuta la consulta para actualizar el rol del usuario
        if ($stmt->execute()) {
            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'message' => 'Rol de usuario actualizado correctamente.');
        } else {
            $header = "HTTP/1.1 422 Unprocessable Entity";
            $response = array('code' => '1', 'message' => 'No se pudo actualizar el rol del usuario.');
        }
    } else {
        // ID de usuario o nuevo rol no proporcionado en la solicitud
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Debes proporcionar el ID de usuario y el nuevo rol.');
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
