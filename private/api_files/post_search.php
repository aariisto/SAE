<?php
$headers = getallheaders();

$response = []; // Crée un tableau pour la réponse

if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"] ) {
    $id = (int)$_SESSION["id"];
    $resultat = (int)$data['resultat'];
    $station_id = (int)$data['station_id'];
    $search = htmlspecialchars($data['search'], ENT_QUOTES, 'UTF-8');
    
    
    $conn = Model::getModel(); // Obtenir la connexion à la base de données
    $search = $conn->escapeString($search);
    $search=preg_replace('/[  ]+/',' ',$search);
    
    if (!$conn) {
        $response['success'] = false;
        $response['message'] = "Échec de la connexion à la base de données";
    } else {
        if( $station_id === 0){
            $sql = "INSERT INTO recherches (client_id, recherche, resultat) VALUES ('$id', '$search', '$resultat')";
        }
        else{
            $sql = "INSERT INTO recherches (client_id, recherche, resultat,station_id) VALUES ('$id', '$search', '$resultat','$station_id')";
        }
        if ($conn->query($sql) === TRUE ) {
            $response['success'] = true;
            $response['message'] = "Recherche insérée avec succès : client_id = $id, recherche = $search";
        } else {
            $response['success'] = false;
            $response['message'] = "Erreur lors de l'insertion" ; // Affiche l'erreur SQL
        }

       
       
    }
} else {
    $response['success'] = false;
    $response['message'] = "Données manquantes.";
}
$token = generateToken($_SESSION['id']); //creation du token
$_SESSION['token'] = $token;//stocket ke token
echo json_encode($response); // Encode la réponse en JSON
?>
