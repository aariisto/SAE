<?php


$response = []; // Crée un tableau pour la réponse

if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"]) {
    $confirmationID = $data['confirmationID'];
   
    $station= $data['station'];
    $type = $data['type'];
    
    $station_id= $data['station_id'];
   


  
    $conn = Model::getModel(); // Obtenir la connexion à la base de données

    $confirmationID = htmlspecialchars($data['confirmationID'], ENT_QUOTES, 'UTF-8'); // Convertit les caractères spéciaux en entités HTML
    $lat = (double)$data['lat']; // Pas besoin de htmlspecialchars pour les nombres
    $lon = (double)$data['lon']; // Pas besoin de htmlspecialchars pour les nombres
    $station_id= (int)$data['station_id'];
    $station = htmlspecialchars($data['station'], ENT_QUOTES, 'UTF-8'); // Pour le champ station
    $type = htmlspecialchars($data['type'], ENT_QUOTES, 'UTF-8'); // Pour le type
    $id = (int)$_SESSION["id"]; // Pas besoin de htmlspecialchars pour les entiers
    
    // Échapper les chaînes de caractères avant l'insertion
    $confirmationID = $conn->escapeString($confirmationID);
    $station = $conn->escapeString($station);
    $type = $conn->escapeString($type);
    
    if (!$conn) {
        $response['success'] = false;
        $response['message'] = "Échec de la connexion à la base de données: ";
    } else {
        // Prépare et exécute la requête SQL pour insérer les données
        $sql = "INSERT INTO reservations (confirmationID,lat,lon,station,type,client_id,station_id) VALUES ('$confirmationID', '$lat', '$lon', '$station', '$type', '$id','$station_id')";
        
        if ($conn->query($sql) === TRUE ) {
            $response['success'] = true;
            $response['message'] = "order insérée avec succès : client_id = $id, confirmationID = $confirmationID";
        } else {
            $response['success'] = false;
            $response['message'] = "Erreur lors de l'insertion"; // Affiche l'erreur SQL
        }

       
    
    }
} else {
    $response['success'] = false;
    $response['message'] = "ERROR";
}
$token = generateToken($_SESSION['id']); //creation du token
$_SESSION['token'] = $token;//stocket ke token
echo json_encode($response); // Encode la réponse en JSON

?>
