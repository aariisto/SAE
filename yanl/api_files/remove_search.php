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

    $id_search=(int)$data["id_search"];

    $conn = Model::getModel();
    $sql="DELETE FROM recherches WHERE id = '$id_search'";

    if ($conn->query($sql) === TRUE ) {
        $response['success'] = true;
        $response['message'] = "Suppression avec succés";
    } else {
        $response['success'] = false;
        $response['message'] = "Erreur lors de la supression"; // Affiche l'erreur SQL
    }

    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['message'] = "ERROR";
}

$token = generateToken($_SESSION['id']); //creation du token
$_SESSION['token'] = $token;//stocket ke token
echo json_encode($response); // Encode la réponse en JSON

?>
