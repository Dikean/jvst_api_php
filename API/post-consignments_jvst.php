<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Conectar a la base de datos

include('../inlcude/headers.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Esta ruta solo acepta solicitudes POST

    if (
        isset($_POST['date']) &&
        isset($_POST['amount']) &&
        isset($_POST['bank']) &&
        isset($_FILES['voucher']) &&
        isset($_POST['users_id']) &&
        trim($_POST['date']) !== '' &&
        trim($_POST['amount']) !== '' &&
        trim($_POST['bank']) !== '' &&
        trim($_POST['users_id']) !== ''
    ) {
        // Todos los parámetros requeridos están presentes y no están vacíos

        // Generar un nombre único para el archivo
        $fileName = uniqid() . '_' . $_FILES['voucher']['name'];
        $filePath = '../consignments/' .$fileName;

        // Mover el archivo cargado a la carpeta "consignments" con el nombre único
        if (move_uploaded_file($_FILES['voucher']['tmp_name'], $filePath)) {
            // Definir la consulta SQL para insertar datos en la tabla "consignments"
            $sql = "INSERT INTO consignments (date, amount, bank, voucher, users_id) VALUES (?, ?, ?, ?, ?)";
            
            // Preparar la declaración SQL
            $stmt = $link->prepare($sql);

            // Enlazar los parámetros a la declaración preparada
            $stmt->bind_param(
                "sdssi",
                $_POST['date'],
                $_POST['amount'],
                $_POST['bank'],
                $filePath, // Almacenar la ruta del archivo en lugar del contenido del archivo
                $_POST['users_id']
            );

            // Ejecutar la declaración para insertar los datos
            $result = $stmt->execute();

            if ($result) {
                // Inserción exitosa
                $header = "HTTP/1.1 200 OK";
                $response = array('code' => '0', 'message' => 'Datos insertados correctamente.');
            } else {
                // Error en la inserción
                $header = "HTTP/1.1 422 Unprocessable Entity";
                $response = array('code' => '1', 'message' => $stmt->error);
            }
        } else {
            // Error al mover el archivo
            $header = "HTTP/1.1 422 Unprocessable Entity";
            $response = array('code' => '1', 'message' => 'Error al cargar el archivo.');
        }
    } else {
        // Algunos parámetros requeridos faltan o están vacíos
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Los campos requeridos no pueden estar vacíos.');
    }
} else {
    // Método HTTP no admitido
    $header = "HTTP/1.1 405 Method not supported";
    $response = array('code' => '1', 'message' => 'Método no admitido para este punto final.');
}

header($header);
echo json_encode($response);

$conexion->desconectar(); // Desconectar de la base de datos
?>
