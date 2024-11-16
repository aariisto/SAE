<?php

// Fonction pour générer un token
function generateToken($userId) {
    // Clé secrète pour signer le token
    $key = "didine_cacon_16"; 

    // Générer une valeur aléatoire pour renforcer l'unicité
    $randomValue = bin2hex(random_bytes(8)); // Générer 16 caractères hexadécimaux

    // Créer le payload
    $payload = [
        'userId' => $userId,
        'random' => $randomValue, // Ajouter la valeur aléatoire au payload
    ];

    // Convertir le payload en JSON
    $payloadJson = json_encode($payload);

    // Encoder le payload en base64
    $base64Payload = base64_encode($payloadJson);

    // Signer le token avec la clé secrète
    $signature = hash_hmac('sha256', $base64Payload, $key);

    // Créer le token final
    $token = $base64Payload . '.' . $signature;
    $conn = Model::getModel();
    $id = (int)$_SESSION['id'];
    $sql="UPDATE users SET token = '$token' WHERE id='" . $_SESSION['id'] . "'";
    if ($conn->query($sql) !== TRUE) {
        echo "Erreur ";
    } 
    

    return $token;
}
?>
