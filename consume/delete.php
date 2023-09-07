<?php
error_reporting(0); // Ocultar mensajes de advertencia

include('../params/connectParams.php');
$curl = curl_init();

// Verificar si se proporcionó el parámetro "action" en la solicitud POST
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Construir la URL dinámica para la solicitud POST
    $apiBaseUrl = $urlAPI."API/"; // Cambiar según tu entorno
    $apiEndpoint = ""; // Aquí debes definir el nombre del archivo en /API/
    
    // Asignar la URL del archivo en función de la acción
    if ($action === 'delete_by_id-consignments') {
        $apiEndpoint = "delete-by_id-consigment_jvst.php";
    } elseif ($action === 'delete_by_id-docuemnts') {
        $apiEndpoint = "delete-by_id_documents_jvst.php";
    }elseif ($action === 'delete_by_id-contacts') {
        $apiEndpoint = "delete-by_id_contacts_jvst.php";
    }elseif ($action === 'delete_by_id-family_infos') {
        $apiEndpoint = "delete-by_id_family_infos_jvst.php";
    }elseif ($action === 'delete_by_id-references') {
        $apiEndpoint = "delete-by_id-references_jvst.php";
    }else {
        echo json_encode(array('error' => 'Invalid action.'));
        exit;
    }

    $url = $apiBaseUrl . $apiEndpoint;

    // Aquí puedes agregar más parámetros POST según sea necesario
    $postFields = array(
        'id' => $_POST['id'],
        // Agregar otros parámetros según corresponda
    );

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST", // Cambiar a "POST"
        CURLOPT_POSTFIELDS => http_build_query($postFields), // Enviar los parámetros POST
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo json_encode(array('error' => 'cURL Error: ' . $err));
    } else {
        header('Content-Type: application/json');
        echo ($response);
    }
} else {
    echo json_encode(array('error' => 'Action parameter is missing.'));
}
?>

