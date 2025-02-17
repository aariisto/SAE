<?php 
// Démarrer la session si elle n'est pas encore active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Supprimer toutes les variables de session
$_SESSION = [];

// Détruire complètement la session
session_destroy();

exit();
?>
