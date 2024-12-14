<?php

$response = []; // Crée un tableau pour la réponse

$id_client = (int)$_SESSION["id"];

$conn = Model::getModel();
$response = $conn->getOrder($id_client);  // Récupère les commandes ou réservations

$reservationContent = "";  // Initialiser le contenu des réservations

// Vérifier si la réponse contient des données
if (!empty($response)) {
    foreach ($response as $item) {
        $reservationContent = "
            <div class='card mb-3 historique-item'>
                <div class='card-body d-flex justify-content-between align-items-center'>
                    <div>
                        <h5 class='card-title'>{$item['station']}</h5>
                        <p class='card-text'>Date de Reservation : {$item['create_time']}</p>
                    </div>
                    <button class='btn btn-success btn-sm remove-btn' confirmationID='{$item['confirmationID']}' onclick='showQR(this)'>Afficher le QR Code</button>
                </div>
            </div>" . $reservationContent;
    }

    // Ajouter un titre au contenu des réservations
    $reservationContent = "<h3 class='section-title' style='margin-bottom: 20px;'>Historique de réservation</h3>" . $reservationContent;
} else {
    // Si aucune réservation n'est trouvée
    $reservationContent = "<h3 class='section-title' style='margin-bottom: 20px;'>Historique de réservation</h3><p style='text-align: center;'>Aucun historique de réservation disponible.</p>";
}


// Retourner le contenu au format JSON
echo json_encode(["resultat" => $reservationContent]);

?>

