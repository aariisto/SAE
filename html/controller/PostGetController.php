<?php
/**
 * Cette fonction gère les requêtes POST envoyées au contrôleur.
 * Elle vérifie que la requête est valide en s'assurant que :
 * - La méthode HTTP est POST
 * - Les en-têtes nécessaires (methode, csrf-token) sont présents
 * - La session utilisateur est active et valide
 * - Le jeton CSRF est vérifié
 * 
 * Si toutes les conditions sont remplies, elle inclut le fichier API correspondant à la méthode demandée.
 * Sinon, elle renvoie une réponse d'erreur appropriée.
 */

// Inclusion des fichiers nécessaires
require '/var/www/private/Model/Model.php'; // Classe Model
require '/var/www/private/functions/tokenCHECK.php'; // Fonction de vérification du jeton CSRF

// Démarre la session
session_start();

// Définit le type de contenu de la réponse en JSON
header('Content-Type: application/json');

// Récupère tous les en-têtes de la requête
$headers = getallheaders(); // csrf-token, methode

// Récupère les données envoyées dans le corps de la requête
$input = file_get_contents("php://input");

// Décode les données JSON reçues
$data = json_decode($input, true);

// Vérifie que la méthode est POST, que les en-têtes et les sessions sont valides, et que le jeton CSRF est vérifié
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($headers['methode']) && isset($headers['csrf-token']) 
        && isset($_SESSION['id']) && isset($_SESSION['token']) && verifyJWT($headers['csrf-token'],$_SESSION['id'])) {

    // Liste des méthodes autorisées
    $allowed_methods = ["post_search", "post_order","get_search","remove_search","get_reservation"]; // fichier API

    // Vérifie si la méthode fournie est dans la liste des méthodes autorisées
    if (in_array($headers['methode'], $allowed_methods)) {
        // Inclut le fichier correspondant à la méthode
        require_once '/var/www/private/api_files/' . $headers['methode'] . '.php';
        
    } else {
        // Réponse d'erreur si la méthode n'est pas autorisée
        echo json_encode(["error" => "Invalid method"]);
    }
} else {
    // Réponse d'erreur si la requête est invalide ou si la session a expiré
    http_response_code(401); // Erreur 401 : Non autorisé
    echo json_encode(["error" => "Votre session a expiré. Veuillez vous reconnecter.","error_code"=> "erreur code: 1","token"=> true]);
    exit();
}
?>