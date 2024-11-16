<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../log_backend/tokenGEN.php';
require '../log_backend/Model.php';
require 'get_token.php';
session_start();

$token=get_token();



$response = []; // Crée un tableau pour la réponse

if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"]) {
   
    $conn = Model::getModel();

    $id_client=(int)$_SESSION["id"];


    $sql="SELECT * FROM recherches WHERE client_id='$id_client'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $response[] = $row;
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
