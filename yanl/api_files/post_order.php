<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../log_backend/tokenGEN.php';
require '../log_backend/Model.php';
require 'get_token.php';
session_start();
header('Content-Type: application/json');

$input = file_get_contents("php://input"); // Récupère les données envoyées
$data = json_decode($input, true); // Décode les données JSON
$token=get_token();
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
        $sql = "INSERT INTO reservations (confirmationID,lat,lon,station,type,id_client,station_id) VALUES ('$confirmationID', '$lat', '$lon', '$station', '$type', '$id','$station_id')";
        
        if ($conn->query($sql) === TRUE ) {
            $response['success'] = true;
            $response['message'] = "Recherche insérée avec succès : client_id = $id, confirmationID = $confirmationID";
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
