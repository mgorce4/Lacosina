<?php

class ContactController{
    
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    //fonction permettant d'afficher le formulaire de contact
    function index(){
        require_once(__DIR__ . '/../Views/Contact/contact.php');
    }

    //fonction permettant d'enregistrer un nouveau contact
    function enregistrer(){
        //récupération des données de formulaire 
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $description = $_POST['description'];

        //préparation de la requête d'insertion dans la base de données
        $requete = $this->pdo->prepare('INSERT INTO contacts (nom, email, description, date_envoi) VALUES (:nom, :email, :description, NOW())');
        $requete->bindParam(':nom', $nom);
        $requete->bindParam(':email', $email);
        $requete->bindParam(':description', $description);

        //exécution de la requête
        $ajoutOk = $requete->execute();

        if ($ajoutOk){
            require_once(__DIR__ . '/../Views/Contact/enregistrer_contact.php');
        } else {
            echo 'Erreur lors de l\'enregistrement du contact.';
        }
    }
}
