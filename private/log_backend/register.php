<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '/var/www/private/Model/Model.php';

session_start();
// Définir le niveau d'erreurs à afficher (E_ALL montre toutes les erreurs)

// Connexion à la base de données
$conn = Model::getModel();

if ($conn->connect_error) {
    die("Connection failed");
}

// Récupérer les données du formulaire

$username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
$username = $conn->escapeString($username);
$email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
$email = $conn->escapeString($email);
$password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
$password = $conn->escapeString($password);
$password = password_hash($password, PASSWORD_BCRYPT); // Hash du mot de passe

// Vérifier si l'utilisateur existe déjà
$sql = "SELECT * FROM users WHERE email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['register_error'] = true;
    header('Location: /register');
    
} else {
    $_SESSION['register_error'] = false;
    
    // Insérer l'utilisateur dans la base de données
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        
        $_SESSION['email'] = $email;
        $_SESSION['register'] = true;
        header('Location: /login');
        exit(); // Assurez-vous de quitter le script après la redirection
    } else {
        echo "Erreur : " . $conn->error;
    }
}

$conn->close();
?>
