<?php

$id_client = (int)$_SESSION["id"];

$conn = Model::getModel();
$response = $conn->getSearch($id_client);

$searchContent = ""; // Initialiser $searchContent à une chaîne vide

if (! empty($response)) {
    foreach ($response as $val) {
        if ($val["resultat"] === 1) {
            $couleurSearch = "bi bi-check-circle-fill me-2 text-success";
            $typeSearch = "bi bi-geo-alt-fill text-success icon-large";
            if ($val["recherche"] === null) {
                $val["recherche"] = $val["station"];
                $typeSearch = "bi bi-bicycle icon-bike text-success";
            }
        } else {
            $couleurSearch = "bi bi-x-circle-fill me-2 text-danger";
            $typeSearch = "";
        }

        // Correction de la syntaxe pour l'insertion de variables dans une chaîne
        $searchContent = "
        <div class='card mb-3 historique-item'>
            <div class='card-body d-flex justify-content-between align-items-center'>
                <div>
                    <h5 class='card-title'>{$val['recherche']} <i class='{$couleurSearch}'></i><i class='{$typeSearch}'></i></h5>
                    <p class='card-text'>Date de Recherche : {$val['created_at']}</p>
                </div>
                <button class='btn btn-danger btn-sm remove-btn' data-id='{$val['id']}' onclick='removeSearch(this)'>Supprimer</button>
            </div>
        </div>" . $searchContent;
    }

    // Ajouter un titre à l'historique des recherches
    $searchContent = "<h3 class='section-title' style='margin-bottom: 20px;'>Historique de recherche</h3>" . $searchContent;
} else {
    $searchContent = "<h3 class='section-title' style='margin-bottom: 20px;'>Historique de recherche</h3><p style='text-align: center;'>Aucun historique de recherche disponible.</p>";
}

echo json_encode(["resultat" => $searchContent]); // Encode la réponse en JSON
?>
