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
        isset($_POST['alergia']) &&
        isset($_POST['enfermedad']) &&
        isset($_POST['prescripcionMedica']) &&
        isset($_POST['incapacidad']) &&
        isset($_POST['restriccionAlimenticia']) &&
        isset($_POST['users_id']) &&
        trim($_POST['alergia']) !== '' &&
        trim($_POST['enfermedad']) !== '' &&
        trim($_POST['prescripcionMedica']) !== '' &&
        trim($_POST['incapacidad']) !== '' &&
        trim($_POST['restriccionAlimenticia']) !== '' &&
        trim($_POST['users_id']) !== ''
    ) {
        // Todos los parámetros requeridos están presentes y no están vacíos

        // Verificar si ya existe un registro con el mismo users_id
        $users_id = $_POST['users_id'];
        $existingRecordQuery = "SELECT * FROM healthy_conditions WHERE users_id = ?";
        $stmt = $link->prepare($existingRecordQuery);
        $stmt->bind_param("i", $users_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Ya existe un registro con el mismo users_id, realizar la actualización
            $sql = "UPDATE healthy_conditions SET
                    alergia = ?,
                    enfermedad = ?,
                    prescripcionMedica = ?,
                    incapacidad = ?,
                    restriccionAlimenticia = ?
                    WHERE users_id = ?";
        } else {
            // No existe un registro con el mismo users_id, realizar la inserción
            $sql = "INSERT INTO healthy_conditions (alergia, enfermedad, prescripcionMedica, incapacidad, restriccionAlimenticia, users_id) VALUES (?, ?, ?, ?, ?, ?)";
        }

        // Preparar la declaración SQL
        $stmt = $link->prepare($sql);

        // Vincular los parámetros a la declaración preparada con sus tipos de datos
        $stmt->bind_param(
            "sssssi",
            $_POST['alergia'],
            $_POST['enfermedad'],
            $_POST['prescripcionMedica'],
            $_POST['incapacidad'],
            $_POST['restriccionAlimenticia'],
            $_POST['users_id']
        );

        // Ejecutar la declaración para insertar o actualizar los datos
        $result = $stmt->execute();

        if ($result) {
            // Inserción o actualización exitosa
            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'message' => 'Data inserted/updated successfully.');
        } else {
            // Error en la inserción o actualización
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
