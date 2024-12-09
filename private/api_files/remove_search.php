<?php

$response = []; // Crée un tableau pour la réponse



    $id_search=(int)$data["id_search"];

    $conn = Model::getModel();
    $response=$conn->removeSearch($id_search);


echo json_encode($response); // Encode la réponse en JSON

?>
