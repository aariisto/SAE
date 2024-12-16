<?php

if (!isset($data['search'])) {
    echo json_encode(["error" => "Le champ 'serach' est requis."]);
    exit();
}

$response = []; // Crée un tableau pour la réponse

$search =$data['search'];
$serach_code=rawurlencode($search);
$affichageMessage="";

$url = "http://127.0.0.1:5000/search_station/${serach_code}";

// Utilisation de file_get_contents pour récupérer le contenu JSON
$response = file_get_contents($url);

// Vérifier si la récupération a réussi
if ($response !== false) {
    // Convertir le JSON en tableau PHP
    $data = json_decode($response, true);
    if ( isset($data["error"]) ){
        $station_id =0;
        $resultat =0;


    }else{
        $station_id =$data["station_id"];
        $resultat =1;
        
        
        if ($station_id === null) {
            $affichageMessage = "Localisation trouvée!";
        } else {
            $affichageMessage = "Station trouvée!";
        }
        $response=["lat"=>$data["lat"],"lon"=>$data["lon"],"message"=>$affichageMessage];
    }


} else {
    http_response_code(400); // Erreur 400 : Mauvaise requête
    echo json_encode(["error" => "Le serveur n'a pas pu traiter votre demande. Veuillez réessayer ultérieurement."]);
    exit();
}
    

    $id =(int)$_SESSION["id"];
    $search = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
    
    $search=preg_replace('/[  ]+/',' ',$search);
    
    $conn = Model::getModel(); // Obtenir la connexion à la base de données
    $conn->postSearch($id,$search,$station_id,$resultat);
    


echo json_encode($response); // Encode la réponse en JSON
?>
