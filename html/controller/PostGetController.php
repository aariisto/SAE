<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '/var/www/private/functions/tokenGEN.php';
require '/var/www/private/Model/Model.php';
require '/var/www/private/functions/get_token.php';
session_start();
header('Content-Type: application/json');
$token=get_token();
$input = file_get_contents("php://input"); // Récupère les données envoyées
$data = json_decode($input, true); // Décode les données JSON
$headers = getallheaders();
// Vérifie que la méthode et les paramètres sont corrects
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['methode'])) {

    $allowed_methods = ["post_search", "post_order","get_search","remove_search","get_reservation"]; // Liste des méthodes autorisées

    // Vérifie si la méthode fournie est dans la liste des méthodes autorisées
    if (in_array($data['methode'], $allowed_methods)) {
        require_once '/var/www/private/api_files/' . $data['methode'] . '.php';
        
    } else {
        echo json_encode(["error" => "Invalid method"]);
    }
} else {
    // Réponse d'erreur si l'action est invalide
    echo json_encode(["error" => "Invalid request"]);
}
?>
