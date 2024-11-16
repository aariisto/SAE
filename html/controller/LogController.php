<?php
// Vérifie que la méthode et les paramètres sont corrects
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['methode'])) {

    $allowed_methods = ["login", "register"]; // Liste des méthodes autorisées

    // Vérifie si la méthode fournie est dans la liste des méthodes autorisées
    if (in_array($_POST['methode'], $allowed_methods)) {
        require_once '/var/www/private/log_backend/' . $_POST['methode'] . '.php';
    } else {
        echo json_encode(["error" => "Invalid method"]);
    }
} else {
    // Réponse d'erreur si l'action est invalide
    echo json_encode(["error" => "Invalid request"]);
}
?>
