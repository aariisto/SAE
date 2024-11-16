<?php
class Model {
    private $bd; // Attribut privé contenant l'objet mysqli
    private static $instance = null; // Attribut qui contiendra l'unique instance du modèle

    /**
     * Constructeur créant l'objet mysqli et l'affectant à $bd
     */
    private function __construct() {
        // Connexion à la base de données
        $this->bd = new mysqli('localhost', 'root', 'yannel', 'user');

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

    public function escapeString($string) {
        return $this->bd->real_escape_string($string);
    }
  

    // Méthode pour fermer la connexion à la base de données
    public function close() {
        $this->bd->close();
    }
}
?>
