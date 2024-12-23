<?php
class Model {
    private $bd; // Attribut privé contenant l'objet mysqli
    private static $instance = null; // Attribut qui contiendra l'unique instance du modèle

    /**
     * Constructeur créant l'objet mysqli et l'affectant à $bd
     */
    private function __construct() {
        // Connexion à la base de données
        $this->bd = new mysqli('localhost', 'user_velib', 'saevelib', 'user');

        // Vérifiez la connexion
        if ($this->bd->connect_error) {
            die("Connection failed: " . $this->bd->connect_error);
        }

        $this->bd->set_charset('utf8'); // Définir l'encodage
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
    
        // Préparer la requête
        $stmt = $this->bd->prepare($sql);
    
        // Vérification si la préparation a réussi
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Erreur de préparation de la requête"
            ];
        }
    
        // Lier les paramètres
        if ($station_id === 0) {
            $stmt->bind_param("isi", $id, $search, $resultat); // "i" pour entier, "s" pour chaîne
        } else {
            $stmt->bind_param("iii", $id, $resultat, $station_id); // "i" pour entier, "s" pour chaîne
        }
    
        // Exécuter la requête
        if (!$stmt->execute()) {
            return [
                'success' => false,
                'message' => "Erreur lors de l'insertion"
            ];
        }
    
        // Retourner le succès
        return [
            'success' => true,
            'message' => "Recherche insérée avec succès : client_id = $id, recherche = $search"
        ];
        
        
    }

    public function postOrder($confirmationID, $type, $id, $station_id){

        $sql = "INSERT INTO reservations (confirmationID, id_velo, client_id, station_id) VALUES (?, ?, ?, ?)";

        // Préparer la requête
        $stmt = $this->bd->prepare($sql);
        if (!$stmt) {
            return [
                'success' => false,
                'message' => "Erreur de préparation de la requête"
            ];
        }

        // Liaison des paramètres
        $stmt->bind_param("iiii", $confirmationID, $type, $id, $station_id);

        // Exécuter la requête
        if ($stmt->execute()) {
            return [
                'success' => true,
                'message' => "Réservation insérée avec succès : client_id = $id, confirmationID = $confirmationID"
            ];
        } else {
            return [
                'success' => false,
                'message' => "Erreur lors de l'insertion : " . $stmt->error
            ];
        }
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
    
        // Requête SQL préparée
        $sql = "SELECT * FROM reservations_vue WHERE client_id = ?";

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
