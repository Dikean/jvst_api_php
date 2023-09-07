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
        isset($_POST['name_emergency_contact']) &&
        isset($_POST['email_emergency_contact']) &&
        isset($_POST['phone_emergency_contact']) &&
        isset($_POST['users_id']) &&
        trim($_POST['name_emergency_contact']) !== '' &&
        trim($_POST['email_emergency_contact']) !== '' &&
        trim($_POST['phone_emergency_contact']) !== '' &&
        trim($_POST['users_id']) !== ''
    ) {
        // All required parameters are present and not empty


        // Define the SQL query to insert data into the "contacts" table
        $sql = "INSERT INTO contacts (name_emergency_contact, email_emergency_contact, phone_emergency_contact, users_id) VALUES (?, ?, ?, ?)";
        
        // Prepare the SQL statement
        $stmt = $link->prepare($sql);

        // Bind the parameters to the prepared statement with their data types
        $stmt->bind_param(
            "sssi",
            $_POST['name_emergency_contact'],
            $_POST['email_emergency_contact'],
            $_POST['phone_emergency_contact'],
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

header($header);
echo json_encode($response);

$conexion->desconectar(); // Disconnect from the database
?>
