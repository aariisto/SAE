<?php

$response = []; // Crée un tableau pour la réponse

$confirmationID = htmlspecialchars($data['confirmationID'], ENT_QUOTES, 'UTF-8'); // Convertit les caractères spéciaux en entités HTML
$station_id = (int)$data['station_id'];
$type = htmlspecialchars($data['type'], ENT_QUOTES, 'UTF-8');
$station_id = (int)$data['station_id'];
$id = (int)$_SESSION["id"];

if ($type === "electric") {
    $type = 1;
} elseif ($type === "mechanical") {
    $type = 2;
} else {
    $type = 3;
}

try {
    $conn = Model::getModel(); // Obtenir la connexion à la base de données
    $response = $conn->postOrder($confirmationID, $type, $id, $station_id);
} catch (Exception $e) {
    http_response_code(500); // Erreur 500 : Probleme du serveur
    echo $e->getMessage();
    exit();
}

echo json_encode($response); // Encode la réponse en JSON

?>
