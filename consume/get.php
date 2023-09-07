<?php
error_reporting(0); // Ocultar mensajes de advertencia

include('../params/connectParams.php');
$curl = curl_init();

// Verificar si se proporcionó el parámetro "action" en la solicitud GET
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Construir la URL dinámica para la solicitud GET
    $apiBaseUrl = $urlAPI."API/"; // Cambiar según tu entorno
    $apiEndpoint = ""; // Aquí debes definir el nombre del archivo en /API/
    
    // Asignar la URL del archivo en función de la acción
    if ($action === 'get_consignments') {
        $apiEndpoint = "get-all_consignments_jvst.php";
    } elseif ($action === 'get_contacts') {
        $apiEndpoint = "get-all_contacts_jvst.php";
    }  elseif ($action === 'get_documents') {
        $apiEndpoint = "get-all_documents_jvst.php";
    }elseif ($action === 'get_family_infos') {
        $apiEndpoint = "get-all_family_infos_jvst.php";
    }elseif ($action === 'get_healthy_conditions') {
        $apiEndpoint = "get-all_healthy_conditions_jvst.php";
    }elseif ($action === 'get_all_references') {
        $apiEndpoint = "get-all_references_jvst.php";
    }elseif ($action === 'get_all_users') {
        $apiEndpoint = "get-all_users.php";
    }else {
        echo json_encode(array('error' => 'Invalid action.'));
        exit;
    }

    $url = $apiBaseUrl . $apiEndpoint;

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
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
