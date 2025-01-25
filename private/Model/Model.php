<?php
class Model {
    private $bd; // Attribut privé contenant l'objet mysqli
    private static $instance = null; // Attribut qui contiendra l'unique instance du modèle

    /**
     * Constructeur créant l'objet mysqli et l'affectant à $bd
     */
    private function __construct() {
          // Activer le mode exceptions pour mysqli
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // Connexion à la base de données
        $this->bd = new mysqli('localhost', 'root', 'yannel', 'user');
        $this->bd->set_charset('utf8'); // Définir l'encodage
    } catch (mysqli_sql_exception $e) {
        // Gérer l'exception et renvoyer un JSON personnalisé
        throw new Exception(json_encode([
            "error" => "Le serveur n'a pas pu traiter votre demande. Veuillez réessayer ultérieurement.",
            "error_code" => "erreur code: 3_0",
            "token" => false
        ]));
    }
    }

    /**
     * Méthode permettant de récupérer l'instance de la classe Model
     */
    public static function getModel() {
        // Si la classe n'a pas encore été instanciée
        if (self::$instance === null) {
            self::$instance = new self(); // On l'instancie
        }
        return self::$instance; // On retourne l'instance
    }

    // Méthode pour exécuter une requête et retourner le résultat
    public function query($sql) {
        return $this->bd->query($sql);
    }


    public function postSearch($id,$search,$station_id,$resultat){

        if ($station_id === 0) {
            $sql = "INSERT INTO recherches (client_id, recherche, resultat) VALUES (?, ?, ?)";
        } else {
            $sql = "INSERT INTO recherches (client_id, resultat, station_id) VALUES (?, ?, ?)";
        }

        try {
            $stmt = $this->bd->prepare($sql);
        } catch (mysqli_sql_exception $e) {
            throw new Exception(json_encode([
            "error" => "Le serveur n'a pas pu traiter votre demande. Veuillez réessayer ultérieurement.",
            "error_code" => "erreur code: 3_1",
            "token" => false
            ]));
            exit();
        }

       
            if ($station_id === 0) {
            $stmt->bind_param("isi", $id, $search, $resultat); // "i" pour entier, "s" pour chaîne
            } else {
            $stmt->bind_param("iii", $id, $resultat, $station_id); // "i" pour entier, "s" pour chaîne
            }
       
        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            throw new Exception(json_encode([
            "error" => "Le serveur n'a pas pu traiter votre demande. Veuillez réessayer ultérieurement.",
            "error_code" => "erreur code: 3_2",
            "token" => false
            ]));
            exit();
        }

        // Retourner le succès
        return [
            'success' => true,
            'message' => "Recherche insérée avec succès : client_id = $id, recherche = $search"
        ];
        
    }

    public function postOrder($confirmationID, $type, $id, $station_id){

        $sql = "INSERT INTO reservations (confirmationID, id_velo, client_id, station_id) VALUES (?, ?, ?, ?)";

        try {
            $stmt = $this->bd->prepare($sql);
        } catch (mysqli_sql_exception $e) {
            throw new Exception(json_encode([
            "error" => "Le serveur n'a pas pu traiter votre demande. Veuillez réessayer ultérieurement.",
            "error_code" => "erreur code: 3_1",
            "token" => false
            ]));
            exit();
        }

        $stmt->bind_param("iiii", $confirmationID, $type, $id, $station_id);

        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            throw new Exception(json_encode([
            "error" => "Le serveur n'a pas pu traiter votre demande. Veuillez réessayer ultérieurement.",
            "error_code" => "erreur code: 3_2",
            "token" => false
            ]));
            exit();
        }

        return [
            'success' => true,
            'message' => "Réservation insérée avec succès : client_id = $id, confirmationID = $confirmationID"
        ];
    }

    public function removeSearch($id_search) {
   
        // Requête SQL préparée
        $sql = "DELETE FROM recherches WHERE id = ?";

        // Préparer la requête
        $stmt = $this->bd->prepare($sql);
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Erreur de préparation de la requête"
            ];
        }

        // Liaison des paramètres
        $stmt->bind_param("i", $id_search);

        // Exécuter la requête
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Suppression réussie : recherche ID = $id_search"
            ];
        } else {
            return [
                'success' => false,
                'message' => "Erreur lors de la suppression" 
            ];
        }
    }

    public function getSearch($id_client) {
    
        // Requête SQL préparée
        $sql = "SELECT * FROM recherches_vue WHERE client_id = ?";

        // Préparer la requête
        $stmt = $this->bd->prepare($sql);
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Erreur de préparation de la requête"
            ];
        }

        // Liaison des paramètres
        $stmt->bind_param("i", $id_client);

        // Exécuter la requête
        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => "Erreur lors de l'exécution de la requête"
            ];
        }

        // Récupérer les résultats
        $result = $stmt->get_result();
        $response = [];
        // Vérifier les lignes trouvées
        if ( $result->num_rows > 0) {
            
            while ($row = $result->fetch_assoc()) {
                $response[] = $row;
            }
        }
        return $response;
    }
 
    
    public function getOrder($id_client) {
    
        $responses = []; // Tableau pour stocker les résultats combinés

    // Première requête pour id_velo = 1
    $sql1 = "SELECT * FROM reservations_vue WHERE client_id = ? AND id_velo = 1;";
    $stmt1 = $this->bd->prepare($sql1);
    if (!$stmt1) {
        return [
            'success' => false,
            'message' => "Erreur de préparation de la première requête"
        ];
    }
    $stmt1->bind_param("i", $id_client);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $responses = array_merge($responses, $result1->fetch_all(MYSQLI_ASSOC));
    $stmt1->close();

    // Deuxième requête pour id_velo = 2
    $sql2 = "SELECT * FROM reservations_vue WHERE client_id = ? AND id_velo = 2;";
    $stmt2 = $this->bd->prepare($sql2);
    if (!$stmt2) {
        return [
            'success' => false,
            'message' => "Erreur de préparation de la deuxième requête"
        ];
    }
    $stmt2->bind_param("i", $id_client);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $responses = array_merge($responses, $result2->fetch_all(MYSQLI_ASSOC));
    $stmt2->close();

    // Trier les résultats combinés par `create_time` (plus petit au plus grand)
    usort($responses, function ($a, $b) {
        return strtotime($a['create_time']) <=> strtotime($b['create_time']);
    });

        return $responses;
    }

    public function getMail($email){
        $sql = "SELECT * FROM users WHERE email = ?";

        // Préparer la requête
        $stmt = $this->bd->prepare($sql);

        if (!$stmt) {
            die("Erreur de préparation");
        }

        // Liaison des paramètres (le "s" indique que c'est une chaîne de caractères)
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function addUser($username, $email, $password) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        // Préparer la requête
        $stmt = $this->bd->prepare($sql);
        if (!$stmt) {
            return false;
        }

        // Liaison des paramètres
        $stmt->bind_param("sss", $username, $email, $password);

        // Exécuter la requête
        return $stmt->execute();
    }


    public function escapeString($string) {
        return $this->bd->real_escape_string($string);
    }
  

    // Méthode pour fermer la connexion à la base de données
    public function close() {
        $this->bd->close();
    }
}
?>
