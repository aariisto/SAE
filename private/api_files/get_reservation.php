<?php



$response = []; // Crée un tableau pour la réponse

if (isset($_SESSION["id"]) && isset($_SESSION["token"]) && $token === $_SESSION["token"]) {
   
    $conn = Model::getModel();

    $id_client=(int)$_SESSION["id"];


    $sql="SELECT * FROM reservations_vue WHERE client_id='$id_client'";
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
