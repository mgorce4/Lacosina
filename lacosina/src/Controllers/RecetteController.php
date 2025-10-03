<?php

class RecetteController{
    
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
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

        //préparation de la requête d'insertion dans la base de données
        $requete = $this->pdo->prepare('INSERT INTO recettes (titre, description, auteur, date_creation) VALUES (:titre, :description, :auteur, NOW())');
        $requete->bindParam(':titre', $titre);
        $requete->bindParam(':description', $description);
        $requete->bindParam(':auteur', $auteur);

        //exécution de la requête
        $ajoutOk = $requete->execute();

        if ($ajoutOk){
            require_once(__DIR__ . '/../Views/Recette/enregistrer.php');
        } else {
            echo 'Erreur lors de l\'enregistrement de la recette.';
        }
    }

    //fonction permettant de lister toutes les recettes
    function index(){
        //préparation de la requête de sélection dans la base de données
        $requete = $this->pdo->prepare("SELECT * FROM recettes ORDER BY date_creation DESC");

        //exécution de la requête et récupération des données
        $requete->execute();
        $recettes = $requete->fetchAll(PDO::FETCH_ASSOC);

        require_once(__DIR__ . '/../Views/Recette/liste.php');
    }
}

