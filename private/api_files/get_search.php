<?php

$id_client = (int)$_SESSION["id"];

try {
$conn = Model::getModel();
$response = $conn->getSearch($id_client);
} catch (Exception $e) {
    http_response_code(500); // Erreur 500 : Probleme du serveur
    echo $e->getMessage();
    exit();
}
$searchContent = ""; // Initialiser $searchContent à une chaîne vide

if (! empty($response)) {
    foreach ($response as $val) {
        // Définition des icônes par défaut
        $couleurSearch = "bi bi-x-circle-fill me-2 text-danger"; // Par défaut en rouge (échec)
        $typeSearch = ""; // Vide par défaut
    
        // Si le résultat est positif (1), on change la couleur et le type d'icône
        if ($val["resultat"] === 1) {
            $couleurSearch = "bi bi-check-circle-fill me-2 text-success";
            $typeSearch = "bi bi-geo-alt-fill icon-large";
    
            // Si la recherche est vide, utiliser la station et changer l'icône
            if (empty($val["recherche"])) {
                $val["recherche"] = $val["station"];
                $typeSearch = "bi bi-bicycle icon-bike";
            }
        }
    
        // Construit le contenu HTML de l'historique de recherche
        $searchContent .= "
        <div class='card mb-3 historique-item'>
            <div class='card-body d-flex justify-content-between align-items-center'>
                <div>
                    <h5 class='card-title'>
                        {$val['recherche']}
                        <i class='{$couleurSearch}'></i>
                        <i class='{$typeSearch}'></i>
                    </h5>
                    <p class='card-text'>Date de Recherche : {$val['created_at']}</p>
                </div>
                <button class='btn btn-danger btn-sm remove-btn' data-id='{$val['id']}' onclick='removeSearch(this)'>
                    Supprimer
                </button>
            </div>
        </div>";
    }

    // Ajouter un titre à l'historique des recherches
    $searchContent = "<h3 class='section-title' style='margin-bottom: 20px;'>Historique de recherche</h3>" . $searchContent;
} else {
    $searchContent = "<h3 class='section-title' style='margin-bottom: 20px;'>Historique de recherche</h3><p style='text-align: center;'>Aucun historique de recherche disponible.</p>";
}

echo json_encode(["resultat" => $searchContent]); // Encode la réponse en JSON
?>
