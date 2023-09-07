<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Conectar a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Este endpoint solo acepta solicitudes POST

    if (
        isset($_POST['id']) &&
        isset($_POST['name']) &&
        isset($_POST['address']) &&
        isset($_POST['postal_code']) &&
        isset($_POST['city']) &&
        isset($_POST['country']) &&
        isset($_POST['about_me']) &&
        isset($_POST['phone']) &&
        trim($_POST['id']) !== '' && // Asegúrate de tener el id
        trim($_POST['name']) !== '' &&
        trim($_POST['address']) !== '' &&
        trim($_POST['postal_code']) !== '' &&
        trim($_POST['city']) !== '' &&
        trim($_POST['country']) !== '' &&
        trim($_POST['about_me']) !== '' &&
        trim($_POST['phone']) !== ''
    ) {
        // Todos los parámetros requeridos están presentes y no están vacíos

        // Obtener el id
        $id = $_POST['id'];

        // Definir la SQL query para actualizar los datos en la tabla "users"
        $sql = "UPDATE users SET
                name = ?,
                address = ?,
                postal_code = ?,
                city = ?,
                country = ?,
                about_me = ?,
                phone = ?
                WHERE id = ?";
        
        // Preparar la declaración SQL
        $stmt = $link->prepare($sql);

        // Vincular los parámetros a la declaración preparada con sus tipos de datos
        $stmt->bind_param(
            "sssssssi",
            $_POST['name'],
            $_POST['address'],
            $_POST['postal_code'],
            $_POST['city'],
            $_POST['country'],
            $_POST['about_me'],
            $_POST['phone'],
            $id
        );

        // Ejecutar la declaración para actualizar los datos
        $result = $stmt->execute();

        if ($result) {
            // Actualización exitosa
            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'message' => 'Data updated successfully.');
        } else {
            // Error en la actualización
            $header = "HTTP/1.1 422 Unprocessable Entity";
            $response = array('code' => '1', 'message' => $stmt->error);
        }
    } else {
        // Faltan parámetros requeridos o están vacíos
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Required fields cannot be empty.');
    }
} else {
    // Método HTTP no admitido
    $header = "HTTP/1.1 405 Method not supported";
    $response = array('code' => '1', 'message' => 'Method not supported for this endpoint.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); // Desconectar de la base de datos
?>
