
<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'tokenGEN.php';
require 'Model.php';
session_start();

// Connexion à la base de données
$conn = Model::getModel();

if ($conn->connect_error) {
    die("Connection failed");
}

// Récupérer les données du formulaire


$email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
$email = $conn->escapeString($email);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
$password = $conn->escapeString($password);

// Chercher l'utilisateur dans la base de données
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Vérifier si le mot de passe est correct
    if (password_verify($password, $user['password'])) {
        // Démarrer une session et stocker l'utilisateur dans la session
        
        $_SESSION['email'] = $user['email'];
        $_SESSION['id'] = $user['id'];
        $_SESSION['nom'] = $user['username'];
        $_SESSION['createTime'] = $user['created_at'];// Stocker le nom d'utilisateur dans la session
        $token = generateToken($_SESSION['id']); //creation du token
        $_SESSION['token'] = $token;//stocket ke token

        // Redirection vers la page protégée après connexion
        header('Location: /index.php');
        exit(); // Assurez-vous de quitter le script après la redirection
    } else {
        $_SESSION['psw_error'] = true;
        header('Location: /login.php');
        exit(); // Assurez-vous de quitter le script après la redirection
    }
} else {
    $_SESSION['mail_error'] = true;
    header('Location: /login.php');
    exit(); // Assurez-vous de quitter le script après la redirection
   
}

$conn->close();
?>
