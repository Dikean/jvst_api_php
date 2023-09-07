<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    // Verifica si se proporcionó un ID en la solicitud
    if (isset($_POST['id'])) {
        // Obtén el ID de la consignación de la solicitud
        $consignmentId = $_POST['id'];

        // Define la consulta SQL para eliminar la consignación por su ID
        $sql = "DELETE FROM healthy_conditions WHERE id = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param("i", $consignmentId); // "i" indica un parámetro entero (ID)

        // Ejecuta la consulta para eliminar la consignación
        if ($stmt->execute()) {
            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'message' => 'Consignación eliminada correctamente.');
        } else {
            $header = "HTTP/1.1 422 Unprocessable Entity";
            $response = array('code' => '1', 'message' => 'No se pudo eliminar la consignación.');
        }
    } else {
        // ID no proporcionado en la solicitud
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Debes proporcionar un ID de consignación.');
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
