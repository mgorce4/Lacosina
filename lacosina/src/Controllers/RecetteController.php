<?php

//connexion à la base de données
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Recette.php');

class RecetteController{
    private $recetteModel;

    public function __construct(){
        $this->recetteModel = new Recette();
    }

    //fonction permettant d'ajouter une nouvelle recette
    function ajouter(){
        require_once(__DIR__ . '/../Views/Recette/ajout.php');
    }

    //fonction permettant d'enregistrer une nouvelle recette
    function enregistrer(){
        //récupération des données de formulaire
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $auteur = $_POST['auteur'];
        $image = ''; // Image vide pour l'instant

        //utilisation du modèle pour enregistrer
        $resultat = $this->recetteModel->add($titre, $description, $auteur, $image);

        if ($resultat){
            require_once(__DIR__ . '/../Views/Recette/enregistrer.php');
        } else {
            echo 'Erreur lors de l\'enregistrement de la recette.';
        }
    }

    //fonction permettant de lister toutes les recettes
    function index(){
        //utilisation du modèle pour récupérer les recettes
        $recettes = $this->recetteModel->findAll();

        require_once(__DIR__ . '/../Views/Recette/liste.php');
    }
}

