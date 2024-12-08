<?php


if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"]) {

    $id_client=(int)$_SESSION["id"];
    
    $conn = Model::getModel();
    $response=$conn->getSearch($id_client);

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
