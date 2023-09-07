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
        isset($_POST['name_family']) &&
        isset($_POST['document_family']) &&
        isset($_POST['relationship_family']) &&
        isset($_POST['birthdate_family']) &&
        isset($_POST['age_family']) &&
        isset($_POST['users_id']) &&
        trim($_POST['name_family']) !== '' &&
        trim($_POST['document_family']) !== '' &&
        trim($_POST['relationship_family']) !== '' &&
        trim($_POST['birthdate_family']) !== '' &&
        trim($_POST['age_family']) !== '' &&
        trim($_POST['users_id']) !== ''
    ) {
        // All required parameters are present and not empty

        // Get the current date and time
        $date = date('Y-m-d H:i:s');

        // Define the SQL query to insert data into the "family_infos" table
        $sql = "INSERT INTO family_infos (name_family, document_family, relationship_family, birthdate_family, age_family, users_id) VALUES (?, ?, ?, ?, ?, ?)";
        
        // Prepare the SQL statement
        $stmt = $link->prepare($sql);

        // Bind the parameters to the prepared statement with their data types
        $stmt->bind_param(
            "sssssi",
            $_POST['name_family'],
            $_POST['document_family'],
            $_POST['relationship_family'],
            $_POST['birthdate_family'],
            $_POST['age_family'],
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
