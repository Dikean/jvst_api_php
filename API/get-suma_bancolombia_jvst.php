<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Nos conectamos a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Este endpoint solo acepta solicitudes GET

    // Define la consulta SQL para obtener la suma de los "amount" cuando el "bank" es "bancolombia"
    $sql = "SELECT IFNULL(SUM(amount), 0) AS total_amount FROM consignments WHERE bank = 'bancolombia'";

    $stmt = $link->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = array();
    $data = array();

    if ($row = $result->fetch_assoc()) {
        // Agrega la suma total a la respuesta
        $totalAmount = $row['total_amount'];
        $response['code'] = '0';
        $response['total_amount'] = $totalAmount;
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
