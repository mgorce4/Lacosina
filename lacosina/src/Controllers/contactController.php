<?php

require_once(__DIR__ . '/../Models/Contact.php');

class ContactController{
    
    private $contactModel;

    public function __construct() {
        $this->contactModel = new Contact();
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

        //utilisation du modèle pour enregistrer
        $resultat = $this->contactModel->add($nom, $email, $description);

        if ($resultat){
            require_once(__DIR__ . '/../Views/Contact/enregistrer_contact.php');
        } else {
            echo 'Erreur lors de l\'enregistrement du contact.';
        }
    }
}
