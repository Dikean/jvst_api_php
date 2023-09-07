<?php
include('../params/connectParams.php');
include('../clases/conexion.php');
include('../sendmails/sendMail.php');

$conexion = new conexion($host, $user, $password, $db, $port);
$link = $conexion->conectar(); // Connect to the database

include('../inlcude/headers.php');




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // This endpoint only accepts POST requests

    if (
        isset($_FILES['file']) &&
        isset($_POST['date']) &&
        isset($_POST['description']) &&
        isset($_POST['users_id']) &&
        trim($_POST['date']) !== '' &&
        trim($_POST['description']) !== '' &&
        trim($_POST['users_id']) !== ''
    ) {
        // All required parameters are present and not empty

       // Generar un nombre único para el archivo
       $fileName = uniqid() . '_' . $_FILES['file']['name'];
       $filePath = '../documents/' .$fileName;

       if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
        // Define the SQL query to insert data into the "documents" table
        $sql = "INSERT INTO documents (file, date, description, users_id) VALUES (?, ?, ?, ?)";
        
        // Prepare the SQL statement
        $stmt = $link->prepare($sql);

        // Bind the parameters to the prepared statement with their data types
        $stmt->bind_param(
            "sssi",
            $filePath, // Almacenar la ruta del archivo en lugar del contenido del archivo
            $_POST['date'],
            $_POST['description'],
            $_POST['users_id']
        );

        // Execute the statement to insert the data
        $result = $stmt->execute();

        if ($result) {
            // Successful insertion
            $header = "HTTP/1.1 200 OK";
            $response = array('code' => '0', 'message' => 'Data inserted successfully.');
        } else {
            // Error in insertion
            $header = "HTTP/1.1 422 Unprocessable Entity";
            $response = array('code' => '1', 'message' => $stmt->error);
        }
    } else {
        // Some required parameters are missing or empty
        $header = "HTTP/1.1 422 Unprocessable Entity";
        $response = array('code' => '1', 'message' => 'Required fields cannot be empty.');
    }
} else {
    // Unsupported HTTP method
    $header = "HTTP/1.1 405 Method not supported";
    $response = array('code' => '1', 'message' => 'Method not supported for this endpoint.');
}
}
header($header);
echo json_encode($response);

$conexion->desconectar(); // Disconnect from the database
?>
