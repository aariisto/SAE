<?php

$response = []; // Crée un tableau pour la réponse

if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"]) {

    $id_search=(int)$data["id_search"];

    $conn = Model::getModel();
    $response=$conn->removeSearch($id_search);
    
} else {
    $response=[
        'success' => false,
        'message' => "Données manquantes."
    ];
}

$token = generateToken($_SESSION['id']); //creation du token
$_SESSION['token'] = $token;//stocket ke token
echo json_encode($response); // Encode la réponse en JSON

?>
