<?php
$headers = getallheaders();

$response = []; // Crée un tableau pour la réponse

if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"] ) {

    $id =(int)$_SESSION["id"];
    $resultat =(int)$data['resultat'];
    $station_id =(int)$data['station_id'];

    $search = htmlspecialchars($data['search'], ENT_QUOTES, 'UTF-8');
    $search=preg_replace('/[  ]+/',' ',$search);
    
    $conn = Model::getModel(); // Obtenir la connexion à la base de données
    $response=$conn->postSearch($id,$search,$station_id,$resultat);

} else {
    $response=[
        'success' => false,
        'message' => "Données manquantes."
    ];
}
$token = generateToken($_SESSION['id']); //creation du token
$_SESSION['token'] = $token;//stocket ke token
echo json_encode($response); // Encode la réponse en JSON
?>
